<?php

namespace App\Http\Controllers;

use App\Role;
use App\Customer;
use App\Product;
use App\SalesDetail;
use App\SalesHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;
use Datatables;
use Illuminate\Support\Carbon;
use App\ProductStockLog;

class SalesController extends Controller
{
    public function index()
    {
        /* RBAC */
        if (!Role::authorize('sales.index')) {
            flash('Insufficient permission', 'warning');
            return redirect('dashboard');
        }

        $x = new HomeController;
        $data['menu'] = $x->getMenu();

        return view('sales.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        /* RBAC */
        if (!Role::authorize('sales.create')) {
            flash('Insufficient permission', 'warning');
            return redirect('dashboard');
        }

        $x = new HomeController;
        $data['menu'] = $x->getMenu();

        $data['customers'] = Customer::all();

        return view('sales.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /* RBAC */
        if (!Role::authorize('sales.create')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'customer_name' => 'required',
                'phone_number' => 'required',
                'address' => 'required',
                'type' => 'required',
                'data_product' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(array('status' => 0, 'message' => $validator->errors()->first()));
            } else {
                $customer_id = $request->input('customer_id');

                // get customer code
                $gcc = Customer::where('id', $customer_id)->first();
                $code = $gcc->code;
                // get total sales ordered by user;
                $gts = DB::select(DB::raw("SELECT COUNT(id) as total FROM sales_headers WHERE customer_id = " . $customer_id));
                $total_pembelian = $gts[0]->total;
                $sales_code = $code . '-' . ($total_pembelian + 1);

                $customer_name = $request->input('customer_name');
                $phone_number = $request->input('phone_number');
                $address = $request->input('address');
                $type = $request->input('type');
                $due_date = $request->input('due_date');
                $total = floatval(str_replace(',', '.', str_replace('.', '', $request->input('total'))));

                $status = 0;

                $pembayaran = '';
                if ($type == 'tunai') {
                    $pembayaran = $request->input('pembayaran');
                }

                // check stock
                $flag = true;
                $products = $request->input('data_product');
                if (count($products) == 0) {
                    return response()->json(array('status' => 0, 'message' => 'Please select min 1 product.'));
                }

                $cant = '';
                for ($i = 0; $i < count($products); $i++) {
                    $product_id = $products[$i]['id'];
                    $qty = (float)$products[$i]['qty'];
                    $check = Product::where('id', $product_id)->first();
                    if ($check->stock < $qty) {
                        $cant = $check->product_name;
                        $flag = false;
                        break;
                    }
                };

                if ($flag == true) {
                    $sales_header_id = SalesHeader::insertGetId([
                        'sales_code' => $sales_code,
                        'customer_id' => $customer_id,
                        'customer_name' => $customer_name,
                        'phone_number' => $phone_number,
                        'address' => $address,
                        'type' => $type,
                        'pembayaran' => $pembayaran,
                        'due_date' => $due_date,
                        'transaction_date' => now(),
                        'total' => $total,
                        'updated_by' => Auth::user()->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                        'status' => $status
                    ]);

                    for ($i = 0; $i < count($products); $i++) {
                        $product_id = $products[$i]['id'];
                        $product_name = $products[$i]['product_name'];
                        $qty = (float)$products[$i]['qty'];
                        $price = (float)$products[$i]['price'];
                        $totalPiece = (float)$products[$i]['total'];

                        SalesDetail::create([
                            'sales_header_id' => $sales_header_id,
                            'product_id' => $product_id,
                            'product_name' => $product_name,
                            'qty' => $qty,
                            'price' => $price,
                            'total' => $totalPiece
                        ]);

                        // update stock
                        $p_old = Product::where('id', $product_id)->first();
                        $stock_old = $p_old->stock;
                        $stock_new = $stock_old - $qty;

                        Product::findOrFail($product_id)->update([
                            'stock' => $stock_new
                        ]);

                        // Insert Log
                        ProductStockLog::create([
                            'product_id' => $product_id,
                            'description' => "penjualan no order " . $sales_code,
                            'from_qty' => $stock_old,
                            'to_qty' => $stock_new,
                            'updated_by' => Auth::user()->id,
                            'flag_admin' => 0
                        ]);
                    };
                    DB::commit();

                    return response()->json(array('status' => 1, 'message' => 'Successfully created sales.', 'intended_url' => '/sales'));
                } else {
                    return response()->json(array('status' => 0, 'message' => 'Stock ' . $cant . ' tidak memadai.'));
                }
            }
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(array('status' => 0, 'message' => 'Something went wrong.'));
        }
    }

    public function getSales($id)
    {
        /* RBAC */
        if (!Role::authorize('sales.index')) {
            flash('Insufficient permission', 'warning');
            return redirect('dashboard');
        }

        $x = new HomeController;
        $data['menu'] = $x->getMenu();

        $data['orderHeader'] = SalesHeader::where('id', $id)->first();
        $temp = date_create($data['orderHeader']->transaction_date);
        $date = date_format($temp, "d M Y");
        $data['transaction_date'] = $date;

        $due = $data['orderHeader']->due_date;
        $due_date_temp = date_add($temp, date_interval_create_from_date_string($due . " days"));
        $due_date = date_format($due_date_temp, "d M Y");
        $data['due_date'] = $due_date;

        $data['orderDetails'] = SalesDetail::where('sales_header_id', $id)->get();

        return view('sales.detail', $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        /* RBAC */
        if (!Role::authorize('sales.destroy')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        DB::beginTransaction();

        try {
            $sales_header = SalesHeader::findOrFail($id);

            $sales_code = $sales_header->sales_code;

            $sales_detail = SalesDetail::where('sales_header_id', $id)->get();
            for ($i = 0; $i < count($sales_detail); $i++) {
                $product_id = $sales_detail[$i]->product_id;
                $qty = $sales_detail[$i]->qty;

                // update stock
                $p_old = Product::where('id', $product_id)->first();
                $stock_old = $p_old->stock;
                $stock_new = $stock_old + $qty;

                Product::findOrFail($product_id)->update([
                    'stock' => $stock_new
                ]);

                // Insert Log
                ProductStockLog::create([
                    'product_id' => $product_id,
                    'description' => "penghapusan no order " . $sales_code,
                    'from_qty' => $stock_old,
                    'to_qty' => $stock_new,
                    'updated_by' => Auth::user()->id,
                    'flag_admin' => 0
                ]);
            }

            $sales_header->delete();

            DB::commit();

            return response()->json(array('status' => 1, 'message' => 'Successfully deleted sales.'));
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(array('status' => 0, 'message' => 'Something went wrong.'));
        }
    }

    /**
     * Return datatables data.
     *
     * @return Response
     */
    public function datatable()
    {
        /* RBAC */
        if (!Role::authorize('sales.index')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        $sales = DB::table('sales_headers')->select(['id', 'sales_code', 'customer_name', 'phone_number', 'address', 'type', 'total', 'transaction_date', 'due_date', 'status', 'pembayaran']);

        return Datatables::of($sales)
            ->addColumn('action', function ($sale) {
                $buttons = '<div class="text-center"><div class="dropdown"><button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-bars"></i></button><ul class="dropdown-menu">';

                /* Tambah Action */
                $buttons .= '<li><a href="sales/' . $sale->id . '/detail"><i class="fa fa-eye"></i>&nbsp; View</a></li>';
                $buttons .= '<li><a href="javascript:;" data-record-id="' . $sale->id . '" onclick="deleteSales($(this));"><i class="fa fa-trash"></i>&nbsp; Delete</a></li>';
                /* Selesai Action */

                $buttons .= '</ul></div></div>';

                return $buttons;
            })
            ->editColumn('type', function ($sale) {
                if ($sale->type == 'tunai') {
                    return '<span class="label label-success"> Tunai ' . $sale->pembayaran . ' </span>';
                } else if ($sale->type == 'kontrabon') {
                    return '<span class="label label-warning"> Kasbon - ' . $sale->due_date . ' </span>';
                } else if ($sale->type == 'kredit') {
                    return '<span class="label label-info"> Kredit </span>';
                }
            })
            ->editColumn('total', function ($sale) {
                return 'Rp ' . number_format($sale->total, 2, ',', '.');
            })
            ->editColumn('transaction_date', function ($sale) {
                return $sale->transaction_date ? with(new Carbon($sale->transaction_date))->format('d F Y H:i') : '';
            })
            ->editColumn('status', function ($sale) {
                if ($sale->status == 1) {
                    return '<span class="label label-success"> Complete </span>';
                } else {
                    return '<span class="label label-warning"> Not Complete </span>';
                }
            })
            ->editColumn('due_date', function ($sale) {
                if ($sale->type == 'tunai' || $sale->type == 'kredit') {
                    return '-';
                } else if ($sale->type == 'kontrabon') {
                    $temp = date_create($sale->transaction_date);
                    $date = date_format($temp, "d M Y");
                    $due = $sale->due_date;
                    $due_date_temp = date_add($temp, date_interval_create_from_date_string($due . " days"));
                    $due_date = date_format($due_date_temp, "d M Y");
                    return $due_date;
                }
            })
            ->rawColumns(['action', 'type', 'status'])
            ->make(true);
    }
}

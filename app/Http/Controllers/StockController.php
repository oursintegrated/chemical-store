<?php

namespace App\Http\Controllers;

use App\ActivityDetailStock;
use App\ActivityStock;
use App\Product;
use App\ProductIngredient;
use App\ProductStockLogAdmin;
use App\ProductStockLogUser;
use Illuminate\Http\Request;
use App\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use DataTables;
use Exception;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Date;
use SebastianBergmann\Environment\Console;
use Validator;

class StockController extends Controller
{
    public function index()
    {
        /* RBAC */
        if (!Role::authorize('stock.index')) {
            flash('Insufficient permission', 'warning');
            return redirect('dashboard');
        }

        $x = new HomeController;
        $data['menu'] = $x->getMenu();

        $ls = DB::select(DB::raw("SELECT COUNT(p.id) as count FROM products p WHERE min_stock > stock"));
        $data['totalLowStock'] = $ls[0]->count;

        return view('master.stock.index', $data);
    }

    public function manage()
    {
        /* RBAC */
        if (!Role::authorize('stock.manage')) {
            flash('Insufficient permission', 'warning');
            return redirect('dashboard');
        }

        $x = new HomeController;
        $data['menu'] = $x->getMenu();

        $ls = DB::select(DB::raw("SELECT COUNT(p.id) as count FROM products p WHERE min_stock > stock"));
        $data['totalLowStock'] = $ls[0]->count;

        return view('master.stock.manage', $data);
    }

    public function history()
    {
        /* RBAC */
        if (!Role::authorize('stock.history')) {
            flash('Insufficient permission', 'warning');
            return redirect('dashboard');
        }

        $x = new HomeController;
        $data['menu'] = $x->getMenu();

        $data['products'] = Product::all();

        if (Auth::user()->role_id == 1) {
            return view('master.stock.historyadmin', $data);
        } else {
            return view('master.stock.historyuser', $data);
        }
    }


    public function lowStockIndex()
    {
        /* RBAC */
        if (!Role::authorize('stock.index')) {
            flash('Insufficient permission', 'warning');
            return redirect('dashboard');
        }

        $x = new HomeController;
        $data['menu'] = $x->getMenu();

        $ls = DB::select(DB::raw("SELECT COUNT(p.id) as count FROM products p WHERE min_stock > stock"));
        $data['totalLowStock'] = $ls[0]->count;

        return view('master.stock.lowstock', $data);
    }

    public function activity()
    {
        $x = new HomeController;
        $data['menu'] = $x->getMenu();

        return view('master.stock.activity', $data);
    }

    public function opname()
    {
        $x = new HomeController;
        $data['menu'] = $x->getMenu();

        return view('master.stock.opname', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function lowStockAdjust(Request $request)
    {
        /* RBAC */
        if (!Role::authorize('stock.adjust')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'adjustment' => 'required',
            ]);

            $cant = [];

            if ($validator->fails()) {
                return response()->json(array('status' => 0, 'message' => $validator->errors()->first()));
            } else {
                $adjusment = $request->input('adjustment');
                for ($i = 0; $i < count($adjusment); $i++) {
                    $id = $adjusment[$i]['product_id'];
                    $adj_stock = $adjusment[$i]['adj_stock'];
                    $type = $adjusment[$i]['type'];
                    $product_name = $adjusment[$i]['product_name'];

                    if ($type == 'Raw Material' || $type == 'Packaging') {
                        $p = Product::where('id', $id)->first();
                        $old_stock = $p->stock;
                        $sum_stock = $old_stock + $adj_stock;
                        $adjust = Product::where('id', $id)->update([
                            'stock' => $sum_stock
                        ]);
                    } else if ($type == 'Recipe') {
                        // check stock
                        $flag = true;
                        $pi = DB::select(DB::raw("SELECT pi.*, p2.product_name, p2.stock FROM products p LEFT JOIN product_ingredients pi on p.id = pi.parent_id LEFT JOIN products p2 ON pi.product_id = p2.id WHERE p.id = " . $id));
                        for ($j = 0; $j < count($pi); $j++) {
                            $req_stock = $pi[$j]->req_stock;
                            $need_stock = $req_stock * $adj_stock;
                            $real_stock = $pi[$j]->stock;
                            if ($need_stock > $real_stock) {
                                $cant[] = ' ' . $product_name;
                                $flag = false;
                                break;
                            }
                        }

                        // bahan memadai
                        if ($flag == true) {
                            $p = Product::where('id', $id)->first();
                            $old_stock = $p->stock;
                            $sum_stock = $old_stock + $adj_stock;
                            $adjust = Product::where('id', $id)->update([
                                'stock' => $sum_stock
                            ]);
                            // pengurangan ingredient stock
                            for ($j = 0; $j < count($pi); $j++) {
                                $ingredient_id = $pi[$j]->product_id;
                                $req_stock = $pi[$j]->req_stock;
                                $need_stock = $req_stock * $adj_stock;
                                $real_stock = $pi[$j]->stock;
                                $stock_left = $real_stock - $need_stock;

                                Product::findOrFail($ingredient_id)->update([
                                    'stock' => $stock_left
                                ]);
                            }
                        }
                    }
                }

                DB::commit();

                return response()->json(array('status' => 1, 'message' => 'Successfully adjust stock.', 'intended_url' => '/data-master/stock', 'cant' => $cant));
            }
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(array('status' => 0, 'message' => 'Something went wrong.'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function manageUpdate(Request $request)
    {
        /* RBAC */
        if (!Role::authorize('stock.manage')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'dataSelected' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(array('status' => 0, 'message' => $validator->errors()->first()));
            } else {
                $type = $request->input("type");
                $selected = $request->input("dataSelected");

                $cant = [];
                if ($type == "insert stock") {
                    for ($i = 0; $i < count($selected); $i++) {
                        $product_id = $selected[$i]['id'];
                        $qty = $selected[$i]['qty'];
                        $type_product = $selected[$i]['type'];
                        $product_name = $selected[$i]['product_name'];

                        if ($qty != 0) {
                            if ($type_product == 'Raw Material' || $type_product == 'Packaging') {
                                // get stock lama
                                $p = Product::where('id', $product_id)->first();
                                $old_stock = $p->stock;
                                $new_stock = $old_stock + $qty;
                                $pu = Product::where('id', $product_id)->update([
                                    'stock' => $new_stock
                                ]);

                                // Insert Log
                                ProductStockLogAdmin::create([
                                    'product_id' => $product_id,
                                    'description' => $type,
                                    'from_qty' => $old_stock,
                                    'to_qty' => $new_stock,
                                    'total' => $new_stock - $old_stock,
                                    'updated_by' => Auth::user()->id,
                                ]);

                                ProductStockLogUser::create([
                                    'product_id' => $product_id,
                                    'description' => $type,
                                    'total' => $new_stock - $old_stock,
                                    'updated_by' => Auth::user()->id,
                                ]);

                                ActivityStock::create([
                                    'product_id' => $product_id,
                                    'updated_by' => Auth::user()->id,
                                    'from_qty' => $old_stock,
                                    'to_qty' => $new_stock,
                                    'qty' => $new_stock - $old_stock,
                                    'description' => $type
                                ]);
                            } else if ($type_product == 'Recipe') {
                                // check stock
                                $flag = true;
                                $pi = DB::select(DB::raw("SELECT pi.*, p2.product_name, p2.stock FROM products p LEFT JOIN product_ingredients pi on p.id = pi.parent_id LEFT JOIN products p2 ON pi.product_id = p2.id WHERE p.id = " . $product_id));
                                for ($j = 0; $j < count($pi); $j++) {
                                    $req_stock = $pi[$j]->req_stock;
                                    $need_stock = $req_stock * $qty;
                                    $real_stock = $pi[$j]->stock;
                                    if ($need_stock > $real_stock) {
                                        $cant[] = ' ' . $product_name;
                                        $flag = false;
                                        break;
                                    }
                                }

                                // bahan memadai
                                if ($flag == true) {
                                    $p = Product::where('id', $product_id)->first();
                                    $parent_stock = $p->parent_stock;
                                    $old_stock = $p->stock;
                                    $sum_stock = $old_stock + ($qty * $parent_stock);
                                    $adjust = Product::where('id', $product_id)->update([
                                        'stock' => $sum_stock
                                    ]);

                                    // Insert Log
                                    ProductStockLogAdmin::create([
                                        'product_id' => $product_id,
                                        'description' => $type,
                                        'from_qty' => $old_stock,
                                        'to_qty' => $sum_stock,
                                        'total' => $sum_stock - $old_stock,
                                        'updated_by' => Auth::user()->id,
                                    ]);

                                    ProductStockLogUser::create([
                                        'product_id' => $product_id,
                                        'description' => $type,
                                        'total' => $sum_stock - $old_stock,
                                        'updated_by' => Auth::user()->id,
                                    ]);

                                    $log_id = ActivityStock::insertGetId([
                                        'product_id' => $product_id,
                                        'updated_by' => Auth::user()->id,
                                        'from_qty' => $old_stock,
                                        'to_qty' => $sum_stock,
                                        'qty' => $sum_stock - $old_stock,
                                        'description' => $type,
                                        'created_at' => now(),
                                        'updated_at' => now()
                                    ]);

                                    // pengurangan ingredient stock
                                    for ($j = 0; $j < count($pi); $j++) {
                                        $ingredient_id = $pi[$j]->product_id;
                                        $req_stock = $pi[$j]->req_stock * $qty;
                                        $real_stock = $pi[$j]->stock;
                                        $stock_left = $real_stock - $req_stock;

                                        Product::findOrFail($ingredient_id)->update([
                                            'stock' => $stock_left
                                        ]);

                                        // Insert Log
                                        ProductStockLogAdmin::create([
                                            'product_id' => $ingredient_id,
                                            'description' => "digunakan untuk pembuatan " . $product_name,
                                            'from_qty' => $real_stock,
                                            'to_qty' => $stock_left,
                                            'total' => $stock_left - $real_stock,
                                            'updated_by' => Auth::user()->id,
                                            'flag_admin' => 1
                                        ]);

                                        ActivityDetailStock::create([
                                            'log_id' => $log_id,
                                            'product_id' => $ingredient_id,
                                            'qty' => $stock_left - $real_stock
                                        ]);

                                        // ProductStockLogUser::create([
                                        //     'product_id' => $ingredient_id,
                                        //     'description' => "-",
                                        //     'total' => $stock_left - $real_stock,
                                        //     'updated_by' => Auth::user()->id,
                                        //     'flag_admin' => 1
                                        // ]);
                                    }
                                }
                            }
                        }
                    }
                } else if ($type == "retur") {
                    for ($i = 0; $i < count($selected); $i++) {
                        $product_id = $selected[$i]['id'];
                        $qty = $selected[$i]['qty'];
                        $type_product = $selected[$i]['type'];
                        $product_name = $selected[$i]['product_name'];

                        if ($qty != 0) {
                            // get stock lama
                            $p = Product::where('id', $product_id)->first();
                            $old_stock = $p->stock;
                            $new_stock = $old_stock + $qty;
                            $pu = Product::where('id', $product_id)->update([
                                'stock' => $new_stock
                            ]);

                            // Insert Log
                            ProductStockLogAdmin::create([
                                'product_id' => $product_id,
                                'description' => $type,
                                'from_qty' => $old_stock,
                                'to_qty' => $new_stock,
                                'total' => $new_stock - $old_stock,
                                'updated_by' => Auth::user()->id,
                            ]);

                            ProductStockLogUser::create([
                                'product_id' => $product_id,
                                'description' => $type,
                                'total' => $new_stock - $old_stock,
                                'updated_by' => Auth::user()->id,
                            ]);

                            ActivityStock::create([
                                'product_id' => $product_id,
                                'updated_by' => Auth::user()->id,
                                'from_qty' => $old_stock,
                                'to_qty' => $new_stock,
                                'qty' => $new_stock - $old_stock,
                                'description' => $type
                            ]);
                        }
                    }
                } else if ($type == "product processing") {
                    for ($i = 0; $i < count($selected); $i++) {
                        $product_id = $selected[$i]['id'];
                        $qty = $selected[$i]['qty'];
                        $type_product = $selected[$i]['type'];
                        $product_name = $selected[$i]['product_name'];

                        if ($qty != 0) {
                            // get stock lama
                            $p = Product::where('id', $product_id)->first();
                            $old_stock = $p->stock;
                            $new_stock = $old_stock - $qty;
                            $pu = Product::where('id', $product_id)->update([
                                'stock' => $new_stock
                            ]);

                            // Insert Log
                            ProductStockLogAdmin::create([
                                'product_id' => $product_id,
                                'description' => $type,
                                'from_qty' => $old_stock,
                                'to_qty' => $new_stock,
                                'total' => $new_stock - $old_stock,
                                'updated_by' => Auth::user()->id,
                            ]);

                            ProductStockLogUser::create([
                                'product_id' => $product_id,
                                'description' => $type,
                                'total' => $new_stock - $old_stock,
                                'updated_by' => Auth::user()->id,
                            ]);

                            ActivityStock::create([
                                'product_id' => $product_id,
                                'updated_by' => Auth::user()->id,
                                'from_qty' => $old_stock,
                                'to_qty' => $new_stock,
                                'qty' => $new_stock - $old_stock,
                                'description' => $type
                            ]);
                        }
                    }
                } else if ($type == 'stock opname') {
                    for ($i = 0; $i < count($selected); $i++) {
                        $product_id = $selected[$i]['id'];
                        $qty = $selected[$i]['qty'];
                        $type_product = $selected[$i]['type'];
                        $product_name = $selected[$i]['product_name'];

                        // get stock lama
                        $p = Product::where('id', $product_id)->first();
                        $old_stock = $p->stock;
                        $new_stock = $qty;
                        $pu = Product::where('id', $product_id)->update([
                            'stock' => $new_stock
                        ]);

                        // Insert Log
                        ProductStockLogAdmin::create([
                            'product_id' => $product_id,
                            'description' => $type,
                            'from_qty' => $old_stock,
                            'to_qty' => $new_stock,
                            'total' => $new_stock - $old_stock,
                            'updated_by' => Auth::user()->id,
                        ]);

                        ProductStockLogUser::create([
                            'product_id' => $product_id,
                            'description' => 'opname',
                            'total' => $new_stock - $old_stock,
                            'updated_by' => Auth::user()->id,
                        ]);
                    }
                }

                DB::commit();

                return response()->json(array('status' => 1, 'message' => 'Successfully updated stock.', 'intended_url' => '/data-master/stock', 'cant' => $cant));
            }
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
        if (!Role::authorize('stock.index')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        $products = DB::table('products')->select(['id', 'code', 'product_name', 'stock', 'min_stock', 'description', 'created_at', 'updated_at', 'type']);

        return Datatables::of($products)
            ->addColumn('action', function ($product) {
                $buttons = '<div class="text-center"><div class="dropdown"><button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-bars"></i></button><ul class="dropdown-menu">';

                /* Tambah Action */
                $buttons .= '<li><a href="product/' . $product->id . '/edit"><i class="fa fa-pencil-square-o"></i>&nbsp; Edit</a></li>';
                $buttons .= '<li><a href="javascript:;" data-record-id="' . $product->id . '" onclick="deleteProduct($(this));"><i class="fa fa-trash"></i>&nbsp; Delete</a></li>';
                /* Selesai Action */

                $buttons .= '</ul></div></div>';

                return $buttons;
            })
            ->editColumn('type', function ($product) {
                if ($product->type == 'raw') {
                    return 'Raw Material';
                } else if ($product->type == 'packaging') {
                    return 'Packaging';
                } else {
                    return 'Recipe';
                }
            })
            ->editColumn('created_at', function ($product) {
                return $product->created_at ? with(new Carbon($product->created_at))->format('d F Y H:i') : '';
            })
            ->editColumn('updated_at', function ($product) {
                return $product->updated_at ? with(new Carbon($product->updated_at))->format('d F Y H:i') : '';
            })
            ->editColumn('stock', function ($product) {
                return number_format($product->stock, 2, '.', '');
            })
            ->editColumn('min_stock', function ($product) {
                return number_format($product->min_stock, 2, '.', '');
            })
            ->make(true);
    }

    /**
     * Return datatables data.
     *
     * @return Response
     */
    public function datatableLowStock()
    {
        /* RBAC */
        if (!Role::authorize('stock.index')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        $products = DB::select(DB::raw("SELECT * FROM products where min_stock > stock"));

        return Datatables::of($products)
            // ->addColumn('add_stock', function ($product) {
            //     $buttons = '<input type="number" step="0.1" class="form-control" min="0.0" value="0">';
            //     return $buttons;
            // })
            ->editColumn('type', function ($product) {
                if ($product->type == 'raw') {
                    return 'Raw Material';
                } else if ($product->type == 'packaging') {
                    return 'Packaging';
                } else {
                    return 'Recipe';
                }
            })
            ->editColumn('stock', function ($product) {
                return number_format($product->stock, 2, '.', '');
            })
            ->editColumn('min_stock', function ($product) {
                return number_format($product->min_stock, 2, '.', '');
            })
            ->editColumn('created_at', function ($product) {
                return $product->created_at ? with(new Carbon($product->created_at))->format('d F Y H:i') : '';
            })
            ->editColumn('updated_at', function ($product) {
                return $product->updated_at ? with(new Carbon($product->updated_at))->format('d F Y H:i') : '';
            })
            // ->rawColumns(['add_stock'])
            ->make(true);
    }

    /**
     * Return datatables data.
     *
     * @return Response
     */
    public function datatableHistoryAdmin(Request $request)
    {
        /* RBAC */
        if (!Role::authorize('stock.history')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $id = $request->input('id');

        if ($startDate == '') {
            $startDate = date('Y-m-d') . " 00:00:00";
        } else {
            $startDate = $startDate . " 00:00:00";
        }
        if ($endDate == '') {
            $endDate = date('Y-m-d') . " 23:59:59";
        } else {
            $endDate = $endDate . " 23:59:59";
        }

        $q = '';
        if ($id != 'All') {
            $q = ' AND product_id = ' . $id;
        }

        $histories = DB::select(DB::raw("SELECT p.product_name, psl.* FROM product_stock_log_admin psl LEFT JOIN products p ON psl.product_id = p.id WHERE psl.updated_at BETWEEN '" . $startDate . "' AND '" . $endDate . "'" . $q . " ORDER BY updated_at ASC"));

        return Datatables::of($histories)
            ->editColumn('updated_at', function ($history) {
                return $history->updated_at ? with(new Carbon($history->updated_at))->format('d F Y H:i') : '';
            })
            ->editColumn('from_qty', function ($history) {
                return number_format($history->from_qty, 2, '.', '');
            })
            ->editColumn('to_qty', function ($history) {
                return number_format($history->to_qty, 2, '.', '');
            })
            ->make(true);
    }

    /**
     * Return datatables data.
     *
     * @return Response
     */
    public function datatableHistoryUser(Request $request)
    {
        /* RBAC */
        if (!Role::authorize('stock.history')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $id = $request->input('id');

        if ($startDate == '') {
            $startDate = date('Y-m-d') . " 00:00:00";
        } else {
            $startDate = $startDate . " 00:00:00";
        }
        if ($endDate == '') {
            $endDate = date('Y-m-d') . " 23:59:59";
        } else {
            $endDate = $endDate . " 23:59:59";
        }

        $q = '';
        if ($id != 'All') {
            $q = ' AND product_id = ' . $id;
        }

        $histories = DB::select(DB::raw("SELECT p.product_name, psl.* FROM product_stock_log_user psl LEFT JOIN products p ON psl.product_id = p.id WHERE psl.updated_at BETWEEN '" . $startDate . "' AND '" . $endDate . "'" . $q . " ORDER BY updated_at ASC"));

        return Datatables::of($histories)
            ->editColumn('updated_at', function ($history) {
                return $history->updated_at ? with(new Carbon($history->updated_at))->format('d F Y H:i') : '';
            })
            ->make(true);
    }

    /**
     * Return datatables data.
     *
     * @return Response
     */
    public function datatableRawProduct()
    {
        /* RBAC */
        if (!Role::authorize('stock.index')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        $products = DB::table('products')->select(['id', 'code', 'product_name', 'stock', 'description', 'created_at', 'updated_at', 'type', 'parent_stock'])->where('type', '!=', 'delivery');

        return Datatables::of($products)
            ->addColumn('action', function ($product) {
                $buttons = '<div class="text-center"><div class="dropdown"><button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-bars"></i></button><ul class="dropdown-menu">';

                /* Tambah Action */
                $buttons .= '<li><a href="product/' . $product->id . '/edit"><i class="fa fa-pencil-square-o"></i>&nbsp; Edit</a></li>';
                $buttons .= '<li><a href="javascript:;" data-record-id="' . $product->id . '" onclick="deleteProduct($(this));"><i class="fa fa-trash"></i>&nbsp; Delete</a></li>';
                /* Selesai Action */

                $buttons .= '</ul></div></div>';

                return $buttons;
            })
            ->editColumn('type', function ($product) {
                if ($product->type == 'raw') {
                    return 'Raw Material';
                } else if ($product->type == 'packaging') {
                    return 'Packaging';
                } else if ($product->type == 'delivery') {
                    return 'Delivery';
                } else {
                    return 'Recipe';
                }
            })
            ->editColumn('stock', function ($product) {
                return number_format($product->stock, 2, '.', '');
            })
            ->editColumn('created_at', function ($product) {
                return $product->created_at ? with(new Carbon($product->created_at))->format('d F Y H:i') : '';
            })
            ->editColumn('updated_at', function ($product) {
                return $product->updated_at ? with(new Carbon($product->updated_at))->format('d F Y H:i') : '';
            })
            ->make(true);
    }

    /**
     * Return datatables data.
     *
     * @return Response
     */
    public function datatableActivity()
    {
        $logs = DB::select(DB::raw('SELECT a.*, p.product_name FROM activity_stocks a LEFT JOIN products p on a.product_id = p.id WHERE a.updated_by =' . Auth::user()->id . ' AND DATE(a.created_at) = CURDATE()'));

        return Datatables::of($logs)
            ->addColumn('action', function ($log) {
                $buttons = '<div class="text-center"><div class="dropdown"><button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-bars"></i></button><ul class="dropdown-menu">';

                /* Tambah Action */
                $buttons .= '<li><a href="javascript:;" data-record-id="' . $log->id . '" onclick="rollbackLog($(this));"><i class="fa fa-refresh"></i>&nbsp; Rollback</a></li>';
                /* Selesai Action */

                $buttons .= '</ul></div></div>';

                return $buttons;
            })
            ->editColumn('created_at', function ($log) {
                return $log->created_at ? with(new Carbon($log->created_at))->format('d F Y H:i') : '';
            })
            ->editColumn('updated_at', function ($log) {
                return $log->updated_at ? with(new Carbon($log->updated_at))->format('d F Y H:i') : '';
            })
            ->make(true);
    }

    public function rollback($id)
    {
        try {
            DB::beginTransaction();

            $logHeader = ActivityStock::where('id', $id)->first();
            $product_id = $logHeader->product_id;
            $qty_header = $logHeader->qty;

            $x = Product::where('id', $product_id)->first();
            $old_stock = $x->stock;

            if ($qty_header > 0) {
                $new_stock = $old_stock - $qty_header;
            } else if ($qty_header < 0) {
                $new_stock = $old_stock + abs($qty_header);
            }

            Product::where('id', $product_id)->update([
                'stock' => $new_stock
            ]);

            // Insert Log
            ProductStockLogAdmin::create([
                'product_id' => $product_id,
                'description' => 'rollback',
                'from_qty' => $old_stock,
                'to_qty' => $new_stock,
                'total' => $new_stock - $old_stock,
                'updated_by' => Auth::user()->id,
            ]);

            ProductStockLogUser::create([
                'product_id' => $product_id,
                'description' => 'rollback',
                'total' => $new_stock - $old_stock,
                'updated_by' => Auth::user()->id,
            ]);

            $logDetail = ActivityDetailStock::where('log_id', $id)->get();
            for ($i = 0; $i < count($logDetail); $i++) {
                $product_id = $logDetail[$i]->product_id;

                $qty_detail = $logDetail[$i]->qty;

                $x = Product::where('id', $product_id)->first();
                $old_stock = $x->stock;

                if ($qty_detail > 0) {
                    $new_stock = $old_stock - $qty_detail;
                } else if ($qty_detail < 0) {
                    $new_stock = $old_stock + abs($qty_detail);
                }

                Product::where('id', $product_id)->update([
                    'stock' => $new_stock
                ]);

                // Insert Log
                ProductStockLogAdmin::create([
                    'product_id' => $product_id,
                    'description' => 'rollback',
                    'from_qty' => $old_stock,
                    'to_qty' => $new_stock,
                    'total' => $new_stock - $old_stock,
                    'updated_by' => Auth::user()->id,
                ]);

                ProductStockLogUser::create([
                    'product_id' => $product_id,
                    'description' => 'rollback',
                    'total' => $new_stock - $old_stock,
                    'updated_by' => Auth::user()->id,
                ]);
            }

            // DELETE
            ActivityStock::where('id', $id)->delete();
            ActivityDetailStock::where('log_id', $id)->delete();

            DB::commit();

            return response()->json(array('status' => 1, 'message' => 'Rollback Success.'));
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(array('status' => 0, 'message' => $e));
        }
    }

    /**
     * Return datatables data.
     *
     * @return Response
     */
    public function datatableOpname()
    {
        /* RBAC */
        if (!Role::authorize('stock.index')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        $products = DB::table('products');

        return Datatables::of($products)
            ->addColumn('no', function () {
                return '-';
            })
            ->editColumn('stock', function ($product) {
                return number_format($product->stock, 2, '.', '');
            })
            ->addColumn('keterangan', function () {
                return '-';
            })
            ->make(true);
    }
}

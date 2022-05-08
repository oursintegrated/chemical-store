<?php

namespace App\Http\Controllers;

use App\Product;
use App\ProductIngredient;
use Illuminate\Http\Request;
use App\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use DataTables;
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

                        if ($type_product == 'Raw Material' || $type_product == 'Packaging') {
                            // get stock lama
                            $p = Product::where('id', $product_id)->first();
                            $old_stock = $p->stock;
                            $new_stock = $old_stock + $qty;
                            $pu = Product::where('id', $product_id)->update([
                                'stock' => $new_stock
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
                                $old_stock = $p->stock;
                                $sum_stock = $old_stock + $qty;
                                $adjust = Product::where('id', $product_id)->update([
                                    'stock' => $sum_stock
                                ]);
                                // pengurangan ingredient stock
                                for ($j = 0; $j < count($pi); $j++) {
                                    $ingredient_id = $pi[$j]->product_id;
                                    $req_stock = $pi[$j]->req_stock;
                                    $need_stock = $req_stock * $qty;
                                    $real_stock = $pi[$j]->stock;
                                    $stock_left = $real_stock - $need_stock;

                                    Product::findOrFail($ingredient_id)->update([
                                        'stock' => $stock_left
                                    ]);
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

                        // get stock lama
                        $p = Product::where('id', $product_id)->first();
                        $old_stock = $p->stock;
                        $new_stock = $old_stock + $qty;
                        $pu = Product::where('id', $product_id)->update([
                            'stock' => $new_stock
                        ]);
                    }
                } else if ($type == "product processing") {
                    for ($i = 0; $i < count($selected); $i++) {
                        $product_id = $selected[$i]['id'];
                        $qty = $selected[$i]['qty'];
                        $type_product = $selected[$i]['type'];
                        $product_name = $selected[$i]['product_name'];

                        // get stock lama
                        $p = Product::where('id', $product_id)->first();
                        $old_stock = $p->stock;
                        $new_stock = $old_stock - $qty;
                        $pu = Product::where('id', $product_id)->update([
                            'stock' => $new_stock
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
            ->editColumn('created_at', function ($product) {
                return $product->created_at ? with(new Carbon($product->created_at))->format('d F Y H:i') : '';
            })
            ->editColumn('updated_at', function ($product) {
                return $product->updated_at ? with(new Carbon($product->updated_at))->format('d F Y H:i') : '';
            })
            // ->rawColumns(['add_stock'])
            ->make(true);
    }
}
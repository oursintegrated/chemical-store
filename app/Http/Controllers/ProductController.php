<?php

namespace App\Http\Controllers;

use App\Product;
use App\ProductIngredient;
use App\ProductStockLogAdmin;
use Illuminate\Http\Request;
use App\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Validator;
use Illuminate\Support\Carbon;

class ProductController extends Controller
{
    public function index()
    {
        /* RBAC */
        if (!Role::authorize('product.index')) {
            flash('Insufficient permission', 'warning');
            return redirect('dashboard');
        }

        $x = new HomeController;
        $data['menu'] = $x->getMenu();

        return view('master.product.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        /* RBAC */
        if (!Role::authorize('product.create')) {
            flash('Insufficient permission', 'warning');
            return redirect('dashboard');
        }

        if (Role::authorize('product.recipe')) {
            $data['flag_recipe'] =  1;
        }

        $x = new HomeController;
        $data['menu'] = $x->getMenu();

        $data['products'] = Product::all();

        return view('master.product.create', $data);
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
        if (!Role::authorize('product.create')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'stock' => 'required',
                'description' => 'required',
                'type' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(array('status' => 0, 'message' => $validator->errors()->first()));
            } else {
                $name = $request->input('name');
                $stock = $request->input('stock');
                $min_stock = $request->input('min_stock');
                $description = $request->input('description');
                $type = $request->input('type');

                // generate code
                $x = Product::latest()->first();
                if (isset($x->id)) {
                    $id = str_pad($x->id + 1, 8, "0", STR_PAD_LEFT);
                } else {
                    $id = '00000001';
                }
                $digit = strtoupper(substr($name, 0, 3));

                if ($type == 'raw' || $type == 'packaging') {
                    $product = Product::create([
                        'code' => $digit . $id,
                        'product_name' => $name,
                        'type' => $type,
                        'stock' => $stock,
                        'min_stock' => $min_stock,
                        'description' => $description,
                        'updated_by' => Auth::user()->id,
                    ]);
                }
                // else if ($type == 'recipe') {
                //     if (!Role::authorize('product.recipe')) {
                //         return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
                //     }

                //     // check stock
                //     $dataIngredient = $request->input('dataIngredients');
                //     for ($i = 0; $i < count($dataIngredient); $i++) {
                //         $ingredient_id = $dataIngredient[$i]['id'];
                //         $ingredient_name = $dataIngredient[$i]['product_name'];
                //         $req_stock = $dataIngredient[$i]['req_stock'];

                //         $check = Product::where('id', $ingredient_id)->first();
                //         $available_stock = $check->stock;
                //         if ($req_stock > $available_stock) {
                //             return response()->json(array('status' => 0, 'message' => $ingredient_name . ' stock is not enough.'));
                //         }
                //         if ($req_stock == 0) {
                //             return response()->json(array('status' => 0, 'message' => $ingredient_name . ' stock required in ingredient is 0.'));
                //         }
                //     }

                //     // insert Product
                //     $product_id = Product::insertGetId([
                //         'code' => $digit . $id,
                //         'product_name' => $name,
                //         'type' => $type,
                //         'stock' => $stock,
                //         'min_stock' => $min_stock,
                //         'description' => $description,
                //         'updated_by' => Auth::user()->id,
                //         'created_at' => now(),
                //         'updated_at' => now()
                //     ]);

                //     // insert Ingredient and substract stock
                //     for ($j = 0; $j < count($dataIngredient); $j++) {
                //         $ingredient_id = $dataIngredient[$j]['id'];
                //         $req_stock = $dataIngredient[$j]['req_stock'];

                //         ProductIngredient::create([
                //             'parent_id' => $product_id,
                //             'product_id' => $ingredient_id,
                //             'req_stock' => $req_stock
                //         ]);

                //         $x = Product::where('id', $ingredient_id)->first();
                //         $req_stock = $dataIngredient[$j]['req_stock'];
                //         $estimate_req = $req_stock * $stock;
                //         $available_stock = $x->stock;
                //         $stock_left = $available_stock - $estimate_req;

                //         Product::findOrFail($ingredient_id)->update([
                //             'stock' => $stock_left
                //         ]);

                //         // Insert Log
                //         ProductStockLogAdmin::create([
                //             'product_id' => $ingredient_id,
                //             'description' => "digunakan untuk pembuatan " . $name,
                //             'from_qty' => $available_stock,
                //             'to_qty' => $stock_left,
                //             'total' => $available_stock -
                //             'updated_by' => Auth::user()->id,
                //             'flag_admin' => 1
                //         ]);
                //     }
                // }

                DB::commit();

                return response()->json(array('status' => 1, 'message' => 'Successfully created product.', 'intended_url' => '/data-master/product'));
            }
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(array('status' => 0, 'message' => 'Something went wrong.'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        /* RBAC */
        if (!Role::authorize('product.edit')) {
            flash('Insufficient permission', 'warning');
            return redirect('dashboard');
        }

        try {
            $data['product'] = Product::findOrFail($id);

            $x = new HomeController;
            $data['menu'] = $x->getMenu();

            return view('master.product.edit', $data);
        } catch (Exception $e) {
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        /* RBAC */
        if (!Role::authorize('product.edit')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                // 'stock' => 'required',
                'description' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(array('status' => 0, 'message' => $validator->errors()->first()));
            } else {
                $name = $request->input('name');
                $min_stock = $request->input('min_stock');
                $description = $request->input('description');

                Product::findOrFail($id)->update([
                    'product_name' => $name,
                    'min_stock' => $min_stock,
                    'description' => $description,
                    'updated_by' => Auth::user()->id
                ]);

                DB::commit();

                return response()->json(array('status' => 1, 'message' => 'Successfully updated product.', 'intended_url' => '/data-master/product'));
            }
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(array('status' => 0, 'message' => 'Something went wrong.'));
        }
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
        if (!Role::authorize('product.destroy')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        DB::beginTransaction();

        try {
            $post = Product::findOrFail($id);

            $pi = ProductIngredient::where('product_id', $id)->count();
            if ($pi == 0) {
                $post->delete();
            } else {
                return response()->json(array('status' => 0, 'message' => "Can't delete product."));
            }

            DB::commit();

            return response()->json(array('status' => 1, 'message' => 'Successfully deleted product.'));
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
        if (!Role::authorize('product.index')) {
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
            ->editColumn('stock', function ($product) {
                return number_format($product->stock, 2, ',', '.');
            })
            ->editColumn('min_stock', function ($product) {
                return number_format($product->min_stock, 2, ',', '.');
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
    public function datatableRawProduct()
    {
        /* RBAC */
        if (!Role::authorize('product.index')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        $products = DB::table('products')->select(['id', 'code', 'product_name', 'stock', 'description', 'created_at', 'updated_at', 'type']);

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
            ->editColumn('stock', function ($product) {
                return number_format($product->stock, 2, ',', '.');
            })
            ->editColumn('created_at', function ($product) {
                return $product->created_at ? with(new Carbon($product->created_at))->format('d F Y H:i') : '';
            })
            ->editColumn('updated_at', function ($product) {
                return $product->updated_at ? with(new Carbon($product->updated_at))->format('d F Y H:i') : '';
            })
            ->make(true);
    }
}

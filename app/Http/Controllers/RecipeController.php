<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;
use App\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Validator;
use Illuminate\Support\Carbon;
use App\ProductIngredient;

class RecipeController extends Controller
{
    public function index()
    {
        /* RBAC */
        if (!Role::authorize('recipe.index')) {
            flash('Insufficient permission', 'warning');
            return redirect('dashboard');
        }

        $x = new HomeController;
        $data['menu'] = $x->getMenu();

        return view('master.recipe.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        /* RBAC */
        if (!Role::authorize('recipe.create')) {
            flash('Insufficient permission', 'warning');
            return redirect('dashboard');
        }

        $x = new HomeController;
        $data['menu'] = $x->getMenu();

        $data['recipes'] = Product::where('type', '=', 'recipe')->get();

        return view('master.recipe.create', $data);
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
        if (!Role::authorize('recipe.create')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'description' => 'required',
                'parent_stock' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(array('status' => 0, 'message' => $validator->errors()->first()));
            } else {
                $name = $request->input('name');
                $description = $request->input('description');
                $type = 'recipe';
                $stock = 0;
                $parent_stock = $request->input('parent_stock');
                $min_stock = 0;

                // generate code
                $x = Product::latest()->first();
                if (isset($x->id)) {
                    $id = str_pad($x->id + 1, 8, "0", STR_PAD_LEFT);
                } else {
                    $id = '00000001';
                }
                $digit = strtoupper(substr($name, 0, 3));


                if (!Role::authorize('recipe.create')) {
                    return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
                }

                $dataIngredient = $request->input('dataIngredients');

                // insert Product
                $product_id = Product::insertGetId([
                    'code' => $digit . $id,
                    'product_name' => $name,
                    'type' => $type,
                    'stock' => $stock,
                    'min_stock' => $min_stock,
                    'parent_stock' => $parent_stock,
                    'description' => $description,
                    'updated_by' => Auth::user()->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // insert Ingredient
                for ($j = 0; $j < count($dataIngredient); $j++) {
                    $ingredient_id = $dataIngredient[$j]['id'];
                    $req_stock = $dataIngredient[$j]['req_stock'];

                    ProductIngredient::create([
                        'parent_id' => $product_id,
                        'product_id' => $ingredient_id,
                        'req_stock' => $req_stock
                    ]);
                }

                DB::commit();

                return response()->json(array('status' => 1, 'message' => 'Successfully created recipe.', 'intended_url' => '/data-master/recipe'));
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
        if (!Role::authorize('recipe.edit')) {
            flash('Insufficient permission', 'warning');
            return redirect('dashboard');
        }

        try {
            $data['recipe'] = Product::findOrFail($id);

            $x = new HomeController;
            $data['menu'] = $x->getMenu();

            return view('master.recipe.edit', $data);
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
        if (!Role::authorize('recipe.edit')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'description' => 'required',
                'dataIngredients' => 'required',
                'parent_stock' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(array('status' => 0, 'message' => $validator->errors()->first()));
            } else {
                $description = $request->input('description');
                $parent_stock = $request->input('parent_stock');

                Product::findOrFail($id)->update([
                    'description' => $description,
                    'parent_stock' => $parent_stock,
                    'updated_by' => Auth::user()->id
                ]);

                $dataIngredient = $request->input('dataIngredients');
                ProductIngredient::where('parent_id', $id)->delete();
                // insert Ingredient and substract stock
                for ($j = 0; $j < count($dataIngredient); $j++) {
                    $ingredient_id = $dataIngredient[$j]['id'];
                    $req_stock = $dataIngredient[$j]['req_stock'];

                    ProductIngredient::create([
                        'parent_id' => $id,
                        'product_id' => $ingredient_id,
                        'req_stock' => $req_stock
                    ]);
                }

                DB::commit();

                return response()->json(array('status' => 1, 'message' => 'Successfully updated recipe.', 'intended_url' => '/data-master/recipe'));
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
        if (!Role::authorize('recipe.destroy')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        DB::beginTransaction();

        try {
            $post = Product::findOrFail($id);

            $pi = ProductIngredient::where('product_id', $id)->count();
            if ($pi == 0) {
                // check type
                if ($post->type == 'recipe') {
                    if ($post->stock == 0) {
                        $post->delete();
                    } else {
                        return response()->json(array('status' => 0, 'message' => "Can't delete this product."));
                    }
                } else {
                    $post->delete();
                }
            } else {
                return response()->json(array('status' => 0, 'message' => "Can't delete product."));
            }

            DB::commit();

            return response()->json(array('status' => 1, 'message' => 'Successfully deleted recipe.'));
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
        if (!Role::authorize('recipe.index')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        $recipes = DB::select(DB::raw(
            'SELECT p.*, (SELECT GROUP_CONCAT(DISTINCT (p2.product_name) SEPARATOR  ", ") FROM product_ingredients pi LEFT JOIN products p2 ON pi.product_id = p2.id WHERE p.id = pi.parent_id) as ingredients FROM products p WHERE p.type = "recipe"'
        ));

        return Datatables::of($recipes)
            ->addColumn('action', function ($recipe) {
                $buttons = '<div class="text-center"><div class="dropdown"><button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-bars"></i></button><ul class="dropdown-menu">';

                /* Tambah Action */
                $buttons .= '<li><a href="recipe/' . $recipe->id . '/edit"><i class="fa fa-pencil-square-o"></i>&nbsp; Edit</a></li>';
                $buttons .= '<li><a href="javascript:;" data-record-id="' . $recipe->id . '" onclick="deleteRecipe($(this));"><i class="fa fa-trash"></i>&nbsp; Delete</a></li>';
                /* Selesai Action */

                $buttons .= '</ul></div></div>';

                return $buttons;
            })
            ->editColumn('created_at', function ($recipe) {
                return $recipe->created_at ? with(new Carbon($recipe->created_at))->format('d F Y H:i') : '';
            })
            ->editColumn('updated_at', function ($recipe) {
                return $recipe->updated_at ? with(new Carbon($recipe->updated_at))->format('d F Y H:i') : '';
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
        if (!Role::authorize('recipe.index')) {
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
}

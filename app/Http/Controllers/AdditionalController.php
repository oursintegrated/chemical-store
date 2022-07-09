<?php

namespace App\Http\Controllers;

use App\Address;
use App\Product;
use App\ProductIngredient;
use App\Telephone;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;
use App\ProductStockLogAdmin;
use App\ProductStockLogUser;
use Illuminate\Support\Facades\Auth;

class AdditionalController extends Controller
{
    public function getCustomerInfo(Request $request)
    {
        try {
            $customerId = $request->input('customerId');
            $telephones = Telephone::where('customer_id', $customerId)->get();
            $addresses = Address::where('customer_id', $customerId)->get();
            return response()->json(array('status' => 1, 'telephones' => $telephones, 'addresses' => $addresses));
        } catch (Exception $e) {
            return response()->json(array('status' => 0, 'message' => 'Something went wrong.'));
        }
    }

    public function salesTabledit(Request $request)
    {
        return response()->json($request->all());
    }

    public function productTabledit(Request $request)
    {
        return response()->json($request->all());
    }

    public function stockTabledit(Request $request)
    {
        try {
            DB::beginTransaction();

            $id = $request->input('id');
            $new_stock = $request->input('stock');
            $keterangan = $request->input('keterangan');

            if ($new_stock != '') {
                $x = Product::where('id', $id)->first();
                $old_stock = $x->stock;

                Product::where('id', $id)->update([
                    'stock' => $new_stock
                ]);

                // Insert Log
                ProductStockLogAdmin::create([
                    'product_id' => $id,
                    'description' => "Stock Opname " . date('M Y'),
                    'from_qty' => $old_stock,
                    'to_qty' => $new_stock,
                    'total' => $new_stock - $old_stock,
                    'updated_by' => Auth::user()->id,
                ]);

                ProductStockLogUser::create([
                    'product_id' => $id,
                    'description' => "Stock Opname " . date('M Y'),
                    'total' => $new_stock - $old_stock,
                    'updated_by' => Auth::user()->id,
                ]);

                DB::commit();
                return response()->json(['data' => $request->all()]);
            }
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['status' => 0, 'message' => $e]);
        }
    }

    public function getProductIngredientsInfo(Request $request)
    {
        $id = $request->input('id');
        $pi = DB::select(DB::raw("SELECT p2.id, p2.product_name, pi.req_stock FROM products p LEFT JOIN product_ingredients pi on p.id = pi.parent_id LEFT JOIN products p2 ON pi.product_id = p2.id WHERE p.id = " . $id));

        $dataIngredient = [];
        for ($i = 0; $i < count($pi); $i++) {
            $obj = new stdClass;
            $obj->id = $pi[$i]->id;
            $obj->no = $i + 1;
            $obj->product_name = $pi[$i]->product_name;
            $obj->req_stock = number_format($pi[$i]->req_stock, 2, '.', '');

            $dataIngredient[] = $obj;
        }
        return response()->json($dataIngredient);
    }

    public function upload(Request $request)
    {
        $name = $request->input('name');
        if (move_uploaded_file(
            $_FILES['pdf']['tmp_name'],
            $_SERVER['DOCUMENT_ROOT'] . "/uploads/" . $name . ".pdf"
        )) {
            return response()->json(['status' => 1, 'filename' => $name . ".pdf"]);
        } else {
            return response()->json(['status' => 0]);
        };
    }

    public function printerDetect(Request $request)
    {
        return view('additional.index');
    }

    public function printNota(Request $req)
    {
        $data['customer_name'] = $req->input('customer_name');
        $data['address'] = $req->input('address');
        $data['phone_number'] = $req->input('phone_number');
        $data['data_product'] = $req->input('data_product');
        $data['total'] = $req->input('total');
        $data['rekening'] = $req->input('rekening');
        return view('printNota', $data);
    }
}

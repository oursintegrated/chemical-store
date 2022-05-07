<?php

namespace App\Http\Controllers;

use App\Address;
use App\ProductIngredient;
use App\Telephone;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

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
            $obj->req_stock = $pi[$i]->req_stock;

            $dataIngredient[] = $obj;
        }
        return response()->json($dataIngredient);
    }
}

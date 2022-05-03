<?php

namespace App\Http\Controllers;

use App\Address;
use App\Telephone;
use Exception;
use Illuminate\Http\Request;

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
}

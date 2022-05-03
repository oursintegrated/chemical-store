<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;

class PDFController extends Controller
{
    public function printNota(Request $request)
    {
        $data['name'] = $request->input('customerName');
        $data['address'] = $request->input('customerAddress');
        $data['phone'] = $request->input('customerNumber');
        $data['products'] = json_decode($request->input('products'));
        $data['total'] = $request->input('total');

        return view('sales.nota', $data);
    }
}

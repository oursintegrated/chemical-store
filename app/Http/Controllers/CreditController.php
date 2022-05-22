<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Credit;
use App\Role;
use Illuminate\Support\Carbon;
use Datatables;
use Validator;
use Illuminate\Support\Facades\Auth;

class CreditController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /* RBAC */
        if (!Role::authorize('dashboard.index')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'nominal' => 'required',
                'payment' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(array('status' => 0, 'message' => $validator->errors()->first()));
            } else {
                $id = $request->input('id');
                $nominal = $request->input('nominal');
                $payment = $request->input('payment');
                $notes = $request->input('notes');

                $input = Credit::create([
                    'sales_id' => $id,
                    'pay' => $nominal,
                    'payment' => $payment,
                    'notes' => $notes,
                    'updated_by' => Auth::user()->id
                ]);

                DB::commit();

                return response()->json(array('status' => 1, 'message' => 'Successfully created credit.'));
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
    public function datatable(Request $request)
    {
        /* RBAC */
        if (!Role::authorize('dashboard.index')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        $id = $request->input('id');
        $credits = DB::table('credits')->where('sales_id', $id)->get();

        return Datatables::of($credits)
            // ->addColumn('action', function ($product) {
            //     $buttons = '<div class="text-center"><div class="dropdown"><button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-bars"></i></button><ul class="dropdown-menu">';

            //     /* Tambah Action */
            //     $buttons .= '<li><a href="product/' . $product->id . '/edit"><i class="fa fa-pencil-square-o"></i>&nbsp; Edit</a></li>';
            //     $buttons .= '<li><a href="javascript:;" data-record-id="' . $product->id . '" onclick="deleteProduct($(this));"><i class="fa fa-trash"></i>&nbsp; Delete</a></li>';
            //     /* Selesai Action */

            //     $buttons .= '</ul></div></div>';

            //     return $buttons;
            // })
            ->editColumn('pay', function ($credit) {
                return number_format($credit->pay, 2, ',', '.');
            })
            ->editColumn('created_at', function ($credit) {
                return $credit->created_at ? with(new Carbon($credit->created_at))->format('d F Y H:i') : '';
            })
            ->make(true);
    }
}

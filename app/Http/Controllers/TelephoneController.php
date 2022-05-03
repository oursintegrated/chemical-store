<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Role;
use App\Telephone;
use Illuminate\Support\Facades\DB;
use Validator;

class TelephoneController extends Controller
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
        if (!Role::authorize('customer.create')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        DB::beginTransaction();

        try {
            $rules = [
                'newPhone' => 'required|min:3',
            ];

            $messages = [
                'required' => ':attribute option is required',
            ];

            $attributes = [
                'newPhone' => 'Telephone number'
            ];

            $validator = Validator::make($request->all(), $rules, $messages, $attributes);

            if ($validator->fails()) {
                return response()->json(array('status' => 0, 'message' => $validator->errors()->first()));
            } else {
                $customer_id = $request->input('customer_id');
                $newPhone = $request->input('newPhone');

                $phone = Telephone::create([
                    'customer_id' => $customer_id,
                    'phone' => $newPhone
                ]);

                DB::commit();

                return response()->json(array('status' => 1, 'message' => 'Successfully created telephone number.'));
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
    public function update(Request $request, $id)
    {
        /* RBAC */
        if (!Role::authorize('customer.edit')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        DB::beginTransaction();

        try {
            $phone = $request->input('phone');
            Telephone::findOrFail($id)->update([
                'phone' => $phone
            ]);

            DB::commit();

            return response()->json(array('status' => 1, 'message' => 'Successfully updated telephone number.'));
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
        if (!Role::authorize('customer.destroy')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        DB::beginTransaction();

        try {
            $post = Telephone::findOrFail($id);
            $post->delete();

            DB::commit();

            return response()->json(array('status' => 1, 'message' => 'Successfully deleted telephone number.'));
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(array('status' => 0, 'message' => 'Something went wrong.'));
        }
    }
}

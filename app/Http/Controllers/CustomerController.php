<?php

namespace App\Http\Controllers;

use App\Address;
use App\Customer;
use Illuminate\Http\Request;
use App\Role;
use App\SalesHeader;
use App\Telephone;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Datatables;
use Validator;
use Illuminate\Support\Carbon;

class CustomerController extends Controller
{
    public function index()
    {
        /* RBAC */
        if (!Role::authorize('customer.index')) {
            flash('Insufficient permission', 'warning');
            return redirect('dashboard');
        }

        $x = new HomeController;
        $data['menu'] = $x->getMenu();

        return view('master.customer.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        /* RBAC */
        if (!Role::authorize('customer.create')) {
            flash('Insufficient permission', 'warning');
            return redirect('dashboard');
        }

        $x = new HomeController;
        $data['menu'] = $x->getMenu();

        $data['customers'] = Customer::all();

        return view('master.customer.create', $data);
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
        if (!Role::authorize('customer.create')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        DB::beginTransaction();

        try {
            $rules = [
                'name' => 'required',
                'telephone.*' => 'required|min:1',
                'address.*' => 'required|min:1',
                'kontrabon' => 'required|min:1'
            ];

            $messages = [
                'required' => ':attribute option is required',
            ];

            $attributes = [
                'telephone.*' => 'telephone',
                'address.*' => 'address'
            ];

            $validator = Validator::make($request->all(), $rules, $messages, $attributes);

            if ($validator->fails()) {
                return response()->json(array('status' => 0, 'message' => $validator->errors()->first()));
            } else {
                $name = $request->input('name');
                $telp = $request->input('telephone');
                $address = $request->input('address');
                $kontrabon = $request->input('kontrabon');
                $code = $request->input('code');

                if (trim($code) == '') {
                    $id = '';
                    $lastId = Customer::latest()->first();
                    if (isset($lastId->id)) {
                        $id = $lastId->id;
                    } else {
                        $id = '1';
                    }

                    $code = str_random(8) . $id;
                }

                $customerId = Customer::insertGetId([
                    'code' => $code,
                    'name' => $name,
                    'kontrabon' => $kontrabon,
                    'updated_by' => Auth::user()->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                for ($i = 0; $i < count($telp); $i++) {
                    $x = Telephone::create([
                        'customer_id' => $customerId,
                        'phone' => $telp[$i]
                    ]);
                }

                for ($j = 0; $j < count($address); $j++) {
                    $y = Address::create([
                        'customer_id' => $customerId,
                        'location' => $address[$j]
                    ]);
                }

                DB::commit();

                return response()->json(array('status' => 1, 'message' => 'Successfully created customer.', 'intended_url' => '/data-master/customer'));
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
        if (!Role::authorize('customer.edit')) {
            flash('Insufficient permission', 'warning');
            return redirect('dashboard');
        }

        try {
            $data['customer'] = Customer::findOrFail($id);
            $data['addresses'] = Address::where('customer_id', $id)->get();
            $data['telephones'] = Telephone::where('customer_id', $id)->get();

            $y = DB::select(DB::raw("SELECT COUNT(id) as count FROM sales_headers WHERE customer_id = " . $id . " AND status = 0"));
            if ($y[0]->count > 0) {
                // still processing
                $data['flag_kontrabon'] = 0;
            } else {
                $data['flag_kontrabon'] = 1;
            }

            $x = new HomeController;
            $data['menu'] = $x->getMenu();

            return view('master.customer.edit', $data);
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
        if (!Role::authorize('customer.edit')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'kontrabon' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(array('status' => 0, 'message' => $validator->errors()->first()));
            } else {
                $name = $request->input('name');
                $telephone = $request->input('telephone');
                $kontrabon = $request->input('kontrabon');

                Customer::findOrFail($id)->update([
                    'name' => $name,
                    'telp' => $telephone,
                    'updated_by' => Auth::user()->id,
                    'kontrabon' => $kontrabon
                ]);

                DB::commit();

                return response()->json(array('status' => 1, 'message' => 'Successfully updated customer.', 'intended_url' => '/data-master/customer'));
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
        if (!Role::authorize('customer.destroy')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        DB::beginTransaction();

        try {
            $post = Customer::findOrFail($id);
            $post->delete();

            DB::commit();

            return response()->json(array('status' => 1, 'message' => 'Successfully deleted customer.'));
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(array('status' => 0, 'message' => 'Something went wrong.'));
        }
    }

    public function datatable()
    {
        /* RBAC */
        if (!Role::authorize('customer.index')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        $customers = DB::select(DB::raw("SELECT c.*, (SELECT GROUP_CONCAT(DISTINCT (t.phone) SEPARATOR ', ') FROM telephones t WHERE c.id = t.customer_id) as phone FROM customers c"));

        return Datatables::of($customers)
            ->addColumn('action', function ($customer) {
                $buttons = '<div class="text-center"><div class="dropdown"><button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-bars"></i></button><ul class="dropdown-menu">';

                /* Tambah Action */
                $buttons .= '<li><a href="customer/' . $customer->id . '/edit"><i class="fa fa-pencil-square-o"></i>&nbsp; Edit</a></li>';
                $buttons .= '<li><a href="javascript:;" data-record-id="' . $customer->id . '" onclick="deleteCustomer($(this));"><i class="fa fa-trash"></i>&nbsp; Delete</a></li>';
                /* Selesai Action */

                $buttons .= '</ul></div></div>';

                return $buttons;
            })
            ->editColumn('kontrabon', function ($customer) {
                return $customer->kontrabon . ' days';
            })
            ->editColumn('created_at', function ($customer) {
                return $customer->created_at ? with(new Carbon($customer->created_at))->format('d F Y H:i') : '';
            })
            ->editColumn('updated_at', function ($customer) {
                return $customer->updated_at ? with(new Carbon($customer->updated_at))->format('d F Y H:i') : '';
            })
            ->make(true);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\APIService;
use App\Store;
use App\User;
use App\Role;
use App\VerificationCode;
use Carbon\Carbon;
use Datatables;
use DB;
use Log;
use Validator;
use Hash;
use Auth;

class UserController extends Controller
{
    protected $_APIService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(APIService $APIService)
    {
        $this->_APIService = $APIService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /* RBAC */
        if (!Role::authorize('user.index')) {
            flash('Insufficient permission', 'warning');
            return redirect('dashboard');
        }

        $x = new HomeController;
        $data['menu'] = $x->getMenu();

        return view('user.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        /* RBAC */
        if (!Role::authorize('user.create')) {
            flash('Insufficient permission', 'warning');
            return redirect('dashboard');
        }

        $data['roles'] = Role::all();

        $x = new HomeController;
        $data['menu'] = $x->getMenu();

        return view('user.create', $data);
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
        if (!Role::authorize('user.create')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'full_name' => 'required',
                'username' => 'required',
                'email' => 'required|email',
                'role_id' => 'required',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(array('status' => 0, 'message' => $validator->errors()->first()));
            } else {
                $full_name = ucwords(strtolower($request->input('full_name')));
                $username = $request->input('username');
                $email = $request->input('email');
                $role_id = $request->input('role_id');
                $store_code = $request->input('store_code');
                $password = Hash::make($request->input('password'));
                $is_suspended = 0;
                $is_disabled = 0;

                $user = User::create([
                    'full_name' => $full_name,
                    'username' => $username,
                    'email' => $email,
                    'role_id' => $role_id,
                    'store_code' => $store_code,
                    'password' => $password,
                    'is_suspended' => $is_suspended,
                    'is_disabled' => $is_disabled,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id
                ]);

                DB::commit();

                return response()->json(array('status' => 1, 'message' => 'Successfully created user.', 'intended_url' => '/configuration/user'));
            }
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(array('status' => 0, 'message' => 'Something went wrong.'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        if (!Role::authorize('user.edit')) {
            flash('Insufficient permission', 'warning');
            return redirect('dashboard');
        }

        try {
            $data['roles'] = Role::all();
            $data['user'] = User::findOrFail($id);

            $x = new HomeController;
            $data['menu'] = $x->getMenu();

            return view('user.edit', $data);
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
        if (!Role::authorize('user.edit')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'full_name' => 'required',
                'email' => 'required|email',
                'role_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(array('status' => 0, 'message' => $validator->errors()->first()));
            } else {
                $full_name = ucwords(strtolower($request->input('full_name')));
                $email = $request->input('email');
                $role_id = $request->input('role_id');
                $store_code = $request->input('store_code');
                $is_suspended = 0;
                $is_disabled = 0;

                User::findOrFail($id)->update([
                    'full_name' => $full_name,
                    'email' => $email,
                    'role_id' => $role_id,
                    'store_code' => $store_code,
                    'is_suspended' => $is_suspended,
                    'is_disabled' => $is_disabled,
                    'updated_by' => Auth::user()->id
                ]);

                DB::commit();

                return response()->json(array('status' => 1, 'message' => 'Successfully updated user.', 'intended_url' => '/configuration/user'));
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
        if (!Role::authorize('user.destroy')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        DB::beginTransaction();

        try {
            $post = User::findOrFail($id);
            $post->delete();

            DB::commit();

            return response()->json(array('status' => 1, 'message' => 'Successfully deleted user.'));
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(array('status' => 0, 'message' => 'Something went wrong.'));
        }
    }

    public function datatable()
    {
        $users = DB::table('users')->select(['users.id', 'full_name', 'username', 'email', 'roles.display_name as role_name', 'password', 'is_suspended', 'is_disabled', 'users.created_at', 'users.updated_at'])
            ->join('roles', 'roles.id', '=', 'users.role_id');

        return Datatables::of($users)
            ->addColumn('action', function ($user) {
                $buttons = '<div class="text-center"><div class="dropdown"><button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-bars"></i></button><ul class="dropdown-menu">';

                /* Tambah Action */
                $buttons .= '<li><a href="user/' . $user->id . '/edit"><i class="fa fa-pencil-square-o"></i>&nbsp; Edit</a></li>';
                $buttons .= '<li><a href="javascript:;" data-record-id="' . $user->id . '" onclick="deleteUser($(this));"><i class="fa fa-trash"></i>&nbsp; Delete</a></li>';
                // $buttons .= '<li><a href="user/' . $user->id . '/reset-password"><i class="fa fa-unlock"></i>&nbsp; Reset Password</a></li>';
                /* Selesai Action */

                $buttons .= '</ul></div></div>';

                return $buttons;
            })
            ->editColumn('created_at', function ($user) {
                return $user->created_at ? with(new Carbon($user->created_at))->format('d F Y H:i') : '';
            })
            ->editColumn('updated_at', function ($user) {
                return $user->updated_at ? with(new Carbon($user->updated_at))->format('d F Y H:i') : '';
            })
            ->make(true);
    }

    public function showResetPassword($user_id)
    {
        /* RBAC */
        if (!Role::authorize('user.reset')) {
            flash('Insufficient permission', 'warning');
            return redirect('home');
        }

        try {
            $data['user'] = User::findOrFail($user_id);

            $x = new HomeController;
            $data['menu'] = $x->getMenu();

            return view('auth.passwords.reset', $data);
        } catch (Exception $e) {
        }
    }

    public function showForgotPassword()
    {
        return view('auth.passwords.email');
    }

    public function checkEmail(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'email' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(array('status' => 0, 'message' => $validator->errors()->first()));
            } else {
                $user = DB::table('users')->select(DB::raw('count(*) as user_count'))
                    ->where('email', '=', $request->input('email'))
                    ->get();

                if ($user[0]->user_count != 0) {
                    $string_random = Str::random(8);

                    // Initialize headers
                    $headers = [
                        'app_id' => 'tenant'
                    ];

                    $body = [
                        'type' => 'body',
                        'boundary' => true,
                        'data' => [
                            'to' => $request->input('email'),
                            'subject' => 'Verification Code',
                            'body' => 'Your verification code is ' . $string_random
                        ]
                    ];

                    Log::info(json_encode($body));

                    $resultSendEmail = $this->_APIService->requestApi(env('API_EMAIL_SERVER'), 'POST', '/api/v1/sendemail', 'multipart/form-data', $headers, $body);

                    if ($resultSendEmail['is_success']) {
                        $verification_code = VerificationCode::create([
                            'email' => $request->input('email'),
                            'code_verification' => $string_random
                        ]);

                        DB::commit();

                        return response()->json(array('status' => 1, 'message' => 'Verification code has been sent to your email, Please Check.'));
                    } else {
                        return response()->json(array('status' => 0, 'message' => 'Something went wrong.'));
                    }
                } else {
                    return response()->json(array('status' => 0, 'message' => 'Your email not available in System.'));
                }
            }
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(array('status' => 0, 'message' => 'Something went wrong.'));
        }
    }

    public function verificationCode(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'verification_code' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(array('status' => 0, 'message' => $validator->errors()->first()));
            } else {
                $returnDelete = VerificationCode::where('email', $request->input('email'))->where('code_verification', $request->input('verification_code'))->delete();

                DB::commit();

                if ($returnDelete) {
                    return response()->json(array('status' => 1, 'message' => 'Verification code is valid'));
                } else {
                    return response()->json(array('status' => 0, 'message' => 'Verification code not valid'));
                }
            }
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(array('status' => 0, 'message' => 'Something went wrong.'));
        }
    }

    public function changePassword(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'email' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(array('status' => 0, 'message' => $validator->errors()->first()));
            } else {
                User::where('email', $request->input('email'))->update(['password' => Hash::make($request->input('new_password'))]);

                DB::commit();
                return response()->json(array('status' => 1, 'message' => 'Your password has been changed'));
            }
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(array('status' => 0, 'message' => 'Something went wrong.'));
        }
    }
}

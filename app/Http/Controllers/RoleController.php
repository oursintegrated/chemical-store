<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Datatables;
use Carbon\Carbon;
use DB;
use Validator;
use App\Http\Controllers\Controller;
use App\Role;
use App\User;
use Auth;
use stdClass;
use App\RoleMenu;
use Illuminate\Support\Facades\Redis;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /* RBAC */
        if (!Role::authorize('role.index')) {
            flash('Insufficient permission', 'warning');
            return redirect('dashboard');
        }

        $x = new HomeController;
        $data['menu'] = $x->getMenu();

        return view('role.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        /* RBAC */
        if (!Role::authorize('role.create')) {
            flash('Insufficient permission', 'warning');
            return redirect('dashboard');
        }

        $x = new HomeController;
        $data['menu'] = $x->getMenu();

        return view('role.create', $data);
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
        if (!Role::authorize('role.create')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'display_name' => 'required',
                'description' => 'required',
                'menu_id' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(array('status' => 0, 'message' => $validator->errors()->first()));
            } else {
                $menu = explode(',', $request->input('menu_id'));
                $name = strtolower($request->input('role_name'));

                $name = strtolower($request->input('name'));
                $display_name = ucwords(strtolower($request->input('display_name')));
                $description = ucfirst($request->input('description'));

                $role_id = Role::insertGetId([
                    'name' => $name,
                    'display_name' => $display_name,
                    'description' => $description,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id
                ]);

                for ($i = 0; $i < count($menu); $i++) {
                    RoleMenu::create([
                        'role_id' => $role_id,
                        'menu_id' => $menu[$i],
                    ]);
                };

                DB::commit();

                return response()->json(array('status' => 1, 'message' => 'Successfully created role.', 'intended_url' => '/configuration/role'));
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
        if (!Role::authorize('role.edit')) {
            flash('Insufficient permission', 'warning');
            return redirect('dashboard');
        }

        try {
            $data['role'] = Role::findOrFail($id);

            $x = new HomeController;
            $data['menu'] = $x->getMenu();

            return view('role.edit', $data);
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
        if (!Role::authorize('role.edit')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'display_name' => 'required',
                'description' => 'required',
                'menu_id' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(array('status' => 0, 'message' => $validator->errors()->first()));
            } else {
                $name = strtolower($request->input('name'));
                $display_name = ucwords(strtolower($request->input('display_name')));
                $description = ucfirst($request->input('description'));
                $new = explode(',', $request->input('menu_id'));
                $role_id = $request->input('role_id');

                $q_old = RoleMenu::select('menu_id')->where('role_id', $role_id)->get();
                $old = [];
                for ($i = 0; $i < count($q_old); $i++) {
                    $old[] = $q_old[$i]->menu_id;
                }

                // delete old -> new
                $diff = [];
                $compares = array_diff($old, $new);
                foreach ($compares as $compare => $value) {
                    $diff[] = $value;
                }

                if (count($diff) > 0) {
                    $del = RoleMenu::whereIn('menu_id', $diff)->where('role_id', $role_id)->delete();
                }

                // insert new -> old
                $in = [];
                $compares = array_diff($new, $old);
                foreach ($compares as $compare => $value) {
                    $in[] = $value;
                }

                for ($j = 0; $j < count($in); $j++) {
                    $ins = RoleMenu::create([
                        'role_id' => $role_id,
                        'menu_id' => $in[$j],
                        'created_by' => Auth::user()->id,
                        'last_updated_by' => Auth::user()->id,
                    ]);
                }

                Role::findOrFail($id)->update([
                    'name' => $name,
                    'display_name' => $display_name,
                    'description' => $description,
                    'updated_by' => Auth::user()->id
                ]);

                // delete cache
                Redis::del('sidebar:' . $role_id);
                Redis::del('rbac:' . $role_id);

                DB::commit();

                return response()->json(array('status' => 1, 'message' => 'Successfully updated role.', 'intended_url' => '/configuration/role'));
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
        if (!Role::authorize('role.destroy')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        DB::beginTransaction();

        try {
            $post = Role::findOrFail($id);
            $post->delete();

            DB::commit();

            return response()->json(array('status' => 1, 'message' => 'Successfully deleted role.'));
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
        if (!Role::authorize('role.index')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        $roles = DB::table('roles')->select(['id', 'name', 'display_name', 'description', 'created_at', 'updated_at']);

        return Datatables::of($roles)
            ->addColumn('action', function ($role) {
                $buttons = '<div class="text-center"><div class="dropdown"><button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-bars"></i></button><ul class="dropdown-menu">';

                /* Tambah Action */
                $buttons .= '<li><a href="role/' . $role->id . '/edit"><i class="fa fa-pencil-square-o"></i>&nbsp; Edit</a></li>';
                $buttons .= '<li><a href="javascript:;" data-record-id="' . $role->id . '" onclick="deleteRole($(this));"><i class="fa fa-trash"></i>&nbsp; Delete</a></li>';
                /* Selesai Action */

                $buttons .= '</ul></div></div>';

                return $buttons;
            })
            ->editColumn('created_at', function ($role) {
                return $role->created_at ? with(new Carbon($role->created_at))->format('d F Y H:i') : '';
            })
            ->editColumn('updated_at', function ($role) {
                return $role->updated_at ? with(new Carbon($role->updated_at))->format('d F Y H:i') : '';
            })
            ->make(true);
    }

    public function generateMenu(Request $req)
    {
        $id = $_GET['id'];
        $type = $_GET['type'];
        $role_id = $_GET['role_id'];

        if ($type == 'create') {
            $x = DB::select(DB::raw('SELECT m.id, m.level, m.menu_desc, a.id as parent_id, a.menu_desc as parent_desc FROM menus m LEFT JOIN menus a ON m.parent_id = a.id ORDER BY m.level ASC'));
            for ($i = 0; $i < count($x); $i++) {
                $temp = new stdClass();

                $temp->id = $x[$i]->id;
                $temp->text = $x[$i]->menu_desc;

                if ($x[$i]->level == 1) {
                    $temp->parent = '#';
                } else {
                    $temp->parent = $x[$i]->parent_id;
                }

                $menu[] = $temp;
            }

            return $menu;
        } else if ($type == 'edit') {
            // get role menus automatic selected and opened
            $selected = [];
            $temp_opened = [];
            $z = DB::select(DB::raw('SELECT rm.menu_id, m.parent_id, m.level FROM role_menus rm JOIN menus m ON rm.menu_id = m.id WHERE rm.role_id = ' . $role_id));
            for ($k = 0; $k < count($z); $k++) {
                $selected[] = $z[$k]->menu_id;
                if ($z[$k]->level == 1) {
                    $temp_opened[] = $z[$k]->menu_id;
                }
            }
            $opened = array_unique($temp_opened);

            $menu = [];
            $x = DB::select(DB::raw('SELECT m.id, m.level, m.menu_desc, a.id as parent_id, a.menu_desc as parent_desc FROM menus m LEFT JOIN menus a ON m.parent_id = a.id ORDER BY m.level ASC'));
            for ($i = 0; $i < count($x); $i++) {
                $temp = new stdClass();

                $temp->id = $x[$i]->id;
                $temp->text = $x[$i]->menu_desc;

                if ($x[$i]->level == 1) {
                    $temp->parent = '#';
                } else {
                    $temp->parent = $x[$i]->parent_id;
                }

                if (in_array($x[$i]->id, $opened) && in_array($x[$i]->id, $selected)) {
                    $temp->state = ['opened' => true, 'selected' => true];
                } else if (in_array($x[$i]->id, $opened)) {
                    $temp->state = ['opened' => true];
                } else if (in_array($x[$i]->id, $selected)) {
                    $temp->state = ['selected' => true];
                }

                $menu[] = $temp;
            }

            return $menu;
        }
    }
}

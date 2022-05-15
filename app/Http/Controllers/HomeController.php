<?php

namespace App\Http\Controllers;

use App\SalesHeader;
use Illuminate\Http\Request;
use stdClass;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Role;
use DataTables;
use DateTime;
use Illuminate\Support\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['menu'] = $this->getMenu();
        return view('dashboard.index', $data);
    }

    public function getMenu()
    {
        // Menu
        $root = [];
        $sub = [];
        $child = [];
        $menu = new stdClass();

        $cached = Redis::get('sidebar:' . Auth::user()->role_id);
        if (isset($cached)) {
            $menu = json_decode($cached, FALSE);
            return $menu;
        } else {
            $x = DB::select(DB::raw("SELECT m.id, m.menu_desc as menu, a.id as parent_id, m.level, (SELECT count(id) FROM menus a WHERE a.parent_id = m.id AND a.type = 'menu') as child
            FROM menus m
            LEFT JOIN menus a ON m.parent_id = a.id
            WHERE m.status = 1 AND m.id IN (SELECT mrm.menu_id FROM role_menus mrm WHERE role_id = " . Auth::user()->role_id . ")
            AND m.type = 'menu'
            ORDER BY m.level, id ASC"));

            for ($i = 0; $i < count($x); $i++) {
                $id = $x[$i]->id;
                $parent_id = $x[$i]->parent_id;
                $level = $x[$i]->level;
                $name = $x[$i]->menu;
                $child = $x[$i]->child;

                $temp = new stdClass();
                $temp->id = $id;
                $temp->name = $name;
                $temp->child = $child;

                if ($level == 1) {
                    $root[$id] = $temp;
                } else if ($level == 2) {
                    $sub[$parent_id][] = $temp;
                } else if ($level == 3) {
                    $child[$parent_id][] = $temp;
                }
            }

            $menu->root = $root;
            $menu->sub = $sub;
            $menu->child = $child;

            Redis::set('sidebar:' . Auth::user()->role_id, json_encode($menu));
            return $menu;
        }
    }

    public function updateStatus(Request $request, $id)
    {
        /* RBAC */
        if (!Role::authorize('sales.index')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        DB::beginTransaction();

        try {
            $payment = $request->input('payment');

            if ($payment == null) {
                SalesHeader::findOrFail($id)->update([
                    'status' => 1
                ]);
            } else {
                SalesHeader::findOrFail($id)->update([
                    'status' => 1,
                    'payment' => $payment
                ]);
            }


            DB::commit();

            return response()->json(array('status' => 1, 'message' => 'Successfully updated status.'));
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(array('status' => 0, 'message' => 'Something went wrong.'));
        }
    }

    public function deleteStatus($id)
    {
        /* RBAC */
        if (!Role::authorize('sales.index')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        DB::beginTransaction();

        try {
            SalesHeader::findOrFail($id)->update([
                'status' => 0
            ]);

            DB::commit();

            return response()->json(array('status' => 1, 'message' => 'Successfully updated status.'));
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
    public function datatableSales()
    {
        /* RBAC */
        if (!Role::authorize('dashboard.index')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        $sales = DB::select(DB::raw("SELECT *, DATE_ADD(transaction_date, INTERVAL due_date DAY) as due FROM sales_headers WHERE status = 0 ORDER BY due ASC"));

        return Datatables::of($sales)
            ->addColumn('action', function ($sale) {
                $buttons = '<div class="text-center"><div class="dropdown"><button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-bars"></i></button><ul class="dropdown-menu">';

                /* Tambah Action */
                $buttons .= '<li><a href="javascript:;" data-record-id="' . $sale->id . '" data-type="' . $sale->type . '" onclick="completeSales($(this));"><i class="fa fa-check"></i>&nbsp; Complete</a></li>';
                /* Selesai Action */

                $buttons .= '</ul></div></div>';

                return $buttons;
            })
            ->editColumn('type', function ($sale) {
                if ($sale->type == 'tunai') {
                    return '<span class="label label-success"> Tunai ' . $sale->payment . ' </span>';
                } else if ($sale->type == 'kontrabon') {
                    return '<span class="label label-warning"> Kasbon - ' . $sale->due_date . ' </span>';
                } else if ($sale->type == 'kredit') {
                    return '<span class="label label-info"> Kredit </span>';
                }
            })
            ->editColumn('total', function ($sale) {
                return 'Rp ' . number_format($sale->total, 2, ',', '.');
            })
            ->editColumn('transaction_date', function ($sale) {
                return $sale->transaction_date ? with(new Carbon($sale->transaction_date))->format('d F Y H:i') : '';
            })
            ->editColumn('status', function ($sale) {
                if ($sale->status == 1) {
                    return '<span class="label label-success"> Complete </span>';
                } else {
                    return '<span class="label label-warning"> Not Complete </span>';
                }
            })
            ->editColumn('due_date', function ($sale) {
                if ($sale->type == 'tunai' || $sale->type == 'kredit') {
                    return '-';
                } else if ($sale->type == 'kontrabon') {
                    $temp = date_create($sale->transaction_date);
                    $date = date_format($temp, "d M Y");
                    $due = $sale->due_date;
                    $due_date_temp = date_add($temp, date_interval_create_from_date_string($due . " days"));
                    $due_date = date_format($due_date_temp, "d M Y");
                    return $due_date;
                }
            })
            ->rawColumns(['action', 'type', 'status'])
            ->make(true);
    }
}

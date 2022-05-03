<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use stdClass;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
}

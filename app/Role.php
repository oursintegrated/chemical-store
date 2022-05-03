<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Role extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    /**
     * Get the users record associated with the user.
     */
    public function users()
    {
        return $this->hasMany('App\User');
    }

    public static function authorize($permission)
    {
        $role_permissions = [];

        $cached = Redis::get('rbac:' . Auth::user()->role_id);
        if (isset($cached)) {
            $role_permissions = json_decode($cached, FALSE);
        } else {
            $x = DB::select(DB::raw('SELECT rm.*, r.key FROM role_menus rm 
            LEFT JOIN rbac r ON rm.menu_id = r.menu_id 
            WHERE rm.role_id = ' . Auth::user()->role_id));

            for ($i = 0; $i < count($x); $i++) {
                if ($x[$i]->key != null) {
                    $role_permissions[] = $x[$i]->key;
                }
            }

            // $role_permissions[] = 'dashboard';

            Redis::set('rbac:' . Auth::user()->role_id, json_encode($role_permissions));
        }

        if (!in_array($permission, $role_permissions)) {
            return false;
        }

        return true;
    }
}

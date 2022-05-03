<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoleMenu extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'role_menus';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role_id',
        'menu_id'
    ];
}

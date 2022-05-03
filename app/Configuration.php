<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'configurations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'value',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];
}

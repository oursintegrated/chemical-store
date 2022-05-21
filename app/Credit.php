<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Credit extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'credits';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sales_id',
        'pay',
        'payment',
        'notes',
        'updated_by'
    ];
}

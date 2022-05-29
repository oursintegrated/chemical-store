<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivityStock extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'activity_stocks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'updated_by',
        'from_qty',
        'to_qty',
        'qty',
        'description',
    ];
}

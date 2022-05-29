<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivityDetailStock extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'activity_detail_stocks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'log_id',
        'product_id',
        'qty'
    ];
}

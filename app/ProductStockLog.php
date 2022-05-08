<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductStockLog extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'product_stock_log';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'description',
        'from_qty',
        'to_qty',
        'updated_by',
        'flag_admin'
    ];
}

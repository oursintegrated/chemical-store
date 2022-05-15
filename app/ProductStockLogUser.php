<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductStockLogUser extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'product_stock_log_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'description',
        'total',
        'updated_by'
    ];
}

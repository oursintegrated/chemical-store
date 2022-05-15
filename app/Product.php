<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'product_name',
        'type',
        'stock',
        'min_stock',
        'parent_stock',
        'description',
        'updated_by',
    ];
}

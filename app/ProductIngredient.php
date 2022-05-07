<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductIngredient extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'product_ingredients';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id',
        'product_id',
        'req_stock',
    ];
}

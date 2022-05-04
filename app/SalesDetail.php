<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesDetail extends Model
{
    /**
     * Table associated with the model
     * 
     * @var string
     */
    protected $table = 'sales_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sales_header_id',
        'product_id',
        'product_name',
        'qty',
        'price',
        'total',
    ];
}

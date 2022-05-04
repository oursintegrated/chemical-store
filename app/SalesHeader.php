<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesHeader extends Model
{
    /**
     * Table associated with the model
     * 
     * @var string
     */
    protected $table = 'sales_headers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sales_code',
        'customer_id',
        'customer_name',
        'phone_number',
        'address',
        'type',
        'due_date',
        'transaction_date',
        'total',
        'updated_by',
    ];
}

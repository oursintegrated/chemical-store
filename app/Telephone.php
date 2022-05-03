<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Telephone extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'telephones';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'phone'
    ];
}

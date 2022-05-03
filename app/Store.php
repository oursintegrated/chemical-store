<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    /**
     * Table associated with the model
     * 
     * @var string
     */
    protected $table = 'stores';

    /**
     * Attributes that are not mass assignable
     * 
     * @var array
     */
    protected $guarded = [
        'id',
        'store_code',
        'initials_code',
        'store_desc',
        'store_status'
    ];

    /**
     * Attributes that should be mutated to date
     * 
     * @var array
     */
    protected $date = [
        'created_at',
        'updated_at'
    ];

    /**
     * Get the users record associated with the store.
     * 
     */
    public function users()
    {
        return $this->hasMany('App\User', 'store_code', 'store_code');
    }
}

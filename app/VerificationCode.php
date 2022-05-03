<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VerificationCode extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'verification_code';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'code_verification',
        'created_at',
        'updated_at'
    ];
}

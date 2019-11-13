<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Log
 * @package App\Entities
 */
class Log extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'to', 'body', 'email_metadata', 'provider', 'sent_at', 'failed_at'
    ];

    /**
     * @var array
     */
    protected $dates = [
        'sent_at', 'failed_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_metadata' => 'json',
    ];
}

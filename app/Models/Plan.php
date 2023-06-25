<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'id',
        'product_id',
        'name',
        'description',
        'status',
        'billing_cycles',
        'payment_preferences',
        'taxes',
        'links',
    ];

    protected $casts = [
        'billing_cycles' => 'json',
        'payment_preferences' => 'json',
        'taxes' => 'json',
        'links' => 'json',
    ];
}

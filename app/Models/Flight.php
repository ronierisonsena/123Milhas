<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{

    protected $fillable = [
        'id', 
        'cia', 
        'fare',
        'flightNumber',
        'origin',
        'destination',
        'departureDate',
        'arrivalDate',
        'departureTime',
        'arrivalTime',
        'classService',
        'price',
        'tax',
        'outbound',
        'inbound',
        'duration',
    ];

}

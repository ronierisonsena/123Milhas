<?php

namespace App\Models;

use App\Models\Flight;

class FlightGroup extends Flight
{
    protected $fillable = [
        'uniqueId', 
        'fare', 
        'totalPrice', 
        'outbound',  // voos ida
        'inbound', // vos de volta
    ];

    public function __construct()
    {
        $this->outbound = collect();
        $this->inbound = collect();
    }

    public function getCheapestPrice()
    {
        
    }

    // Adiciona um voo de Ida
    public function setOutbound($flight)
    {
        $existFlight = $this->outbound->where('id', $flight->id)->first();

        if (!$existFlight) {
            $this->outbound->push($flight);
        }

        return $this->outbound;
    }

    // Adiciona um voo de volta
    public function setInbound($flight)
    {
        $existFlight = $this->inbound->where('id', $flight->id)->first();

        if (!$existFlight) {
            $this->inbound->push($flight);
        }
        
        return $this->inbound;
    }
}

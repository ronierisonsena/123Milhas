<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlightGroup extends Model
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

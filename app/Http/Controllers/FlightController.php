<?php

namespace App\Http\Controllers;

use App\Models\Flight;
use App\Models\FlightGroup;

class FlightController extends Controller
{
    private $url = 'http://prova.123milhas.net/api/flights';

    /**
     * Retorna todos os vôos disponiveis
     * 
     * @return Array
     */
    public function getFlights($inboundOutbound = null) : array
    {
        // dd($inboundOutbound);
        $retorno = [
            'status' => 200,
            'data' => []
        ];

        try {
            $flights = json_decode(
                file_get_contents($this->url)
            );

            if ($inboundOutbound) {
                $flights = collect($flights);
                $flights = $flights->where('outbound', ($inboundOutbound == 'outbound' ? 1 : 0));
            }

            $retorno['data'] = $flights;
        } catch (\Exception $e) {
            $retorno['status'] = 422;
            $retorno['message'] = $e->getMessage();
        }

        return $retorno;
    }


    /**
     * Retorna os grupos de vôos ordenados por menor preço
     * 
     * @return Array
     */
    public function getFlightsGroups() : array
    {
        $data = [
            'status' => 200,
            'data' => []
        ];

        try {
            $flights = $this->getFlights()['data'];

            // cria a collection de Flight's
            $flights = collect(
                array_map(function($flight) {
                    return new Flight((array) $flight);
                }, $flights)
            );

            // Busca vôos ida/volta
            $outboundFlights = $flights->where('outbound', 1);
            $inboundFlights = $flights->where('outbound', 0)->all();
            
            // Cria os grupos
            $groups = [];
            
            foreach ($outboundFlights as $outboundFlight) {
                array_walk($inboundFlights, function($inboundFlight) use ($outboundFlight, &$groups) {                    

                    if ($outboundFlight->fare === $inboundFlight->fare) {
                        // Valor total das passagens do grupo
                        $totalValue = $outboundFlight->price + $inboundFlight->price;
                        
                        // Cria o ID do grupo
                        $idGrupo = md5($totalValue);
                        
                        // Cria o grupo se não existir
                        if (!isset($groups[$idGrupo])) {
                            $groups[$idGrupo] = new FlightGroup();
                        }

                        // Seta os valores
                        $groups[$idGrupo]['uniqueId'] = $idGrupo;
                        $groups[$idGrupo]['fare'] = $outboundFlight->fare;
                        $groups[$idGrupo]['totalPrice'] = $totalValue;
                        $groups[$idGrupo]->setOutbound($outboundFlight);
                        $groups[$idGrupo]->setInbound($inboundFlight);
                    }
                });
            };

            $data['data'] = collect($groups)->sortBy('totalPrice');
        } catch (\Exception $e) {
            $data['status'] = 422;
            $data['message'] = $e->getMessage();
        }

        return $data;
    }

    /**
     * Retorna uma lista de vôos, grupos de vôos, total de grupos, total vôos
     * 
     * @return Array
     */
    public function getAll() : array
    {
        try {
            $flights = $this->getFlights()['data'];
            $flightsGroups = collect(
                    $this->getFlightsGroups()['data']
                );

            $minPrice = $flightsGroups->min('totalPrice');

            $data['flights'] = $flights;
            $data['groups'] = $flightsGroups;
            $data['totalGroups'] = $flightsGroups->count();
            $data['totalFlights'] = count($flights);
            $data['cheapestPrice'] = $flightsGroups->min('totalPrice');
            $data['cheapestGroup'] = $flightsGroups->where('totalPrice', $minPrice)->first()->uniqueId;

            return $data;
        } catch(\Exception $e) {
            return [
                'status' => 422,
                'message' => $e->getMessage()
            ];
        }
    }
}

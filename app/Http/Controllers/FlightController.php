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
    public function getFlights($inboundOutbound = null)
    {
        try {
            $flights = json_decode(
                file_get_contents($this->url)
            );

            if ($inboundOutbound) {
                $flights = collect($flights);
                $flights = $flights->where('outbound', ($inboundOutbound == 'outbound' ? 1 : 0));
            }

            return json_encode($flights);
        } catch (\Exception $e) {
            return json_encode([
                'message' => $e->getMessage()
            ]);
        }
    }


    /**
     * Retorna os grupos de vôos ordenados por menor preço
     * 
     * @return Array
     */
    public function getFlightsGroups()
    {
        global $flights;

        try {
            // cria a collection de Flight's
            $flights = collect(
                    json_decode($this->getFlights())
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
                        
                        // TODO definir id do grupo, verificar grupo com valores iguais
                        $groupId = md5($totalValue);
                        
                        // Cria o grupo se não existir
                        if (!isset($groups[$groupId])) {
                            $groups[$groupId] = new FlightGroup();
                        }

                        // Seta os valores
                        $groups[$groupId]['uniqueId'] = $groupId;
                        $groups[$groupId]['fare'] = $outboundFlight->fare;
                        $groups[$groupId]['totalPrice'] = $totalValue;
                        $groups[$groupId]->setOutbound($outboundFlight);
                        $groups[$groupId]->setInbound($inboundFlight);
                    }
                });
            };

            return json_encode(
                collect($groups)->sortBy('totalPrice')
            );
        } catch (\Exception $e) {
            return json_encode([
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Retorna uma lista de vôos, grupos de vôos, total de grupos, total vôos
     * 
     * @return Array
     */
    public function getAll()
    {
        global $flights;
        
        try {
            $flightsGroups = collect(
                    json_decode($this->getFlightsGroups())
                );
            $flights = collect(
                json_decode($flights)
            );

            $minPrice = $flightsGroups->min('totalPrice');

            $data['flights'] = $flights;
            $data['groups'] = $flightsGroups;
            $data['totalGroups'] = $flightsGroups->count();
            $data['totalFlights'] = count($flights);
            $data['cheapestPrice'] = $flightsGroups->min('totalPrice');
            $data['cheapestGroup'] = $flightsGroups->where('totalPrice', $minPrice)->first()->uniqueId;

            return json_encode($data);
        } catch(\Exception $e) {
            return json_encode([
                'message' => $e->getMessage()
            ]);
        }
    }
}

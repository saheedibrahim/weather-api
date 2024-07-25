<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class WeatherController extends Controller
{
    public function weather(Request $request, $name)
    {
        $visitorName = $request->input('visitor_name', $name);
       
        $clientIp = $request->getClientIp();     // Get client IP address
        $location = $this->getLocationByIp($clientIp); //Get client details i.e country, city, latitude, longitude etc
        $temperature = $this->getTemperature($location); // Get client location temperature
        $response = [
            'client_ip' => $location['query'],
            'location' => $location['city'],
            'greeting' => "Hello, $visitorName! The temperature is {$temperature['current']['temp_c']} degrees Celsius in {$location['city']}."
        ];
        
        return response()->json($response);
    }
    
    private function getLocationByIp($ip)
    {
        $client = new Client();
        $response = $client->get("http://ip-api.com/json/{$ip}");
        $data = json_decode($response->getBody(), true);

        return $data;
    }
    
    private function getTemperature($getLoc)
    {
        $client = new Client();
        $api = "30329a0a640c479ebec65139240307";
        $response = $client->get("http://api.weatherapi.com/v1/current.json?key=$api&q={$getLoc['lat']},{$getLoc['lon']}");
        $data = json_decode($response->getBody(), true);

        return $data;
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class weatherController extends Controller
{
    protected $user;
    public function __construct()
    {
        // check user login
        $this->user = auth()->user();
    }
    public function checkWeather($location)
    {
        // user not login
        if (!isset($this->user)) {
            return response()->json([
                'message' => 'Please Login first'
            ]);
        } else {
            $apiKey = 'bc6e9dcada92a184251a084d1bfe4412'; // api key for openweather API
            $response = Http::get("https://api.openweathermap.org/data/2.5/weather?q=$location&appid=$apiKey"); //create custom response for specific location

            return $response->json();
        }
    }
}

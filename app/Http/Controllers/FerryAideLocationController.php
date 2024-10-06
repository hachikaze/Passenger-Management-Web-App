<?php

namespace App\Http\Controllers;

use App\Models\FerryAideLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // To get the current user

class FerryAideLocationController extends Controller
{
    // List of station coordinates
    protected $stations = [
        'Guadalupe' => ['lat' => 14.5680014, 'lng' => 121.0479274],
        'San Joaquin' => ['lat' => 14.5545958, 'lng' => 121.073561],
        'Kalawaan' => ['lat' => 14.553088, 'lng' => 121.0817678],
        'Sta. Ana' => ['lat' => 14.582265, 'lng' => 121.0109986],
        'Hulo' => ['lat' => 14.5680219, 'lng' => 121.0332236],
        'Lambingan' => ['lat' => 14.5873821, 'lng' => 121.0178311],
        'Pinagbuhatan' => ['lat' => 14.5359297, 'lng' => 121.1015889],
        'Valenzuela' => ['lat' => 14.5739052, 'lng' => 121.0251063],
        'PUP' => ['lat' => 14.59607, 'lng' => 121.0101495],
        'Lawton' => ['lat' => 14.595716, 'lng' => 120.9807355],
        'Escolta' => ['lat' => 14.596435, 'lng' => 120.9769375],
        'Quinta' => ['lat' => 14.595774, 'lng' => 120.9818737],
    ];
    
    public function index() 
    {
        $user = Auth::user(); // Get the currently authenticated user
        $station = $user->assigned_station;

        return view('map', compact('station'));
    }

    // Fetch the latest locations of the ferry aides
    public function getFerryAideLocations()
    {
        try {
            $latestLocations = FerryAideLocation::select('ferry_aide_locations.ferry_aide_id', 'ferry_aide_locations.latitude', 'ferry_aide_locations.longitude', 'boats.boat_name')
                ->join('boats', 'ferry_aide_locations.ferry_aide_id', '=', 'boats.id')
                ->whereIn('ferry_aide_locations.id', function ($query) {
                    $query->select(DB::raw('MAX(id)'))
                        ->from('ferry_aide_locations')
                        ->groupBy('ferry_aide_id');
                })
                ->get();

            return response()->json($latestLocations);
        } catch (\Exception $e) {
            Log::error("Error fetching ferry aide locations: " . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    // Get the assigned station coordinates for the current user
    public function getAssignedStation()
    {
        try {
            $user = Auth::user(); // Get the currently authenticated user
            $station = $user->assigned_station;

            // If the station is in the predefined list, return its coordinates
            if (array_key_exists($station, $this->stations)) {
                return response()->json($this->stations[$station]);
            }

            return response()->json(['error' => 'Assigned station not found'], 404);
        } catch (\Exception $e) {
            Log::error("Error fetching assigned station: " . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}

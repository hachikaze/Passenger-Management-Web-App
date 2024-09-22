<?php

namespace App\Http\Controllers;

use App\Models\FerryAideLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FerryAideLocationController extends Controller
{
    public function getFerryAideLocations()
    {
        try {
            // Fetch the latest 10 ferry aide locations
            $locations = FerryAideLocation::orderBy('created_at', 'desc')
                ->limit(10)
                ->get(['latitude', 'longitude', 'ferry_aide_id']);
                
            return response()->json($locations);
        } catch (\Exception $e) {
            // Handle and log the error
            Log::error("Error fetching ferry aide locations: " . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}


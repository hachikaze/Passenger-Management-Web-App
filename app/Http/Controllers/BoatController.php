<?php

namespace App\Http\Controllers;

use App\Models\Boat;
use App\Models\BoatStatusLog;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BoatController extends Controller
{
    public function index()
    {
        // Get today's date
        $today = Carbon::now()->format('Y-m-d');
        
        // Get the last date status was updated (assuming you store it in a 'settings' or similar table)
        $lastUpdateDate = Setting::where('key', 'last_boat_status_update')->value('value');

        // Check if the status has already been updated today
        if ($lastUpdateDate !== $today) {
            // Find all active boats except those under maintenance
            Boat::where('status', 'ACTIVE')
                ->where('status', '!=', 'MAINTENANCE')
                ->update(['status' => 'INACTIVE']);
            
            // Update the last status update date in the settings
            Setting::updateOrCreate(
                ['key' => 'last_boat_status_update'],
                ['value' => $today]
            );
        }

        $boats = Boat::all();
        return view('boats', compact('boats'));
    }

    public function updateStatus(Request $request)
    {
        $boat = Boat::find($request->id);
        if ($boat) {
            $boat->status = $request->status;
            $boat->save();

            BoatStatusLog::create([
                'boat_id' => $boat->id,
                'status' => $request->status,  // Use $request->status instead
                'date' => Carbon::now()->format('Y-m-d'),
            ]);

            return redirect()->route('boats')->with('success', 'Status updated successfully.');
        }

        return redirect()->route('boats')->with('error', 'Boat not found.');
    }

    public function addBoat(Request $request)
    {
        $attributes = $request->validate([
            'boat_name' => ['required', function($attribute, $value, $fail) {
                if ($value !== strtoupper($value)) {
                    $fail('The BOAT NAME must be in all capital letters.');
                }
            }],
            'max_capacity' => ['required'],
            'status' => ['required', 'in:ACTIVE,NOT ACTIVE,UNDER REPAIR']
        ]);

        $boat = new Boat($attributes);
        $boat->save();

        return redirect()->route('boats')->with('success', 'Boat added successfully.');
    }

    public function deleteBoat(Boat $boat)
    {
        $boat->delete();

        return redirect()->route('boats')->with('success', 'Boat deleted successfully.');
    }
}

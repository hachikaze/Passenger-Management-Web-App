<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StationSchedule extends Model
{
    use HasFactory;

    protected $table = "station_schedules";

    protected $guarded = [];

    public function manifests()
    {
        return $this->hasMany(PassengerManifest::class, 'origin', 'station_name');
    }

    public function riderships()
    {
        return $this->hasMany(Ridership::class, 'origin', 'station_name');
    }
}

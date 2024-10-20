<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PassengerManifest extends Model
{
    use HasFactory;

    protected $table = "passenger_manifest";

    protected $guarded = [];

    // Casting the UUID to string
    protected $casts = [
        'id' => 'string',  // Treat the UUID as a string
    ];

    // One-to-many relationship with Ridership
    public function ridership()
    {
        return $this->hasMany(\App\Models\Ridership::class, 'ridership_id_key', 'id');  // 'ridership_id_key' references the UUID in PassengerManifest
    }
}

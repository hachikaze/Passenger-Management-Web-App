<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ridership extends Model
{
    use HasFactory;

    protected $table = "ridership";

    protected $guarded = [];

    // Casting the UUID in ridership model to string
    protected $casts = [
        'ridership_id_key' => 'string',  // Treat the UUID as a string
    ];

    // BelongsTo relationship with PassengerManifest
    public function passengerManifest()
    {
        return $this->belongsTo(PassengerManifest::class, 'ridership_id_key', 'id');  // 'ridership_id_key' is the foreign key
    }
}

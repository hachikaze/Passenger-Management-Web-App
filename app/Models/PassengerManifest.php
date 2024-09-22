<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PassengerManifest extends Model
{
    use HasFactory;

    protected $table = "passenger_manifest";

    protected $guarded = [];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoatStatusLog extends Model
{
    use HasFactory;

    protected $table = "boat_status_logs";

    protected $guarded = [];
}

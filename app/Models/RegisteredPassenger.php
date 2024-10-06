<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisteredPassenger extends Model
{
    use HasFactory;

    protected $table = "registered_passenger";

    protected $guarded = [];
}

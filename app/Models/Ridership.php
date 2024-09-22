<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ridership extends Model
{
    use HasFactory;

    protected $table = "ridership";

    protected $guarded = [];
}

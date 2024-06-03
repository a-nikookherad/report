<?php

namespace App\Models\Instruments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ons extends Model
{
    use HasFactory;

    protected $table="ons";
    protected $fillable=[
        "open",
        "high",
        "low",
        "close",
        "date_time",
        "timestamp",
        "tarikh",
    ];
}

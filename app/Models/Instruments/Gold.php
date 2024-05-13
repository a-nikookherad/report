<?php

namespace App\Models\Instruments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gold extends Model
{
    use HasFactory;

    protected $table="golds";
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

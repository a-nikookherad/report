<?php

namespace App\Models\Instruments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gold18 extends Model
{
    use HasFactory;

    protected $table="golds_18";
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

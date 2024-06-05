<?php

namespace Trade\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    use HasFactory;
    protected $table="trade_journals";

    protected $fillable = [
        "instrument_name",
        "open_price",
        "close_price",
        "reason_for_open_order",
        "reason_for_close_order",
        "emotion",
        "started_at",
        "ended_at",
        "solar_started_at",
        "solar_ended_at",
        "trade_id",
        "tarikh",
        "date_time",
    ];
}

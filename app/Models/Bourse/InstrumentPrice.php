<?php

namespace App\Models\Bourse;

use Illuminate\Database\Eloquent\Model;

class InstrumentPrice extends Model
{
    protected $fillable = [
        "open",
        "high",
        "low",
        "close",
        "tarikh",
        "date_time",
        "timestamp",
        "instrument_id",
    ];

    public function instrument()
    {
        return $this->belongsTo(Instrument::class, "instrument_id");
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        "domestic_sales",
        "export_sales",
        "this_month_sales",
        "total_domestic_sales",
        "total_export_sales",
        "total_sales",
        "average_sales",
        "order",
        "instrument_id",
        "financial_period_id",
    ];

    public function instrument()
    {
        return $this->belongsTo(Instrument::class, "instrument_id");
    }

    public function financialPeriod()
    {
        return $this->belongsTo(FinancialPeriod::class, "financial_period_id");
    }
}

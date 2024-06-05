<?php

namespace Trade\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $table = "trade_account";
    protected $fillable = [
        "instrument_name",
        "total_amount",
        "backup_amount",
    ];
}

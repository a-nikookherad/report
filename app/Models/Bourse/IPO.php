<?php

namespace App\Models\Bourse;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IPO extends Model
{
    use HasFactory;

    protected $table = "ipo";
    protected $fillable = [
        "symbol",
        "price",
        "quantity",
        "success",
        "status",
        "body",
    ];
}

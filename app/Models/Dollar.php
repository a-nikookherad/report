<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dollar extends Model
{
    use HasFactory;
    protected $fillable=[
      "open",
      "high",
      "low",
      "close",
      "date_time",
    ];
}
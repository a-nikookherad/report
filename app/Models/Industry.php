<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "description",
    ];

    public function instruments()
    {
        return $this->hasMany(Instrument::class,"industry_id");
    }
}

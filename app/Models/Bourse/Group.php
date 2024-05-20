<?php

namespace App\Models\Bourse;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "description",
    ];

    public function instrument()
    {
        return $this->hasMany(Instrument::class, "group_id");
    }
}

<?php

namespace Trade\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NapoleonHill extends Model
{
    use HasFactory;

    protected $table = "trade_think_and_grow_rich";
    protected $fillable = [
        "how_many_you_want",
        "cost_of_your_request",
        "date_for_request",
        "plan_for_request",
        "conclude_every_fifth_steps",
    ];
}

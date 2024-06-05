<?php

namespace Trade\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Strategy extends Model
{
    use HasFactory;

    protected $table = "trade_strategies";
    protected $fillable = [
        "name",
        "conditions_for_enter",
        "where_exit_if_win",
        "where_exit_if_loss",
        "type_of_strategy",
        "technicals",
        "description",
    ];
}

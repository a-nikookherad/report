<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Bourse\Instrument;
use Illuminate\Http\Request;

class RatioController extends Controller
{
    public function show(Instrument $instrument)
    {
        //todo we need to gather all data here
        return view("contents.instruments.ratio", compact("instrument"));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Colour;

class ColourController extends Controller
{
    public function index()
    {
        $colours = Colour::all();
        return view('colours.index', compact('colours'));
    }

    public function getAllColours()
    {
        $colours = Colour::all();
        return $colours;
    }
}

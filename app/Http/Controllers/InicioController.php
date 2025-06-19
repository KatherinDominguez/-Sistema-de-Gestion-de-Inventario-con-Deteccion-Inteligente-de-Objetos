<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Objeto;

class InicioController extends Controller
{
    public function index()
    {
        $objetos = Objeto::all();
        return view('inicio', compact('objetos'));
    }
}

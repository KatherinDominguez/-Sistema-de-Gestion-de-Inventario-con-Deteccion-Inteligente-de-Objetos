<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Objeto;
class ObjetoController extends Controller
{
    public function index()
    {
        $objetos = Objeto::all();
        return view('objetos.index', compact('objetos'));
    }
    public function create()
    {
        return view('objetos.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'forma' => 'required',
            'color' => 'required',
        ]);
        Objeto::create($request->all());
        return redirect()->route('objetos.index')->with('success', 'Objeto registrado.');
    }
}

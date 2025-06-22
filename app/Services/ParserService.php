<?php

namespace App\Services;

use App\Librerias\MiParseador;
use App\Models\Objeto;

class ParserService
{
    private $parser;

    public function __construct()
    {
        $this->parser = new MiParseador();
        $this->cargarLexicoYGramatica();
        $this->definirComandos();
        $this->parser->build();
    }

    private function cargarLexicoYGramatica()
    {
        $lexico = <<<LEX
        PALABRA: [a-zA-ZáéíóúÁÉÍÓÚñÑ]+
        SKIP: [\s]+
        LEX;

        $gramatica = <<<GRAM
        COMANDO -> ACCION OBJETO COLOR
        COMANDO -> ACCION OBJETO
        ACCION -> PALABRA
        OBJETO -> PALABRA
        COLOR -> PALABRA
        GRAM;

        $this->parser->setLexico($lexico);
        $this->parser->setGramatica($gramatica);
    }

    private function definirComandos()
    {
        // Subir archivo
        $this->parser->agregarComando('subir', function () {
            session(['comando_voz' => 'subir']);
        });

        // Identificar objeto con o sin color
        $this->parser->agregarComando('identificar', function ($nombre, $color = null) {
            $nombre = strtolower($nombre);
            $color = $color ? strtolower($color) : null;

            // Si solo se dice "identificar Coca", buscar en la BD su color automáticamente
            if (!$color) {
                $objeto = Objeto::whereRaw('LOWER(nombre) = ?', [$nombre])->first();
                if ($objeto) {
                    $color = strtolower($objeto->color);
                }
            }

            session([
                'comando_voz' => 'identificar',
                'nombre' => $nombre,
                'color' => $color
            ]);
        });
    }

    public function interpretar($texto)
    {
        try {
            $texto = strtolower($texto);
            $this->parser->parsearYejecutar($texto);
        } catch (\Exception $e) {
            session(['comando_voz' => 'ninguno']);
        }
    }
}

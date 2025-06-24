<?php
/**
 * Clase ParserService
 *
 * Esta clase implementa el patrón de diseño "Service Layer" (Capa de Servicios).
 * Se encarga de encapsular la lógica de negocio relacionada con la interpretación
 * de comandos por voz usando el parser personalizado MiParseador.
 *
 * Responsabilidades:
 * - Definir y cargar el léxico y la gramática.
 * - Registrar comandos y asociarles acciones.
 * - Interpretar texto ingresado y ejecutar la acción correspondiente.
 * - Interactuar con el modelo Objeto para completar información faltante.
 *
 * Este patrón permite mantener los controladores ligeros y enfocados en el flujo
 * HTTP, delegando la lógica de negocio a servicios reutilizables.
 *
 * Patrón aplicado: Service Layer (Martin Fowler)
 */

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
        COMANDO -> ACCION OBJETO 
        COMANDO -> ACCION
        ACCION -> PALABRA
        OBJETO -> PALABRA
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
        $this->parser->agregarComando('reiniciar', function () {
            session(['comando_voz' => 'reiniciar']);
        });
        $this->parser->agregarComando('abrir', function ($destino) {
        $destino = strtolower($destino);
        $ruta = null;

        switch ($destino) {
            case 'inventario':
                $ruta = route('inventario');
                break;
            case 'reportes':
                $ruta = route('reportes.index');
                break;
            case 'objetos':
                $ruta = route('objetos.index');
                break;
        }

        if ($ruta) {
            session(['comando_voz' => 'redirigir', 'url' => $ruta]);
        } else {
            session(['comando_voz' => 'ninguno']);
        }
        });
    }

    public function interpretar($texto)
    {
        try {
            $texto = strtolower($texto);
            $this->parser->parsearYejecutar($texto);
        } catch (\Exception $e) {
            \Log::error('Parser error: ' . $e->getMessage());
            session(['comando_voz' => 'ninguno']);
        }
    }
}

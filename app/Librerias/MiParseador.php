<?php

namespace App\Librerias;

class MiParseador {
    private $comandos = [];

    public function __construct() {
        // No necesitamos Parle para comandos simples de voz
    }

    public function setLexico(string $texto) {
        // Método vacío - no necesario para esta implementación simple
    }

    public function setGramatica(string $texto) {
        // Método vacío - no necesario para esta implementación simple
    }

    public function build() {
        // Método vacío - no necesario para esta implementación simple
    }

    public function agregarComando(string $comando, callable $funcion) {
        $this->comandos[strtolower($comando)] = $funcion;
    }

    public function parsear(string $texto): array {
        // Limpiar y separar palabras
        $texto = strtolower(trim($texto));
        $texto = preg_replace('/[^\p{L}\s]/u', '', $texto);
        return array_filter(explode(' ', $texto));
    }

    public function parsearYejecutar(string $texto) {
        $palabras = $this->parsear($texto);
        $this->ejecutarComando($palabras);
    }

    public function ejecutarComando(array $palabras) {
        if (empty($palabras)) {
            throw new \Exception("Nada para ejecutar");
        }

        // Buscar el comando en las palabras
        $comando = null;
        $indice = -1;
        
        foreach ($palabras as $i => $palabra) {
            if (array_key_exists($palabra, $this->comandos)) {
                $comando = $palabra;
                $indice = $i;
                break;
            }
        }

        if (is_null($comando)) {
            throw new \Exception("No se encontró un comando válido en la entrada.");
        }

        // Obtener los argumentos después del comando
        $args = array_slice($palabras, $indice + 1);
        
        // Ejecutar el comando con sus argumentos
        call_user_func_array($this->comandos[$comando], $args);
    }
    
    public function esValido(string $texto): bool {
        try {
            $palabras = $this->parsear($texto);
            foreach ($palabras as $palabra) {
                if (array_key_exists($palabra, $this->comandos)) {
                    return true;
                }
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function tablaDeComandos() {
        return array_keys($this->comandos);
    }

    public function mostrar() {
        print_r($this->comandos);
    }
}
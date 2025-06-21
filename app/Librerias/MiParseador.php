<?php
namespace App\Librerias;
use Parle\{Lexer, Token, Parser, ParserException};
class MiParseador {
    private $parser;
    private $lexer;
    private $comandos = [];
    private $producciones = [];
    public function __construct() {
        $this->parser = new Parser();
        $this->lexer = new Lexer();
    }
    public function setLexico(string $texto) {
        $lineas = explode("\n", $texto);
        foreach ($lineas as $linea) {
            $linea = trim($linea);
            if (!$linea) continue;
            [$nombre, $regex] = explode(":", $linea, 2);
            $nombre = trim($nombre);
            $regex = trim($regex);
            if ($nombre === "SKIP") {
                $this->lexer->push($regex, Token::SKIP);
            } else {
                $this->parser->token($nombre);
                $this->lexer->push($regex, $this->parser->tokenId($nombre));
            }
        }
    }
    public function setGramatica(string $texto) {
        $lineas = explode("\n", $texto);
        foreach ($lineas as $linea) {
            $linea = trim($linea);
            if (!$linea) continue;
            [$izq, $der] = explode("->", $linea);
            $izq = trim($izq);
            $der = trim($der);
            $prodId = $this->parser->push($izq, $der);
            $this->producciones[$izq] = $prodId;
        }
    }
    public function mostrar(){
        print_r($this->producciones);
    }
    public function build() {
        $this->parser->build();
        $this->lexer->build();
    }
    public function tablaDeComandos(){
        return array_keys($this->comandos);
    }
    public function agregarComando(string $comando, callable $funcion) {
        $this->comandos[strtolower($comando)] = $funcion;
    }
    public function parsear(string $texto): array {
        $palabras = [];
        $this->parser->consume($texto, $this->lexer);
        do {
            switch ($this->parser->action) {
                case Parser::ACTION_ERROR:
                    throw new ParserException("No cumple con la gramática");
                case Parser::ACTION_REDUCE:
                    $prodLen = $this->parser->sigilCount();
                    if($prodLen == 1){
                        for ($i = 0; $i < $prodLen; ++$i) {
                            $valor = $this->parser->sigil($i);
                            if (!is_null($valor)) {
                                $palabras[] = $valor;
                            }
                        }
                    }
                break;
            }
            $this->parser->advance();
        } while ($this->parser->action !== Parser::ACTION_ACCEPT);
        return $palabras;
    }
    public function parsearYejecutar(string $texto) {
        $comandos = explode(";", $texto);
        foreach ($comandos as $comandoTexto) {
            $palabras = $this->parsear(trim($comandoTexto));
            $this->ejecutarComando($palabras);
        }
    }
    public function ejecutarComando(array $palabras) {
        if (empty($palabras)) {
            throw new Exception("Nada para ejecutar");
        }
        $claves = array_keys($this->comandos);
        $comando = null;
        foreach ($palabras as $i => $palabra) {
            if (in_array(strtolower($palabra), $claves)) {
                $comando = strtolower($palabra);
                $indice = $i;
                break;
            }
        }
        if (is_null($comando)) {
            throw new Exception("No se encontró un comando válido en la entrada.");
        }
        $args = array_slice($palabras, $indice + 1);
        call_user_func_array($this->comandos[$comando], $args);
    }
    
    public function esValido(string $texto): bool {
        try {
            $this->parser->consume($texto, $this->lexer);
            do {
                if ($this->parser->action === Parser::ACTION_ERROR) return false;
                $this->parser->advance();
            } while ($this->parser->action !== Parser::ACTION_ACCEPT);
            return true;
        } catch (ParserException $e) {
            return false;
        }
    }
}

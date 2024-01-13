<?php 

namespace Model;

class Cita extends ActiveRecord{
    // Base de datos 
    protected static $tabla = 'citas';
    protected static $columnasDB = ['id', 'fecha', 'hora', 'usuarioId'];

    public $id;
    public $fecha;
    public $hora;
    public $usuarioId;

    public function __construct($args = []) { 
        // __construct() Es un metodo especial que es llamado cuando un objeto es creado a partir de una clase 
        // $args
        $this->id = $args['id'] ?? null;
        $this->fecha = $args['fecha'] ?? '';
        $this->hora = $args['hora'] ?? '';
        $this->usuarioId = $args['usuarioId'] ?? '';
    
    }   
}

?>
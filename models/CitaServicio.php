<?php 

namespace Model;

class CitaServicio extends ActiveRecord {
    // Base de Datos
    protected static $tabla = 'citasServicios';
    protected static $columnasDB  = ['id', 'citaId', 'servicioId'];

    public $id;
    public $citaId;
    public $servicioId;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null; // '??' Null Coalescing Operator
        $this->citaId = $args['citaId'] ?? '';
        $this->servicioId = $args['servicioId'] ?? '';
    }
}


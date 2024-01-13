<?php 

namespace Controllers;

use Model\CitaServicio;
use Model\Cita;
use Model\Servicio;

class APIController {
    public static function index() {
        $servicios = Servicio::all();
        echo json_encode($servicios);
    }

    public static function guardar() {
        
        // Almacena la Cita y devuelve el ID
        $cita = new Cita($_POST);
        $resultado = $cita->guardar();

        $id = $resultado['id'];
        
        // Almacena los Servicios con el ID de la cita
        $idServicios = explode(",", $_POST['servicios']);
        // explode() separa los elementos de un arreglo y los convierte a string, .split() es el equivalente en JS

        foreach ($idServicios as $idServicio) {
            $args = [
                'citaId' => $id,
                'servicioId' => $idServicio
            ];
            $citaServicio = new CitaServicio($args);
            $citaServicio->guardar();
        }

        // Retornamos una respuesta
        echo json_encode(['resultado' => $resultado]);
    }

    public static function eliminar(){
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Este código solo se ejecuta si el método POST está activo
            $id = $_POST['id']; // 1. Se lee el ID
            $cita = Cita::find($id); // 2. Se encuentra
            $cita->eliminar(); // 3. Se elimina

            header('Location:' . $_SERVER['HTTP_REFERER']);
        }
    }
}

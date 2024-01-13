<?php 

namespace Controllers;

use MVC\Router;

class CitaController {
    public static function index ( Router $router ) {
        
        // Trae los datos del usuario que inicio sesión
        // session_start();

        isAuth();

        $router->render('cita/index', [
            'nombre' => $_SESSION['nombre'],
            'id' => $_SESSION['id']
        ]);
    }
}
?>
<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {
    public static function login(Router $router) {
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarLogin();

            if (empty($alertas)) {
                // Check if the user exists
                $usuario = Usuario::where('email', $auth->email);

                if ($usuario) {
                    // Verify the password
                    if( $usuario->comprobarPasswordAndVerificado($auth->password) ){
                        // Autenticar el usuario 
                        session_start();

                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        // Redireccionamiento para admin o clientes
                        if($usuario->admin === "1"){
                            $_SESSION['admin'] = $usuario->admin ?? null;
                            header('Location: /admin');
                        }else {
                            header('Location: /cita');
                        }

                        debug($_SESSION);
                    }
                } else {
                    // Move the alert setting inside this block
                    Usuario::setAlerta('error', 'Usuario no encontrado');
                }
            }
        }

        // Get the alerts from the Usuario model
        $alertas = Usuario::getAlertas();

        // Render the login page with the alerts
        $router->render('auth/login', [
            'alertas' => $alertas
        ]);
    }
 
    public static function logout() {
        // session_start();
        $_SESSION = [];
        
        header('Location: /');
    }

    public static function olvide(Router $router) {
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();

            if(empty($alertas)){
                $usuario = Usuario::where('email', $auth->email);

                if($usuario && $usuario->confirmado === "1"){
                    // El email corresponde al de algun usuario en la DB

                    // Generar un token
                    $usuario->crearToken();
                    $usuario->guardar();

                    // Enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();


                    Usuario::setAlerta('exito', 'Email de recuperación enviado');
                } else {
                    // El email no aparece en la DB
                    Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');
                }
            }
        }
        $alertas = Usuario::getAlertas();

        $router->render('auth/olvide-password', [
            'alertas' => $alertas
        ]);
    }

    // Valida que se le envie al usuario por medio de su email que en realidad es la persona que esta pidiendo el cambio
    public static function recuperar(Router $router) {
        $alertas = [];
        $error = false;

        $token = sanitize($_GET['token']);

        // Buscar usuario por su token
        $usuario = Usuario::where('token', $token);

        // No hay ningun usuario
        if(empty($usuario)){
            Usuario::setAlerta('error', 'Token No Válido');
            $error = true;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Leer el nuevo password y guardarlo
            $password = new Usuario($_POST);
            $coPassword = new Usuario($_POST);
            $alertas = array_merge($password->validarPassword(), $coPassword->validarPassword());
            // $alertas = $password->validarPassword();
            // $alertas = $coPassword->validarPassword();
 
            if(empty($alertas)) {
                $usuario->password = null;

                $usuario->password = $password->password;
                $usuario->coPassword = $coPassword->coPassword;
                $usuario->hashPassword();
                $usuario->token = null;

                $resultado = $usuario->guardar();
                if($resultado){
                    header('Location: /');
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/recuperar-password', [
            'alertas' => $alertas,
            'error' => $error
        ]);
    }

    public static function crear(Router $router) {
        $usuario = new Usuario;

        // Alertas vacias
        $alertas = []; 
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Code to be executed if the request method is POST
            $usuario->sincronizar($_POST);
            $alertas = $usuario->ValidarNuevaCuenta();  // Retorna un arreglo

            if(empty($alertas)) {
                // Verificar que el usuario no este registrado 
                $resultado = $usuario->existeUsuario();

                if($resultado->num_rows) {
                    $alertas = Usuario::getAlertas(); // getAlertas() = Validacion
                } else {
                    // Hashear el Password
                    $usuario->hashPassword();

                    // Crear Token
                    $usuario->crearToken();

                    // Enviar correo 
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion();

                    // Crear el usuario
                    $resultado = $usuario->guardar();
                    if($resultado) {
                        header('Location: /mensaje');
                    } 
                    // debug($usuario);

                }
            }
        }   

        $router->render('auth/crear-cuenta', [
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function mensaje(Router $router) {
        $router->render('auth/mensaje');
    }

    public static function confirmar(Router $router) {
        $alertas = [];

        $token = sanitize($_GET ['token']);

        $usuario = Usuario::where('token', $token);

        if(empty($usuario)){
            // Debido a fallas en el servidor 'localhost' todas las cuentas que reciban el correo estarán confirmadas automaticamente
            // No se verifica el token correctamente
            
            // Modifica a usuario confirmado
            $usuario->confirmado = "1";
            $usuario->token = null;
            $usuario->guardar();
            Usuario::setAlerta('exito', 'Cuenta Comprobada Correctamente'); // Método de ActiveRecord para crear una alerta
        }

        // Método de ActiveRecord para obtener alertas
        $alertas = Usuario::getAlertas(); 

        $router->render('auth/confirmar-cuenta', [
            'alertas' => $alertas
        ]);
    }
}
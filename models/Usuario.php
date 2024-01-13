<?php 

namespace Model;

class Usuario extends ActiveRecord {
    // Base de  Datos
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'apellido','email', 
    'password', 'coPassword', 'telefono', 'admin', 'confirmado', 'token'];

    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $coPassword;
    public $telefono;
    public $admin;
    public $confirmado;
    public $token;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->coPassword = $args['coPassword'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->admin = $args['admin'] ?? '0';
        $this->confirmado = $args['confirmado'] ?? '0';
        $this->token = $args['token'] ?? '';
    }

    // Mensajes de validación para la creación de una cuenta 
    public function ValidarNuevaCuenta() {
    // Para 'public' es necesario crear un objeto para llamar al objeto (ValidarNuevaCuenta)
    $etiquetas = [
        'nombre' => 'El Nombre',
        'apellido' => 'El Apellido',
        'telefono' => 'El Teléfono',
        'password' => 'El Password',
    ];
    
    foreach ($etiquetas as $campo => $etiqueta) {
        if (!$this->{$campo}) {
            self::$alertas['error'][] = "$etiqueta es obligatorio";
        }
    }

        if(!$this->coPassword) {
            self::$alertas['error'][] = 'Confirma tu Password';
        }
        if($this->password !== $this->coPassword) {
            self::$alertas['error'][] = "Los passwords no coinciden";
        } 
        if(strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El Password debe contener al menos 6 caracteres';
        }

        return self::$alertas;
    } 

    // Mensajes de validacion para el Login
    public function validarLogin() {
        if(!$this->email){
            self::$alertas['error'][] = 'El email es obligatorio';
        }
        if(!$this->password){
            self::$alertas['error'][] = 'El password es obligatorio';
        }

        return self::$alertas;
    }

    // Mensaje de validacion para olvide Password
    public function validarEmail() {
        if(!$this->email){
            self::$alertas['error'][] = "El email es obligatorio";
        }
        return self::$alertas;
    } 

    public function validarPassword() {
        if(!$this->password){
            self::$alertas['error'][] = " El password es obligatorio";
        }

        if(!$this->coPassword) {
            self::$alertas['error'][] = 'Confirma tu Password';
        }

        if(strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El Password debe contener al menos 6 caracteres';
        }

        if($this->password !== $this->coPassword) {
            self::$alertas['error'][] = "Los passwords no coinciden";
        } 
        return self::$alertas;
    }

    // Revisa si el usuario ya existe
    public function existeUsuario() {
        // Se escribe el query para consultar la DB
        $query = "SELECT * FROM " . self::$tabla . " WHERE email = '" . $this->email . "'  LIMIT 1";
        
        // Se consulta la base de datos
        $resultado = self::$db->query($query);

        // Si el usuario ya esta registrado, se agrega a las alertas
        if($resultado->num_rows) {
            self::$alertas['error'][] = 'El Usuario ya esta registrado';
        }

        // Se retorna el resultado
        return $resultado;
    }

    public function hashPassword() {
        $hashedPassword = password_hash($this->password, PASSWORD_BCRYPT);

        // Asigna el mismo hash a ambas contraseñas
        $this->password = $hashedPassword;
        $this->coPassword = $hashedPassword;
    }

    public function crearToken() {
        $this->token = uniqid();
    }

    public function comprobarPasswordAndVerificado($password) {
        $resultado = password_verify($password, $this->password);
        
        if (!$resultado || !$this->confirmado) {
            self::$alertas['error'][] = "Password Incorrecto o tu cuenta no ha sido confirmada";
        } else {
            return true; // Password correct and account confirmed
        }
    }
}

?>
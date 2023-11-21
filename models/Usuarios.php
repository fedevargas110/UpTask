<?php 

namespace Model;

class Usuarios extends ActiveRecord {
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'email', 'password', 'token', 'confirmado'];

    public $id;
    public $nombre;
    public $email;
    public $password;
    public $token;
    public $confirmado;
    public $password2;
    public $password_actual;
    public $password_nueva;


    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->password_actual = $args['password_actual'] ?? '';
        $this->password_nueva = $args['password_nuevo'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? 0;
    }

    // Validar el login de usuarios
    public function validandoLogin() {
        if(!$this->email) {
            self::$alertas['error'][] = 'El Email del Usuario es Obligatorio';
        }

        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'Email no Válido';
        }
        
        if(!$this->password) {
            self::$alertas['error'][] = 'El campo del password no puede estar vacio';
        }
        return self::$alertas;
    }
    // Validacion para Cuentas Nuevas
    public function validarNuevaCuenta() {
        if(!$this->nombre) {
            self::$alertas['error'][] = 'El Nombre del Usuario es Obligatorio';
        }

        if(!$this->email) {
            self::$alertas['error'][] = 'El Email del Usuario es Obligatorio';
        }

        if(!$this->password) {
            self::$alertas['error'][] = 'El campo del password no puede estar vacio';
        }

        if(strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El password debe contener al menos 6 caracteres';
        }

        if($this->password !== $this->password2) {
            self::$alertas['error'][] = 'Ambas contraseñas deben ser iguales';
        }
        return self::$alertas;
    }

    // Vaidar el password
    public function validarPassword() {
        if(!$this->password) {
            self::$alertas['error'][] = 'El campo del password no puede estar vacio';
        }

        if(strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El password debe contener al menos 6 caracteres';
        }
        return self::$alertas;  
    }

    public function comprobarPassword() : bool {
        return password_verify($this->password_actual, $this->password);
    }

    // hashea el pass
    public function hashPassword() {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    // Generar el token
    public function crearToken() {
        $this->token = uniqid();
    }

    // Validar email
    public function validarEmail() {
        if(!$this->email) {
            self::$alertas['error'][] = 'El email es Obligatorio';
        }

        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'Email no Válido';
        }

        return self::$alertas;
    }

    // Validar Perfil
    public function validar_perfil() {
        if( !$this->nombre ) {
            self::$alertas['error'][] = 'El nombre es Obligatorio';
        }
        if( !$this->email ) {
            self::$alertas['error'][] = 'El email es Obligatorio';
        }
        return self::$alertas;
    }

    public function password_nuevo() {
        if(!$this->password_actual) {
            self::$alertas['error'][] = 'La Contraseña Actual no puede estar vacio';
        }
        if(!$this->password_nueva) {
            self::$alertas['error'][] = 'La Contraseña Nueva no puede estar vacio';
        }
        if(strlen($this->password_nueva) < 6) {
            self::$alertas['error'][] = 'La Contraseña Nueva debe contener no menos de 6 caracteres';
        }
        return self::$alertas;
    }
}
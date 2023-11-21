<?php 

namespace Controllers;

use Classes\Email;
use Model\Usuarios;
use MVC\Router;

class LoginController {
    public static function login(Router $router) {
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuarios($_POST);

            $alertas = $auth->validandoLogin();

            if(empty($alertas)) {
                // Verificar que el usuario exista
                $usuario = Usuarios::where('email', $auth->email);

                if(!$usuario || !$usuario->confirmado) {
                    Usuarios::setAlerta('error', 'El usuario no existe o no esta confirmado');
                } else {
                    // El usuario existe
                    if(password_verify($_POST['password'], $usuario->password)) {
                        // Password correcto -> inicia sesion
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        // Redireccionar 
                        header('Location: /dashboard');


                    } else {
                        Usuarios::setAlerta('error', 'Password Incorrecta');
                    }
                }
            }
        }

        $alertas = Usuarios::getAlertas();
        // Render a la vista 
        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesion', // Titulos dinamicos
            'alertas' => $alertas
        ]);
    }

    public static function logout() {
        session_start();
        $_SESSION = [];
        header('Location: /');
    }

    public static function crear(Router $router) {
        $alertas = [];
        $usuario = new Usuarios;

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            if(empty($alertas)) {
                $existeUsuario = Usuarios::where('email', $usuario->email);

                if($existeUsuario) {
                    Usuarios::setAlerta('error', 'El usuario ya esta registrado');
                    $alertas = Usuarios::getAlertas();
                } else {
                    // hashear el password
                    $usuario->hashPassword();

                    // Eliminar pass2
                    unset($usuario->password2);

                    // Generar el token
                    $usuario->crearToken();

                   // Crear un nuevo usuario
                    $resultado = $usuario->guardar();

                    // Enviar Email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion();

                    if($resultado) {
                        header('Location: /mensaje');
                    }
                }
            }
            
        }
        // Render a la vista
        $router->render('auth/crear', [
            'titulo' => 'Crea tu cuenta en UpTask',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function olvide(Router $router) {
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuarios($_POST);
            $alertas = $usuario->validarEmail();

            if(empty($alertas)) {
                // Buscar el usuario
                $usuario = Usuarios::where('email', $usuario->email);

                if($usuario && $usuario->confirmado) {
                    // Generar un nuevo token
                    $usuario->crearToken();
                    unset($usuario->password2);

                    // Actualizar el usuario
                    $usuario->guardar();

                    // Enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();


                    // Imprimir la alerta
                    Usuarios::setAlerta('exito', 'Hemos enviado las instrucciones a tu email');
                } else {
                    Usuarios::setAlerta('error', 'El usuario no existe o no esta confirmado');
                    
                }
            }
        }
        $alertas = Usuarios::getAlertas();

        // Render a la vista
        $router->render('auth/olvide', [
            'titulo' => 'Olvidaste tu Password',
            'alertas' => $alertas
        ]);
    }

    public static function restablecer(Router $router) {
        
        $token = s($_GET['token']);
        $mostrar = true;

        if(!$token) header('Locaton: /');

        // Identificar el usuario cn este token
        $usuario = Usuarios::where('token', $token);

        if(empty($usuario)) {
            Usuarios::setAlerta('error', 'El token del usuario no es valido');
            $mostrar = false;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Añadir en nuevo pass
            $usuario->sincronizar($_POST);

            // Validar el pass
            $alertas = $usuario->validarPassword();

            if(empty($alertas)) {
                // hash nuevo pass
                $usuario->hashPassword();

                // Eliminar token
                $usuario->token = null;

                // Guardar usuario actualizado en base de datos
                $resultado = $usuario->guardar();

                // Redireccionar a iniciar sesion
                if($resultado) {
                    header('Location: /');
                }
            }
        }

        $alertas = Usuarios::getAlertas();
        // Render a la vista
        $router->render('auth/restablecer', [
            'titulo' => 'Restablecer Password',
            'alertas' => $alertas,
            'mostrar' => $mostrar
        ]);
    }

    public static function mensaje(Router $router) {
        $router->render('auth/mensaje', [
            'titulo' => 'Activa tu cuenta'
        ]);
    }

    public static function confirmar(Router $router) {

        $token = s($_GET['token']);

        if(!$token) header('Location: /');

        // Encontrar al usuario con el token
        $usuario = Usuarios::where('token', $token);
        
        if(empty($usuario)) {
            // No se encontro un suario con ese token
            Usuarios::setAlerta('error', 'Token no válido');
        }else {
            // Confirmar la cuenta
            $usuario->confirmado = 1;
            $usuario->token = null;
            unset($usuario->password2);
            
            // Guardar en la base de datos
            $usuario->guardar();

            Usuarios::setAlerta('exito', 'Cuenta Confirmada con exito');
        }

        $alertas = Usuarios::getAlertas();


        $router->render('auth/confirmar', [
            'titulo' => 'Confirma tu Cuenta UpTask',
            'alertas' => $alertas
        ]);
    }
}

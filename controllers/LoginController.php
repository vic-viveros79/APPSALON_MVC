<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {
    public static function login(Router $router) {
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);

            $alertas = $auth->validarLogin();

            if(empty($alertas)) {
                //comprobar que existe el usuario
                $usuario = Usuario::where('email', $auth->email);
                
                if ($usuario) {
                    //verificar el paswword
                   if($usuario->comprobarPasswordAndVerificado($auth->password)) {
                    //Autenticar el usuario
                    if(!isset($_SESSION)) {
                        session_start();
                   };
                    

                    $_SESSION['id'] = $usuario->id;
                    $_SESSION['nombre'] = $usuario->nombre ." ".$usuario->apellido;
                    $_SESSION['email'] = $usuario->email;
                    $_SESSION['login'] = true;

                    //redireccionamiento
                    if ($usuario->admin === "1") {
                        $_SESSION['admin'] = $usuario->admin ?? null;

                        header('Location: /admin');
                    }else {
                        header('Location: /cita');
                    }

                    
                   }
                } else {
                    Usuario::setAlerta('error', 'Usuario no encontrado');
                }
                
            }
            //debuguear($auth);
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/login', [
            'alertas' => $alertas
        ]);
    }

    public static function logout() {
        if (!$_SESSION['nombre']) {
            session_start();
          }
        
        $_SESSION = [];
        
        header('Location:/');
    }

    public static function olvide(Router $router) {

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();

            if(empty($alertas)) {
                $usuario = Usuario::where('email', $auth->email);

                if($usuario && $usuario->confirmado === "1") {
                    //Generar un token
                    $usuario->crearToken();
                    $usuario->guardar();
                    //enviar email
                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
                    $email->enviarInstrucciones();
                    //alerta
                    Usuario::setAlerta('exito', 'Revisa tu email');
                } else {
                    Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');
                    
                }
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/olvide-password',[
            'alertas' => $alertas
        ]);
    }

    public static function recuperar(Router $router) {
        $alertas = [];
        $error = false;

        $token = s($_GET['token']);

        //buscar usuario por su token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)) {
            Usuario::setAlerta('error', 'Token no valido');
            $error = true;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            //Leer el nuevo password y guardarlo

            $password = new Usuario($_POST);
            $alertas = $password->validarPassword();

            if(empty($alertas)) {
                $usuario->password = null;

                $usuario->password = $password->password;
                $usuario->hashPassword();
                $usuario->token = null;

                $resultado = $usuario->guardar();
                if($resultado) {
                    header('Location: /');
                }
                
            }

            //debuguear($password);
        }

        

        $alertas = Usuario::getAlertas();
        $router->render('auth/recuperar-password',[
            'alertas' => $alertas ,
            'error' => $error
        ]);
    }

    public static function crear(Router $router) {
        $usuario = new Usuario;

        //alertas vacias
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST') {             
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta(); 
            
            //revisar que alertas este vacio
            if(empty($alertas)) {
                $resultado = $usuario->existeUsuario();

                if($resultado->num_rows) {
                    $alertas = Usuario::getAlertas();
                } else {
                    //Hashear el password
                    $usuario->hashPassword(); 
                    //Generar un token unico
                    $usuario->crearToken();
                    //Enviar el email
                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);

                    $email->enviarConfirmacion();

                    //crear usuario
                    $resultado = $usuario->guardar();
                    if($resultado) {
                        header('Location: /mensaje');
                    }

                   // debuguear($usuario);


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

        $token = s($_GET['token']);

        $usuario = Usuario::where('token', $token);

        if (empty($usuario)) {
            //Mensaje de error
            Usuario::setAlerta('error', 'Token no valido');
        } else {
            //Modificar usuario
            $usuario->confirmado = '1';
            $usuario->token = 'null';
            $usuario->guardar();
            Usuario::setAlerta('exito', 'Cuenta validada');
        }
        

        //debuguear($usuario);
        //Obtener alertas
        $alertas = Usuario::getAlertas();
        //Renderizar la vista
        $router->render('auth/confirmar-cuenta', [
            'alertas' => $alertas 
        ]);
    }

    public static function landing(Router $router) {
        $router->renderland('auth/landing');
    }
}
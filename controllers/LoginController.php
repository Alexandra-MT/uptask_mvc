<?php

namespace Controllers;

use MVC\Router;
use Classes\Email;
use Model\Usuario;

class LoginController{
    public static function login(Router $router){
        $alertas = [];
        //$auth = new Usuario;
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            //sincronizamos con post
            $usuario = new Usuario($_POST);
            //$auth->sincronizar($_POST);

            //validamos
            $alertas = $usuario->validarLogin();

            if(empty($alertas)){
                //comprobar si existe el usuario
                $usuario = Usuario::where('email' , $usuario->email);
                
                if(!$usuario || !$usuario->confirmado){
                    Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');
                }else{
                    //el usuario existe
                    if(password_verify($_POST['password'], $usuario->password)){
                        //inicar sesión
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;
                        //debuguear($_SESSION);

                        //redireccionar 
                        header('Location:/dashboard');
                    }else{
                        Usuario::setAlerta('error', 'Password incorrecto');
                    }
                }
            
            }
       }
       $alertas = Usuario::getAlertas();

       $router->render('auth/login', [
            'titulo' => 'Iniciar sesión',
            'alertas' => $alertas
       ]);
    }

    public static function logout(Router $router){
        //iniciar session
        session_start();
        $_SESSION = []; //limpiando valores
        header('Location: /');

    }

    public static function crear(Router $router){
        $usuario = new Usuario();
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            //se podria validar los password aqui pero no es recomendable
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarCuentaNueva();
            //debuguear($alertas);

            if(empty($alertas)){
            //no se puede utilizar el objeto $usuario porque lo va a reescribir
                $existeUsuario = Usuario::where('email', $usuario->email);
                if($existeUsuario){
                    Usuario::setAlerta('error', 'El usuario ya esta registrado');
                }else{
                    //crear un nuevo usuario,

                    //hashear su password
                    $usuario->hashPassword();

                    //ELIMIAR PASSWORD2, no lo requerimos
                    unset($usuario->password2);

                    //generar token
                    $usuario->token();

                    //$usuario->confirmar sera 0
               
                    //guardar el usuario en la bbdd
                    $resultado = $usuario->guardar();
                    

                    //enviar email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion();
                    
                    if($resultado){
                        header('Location: /mensaje');
                    }
                }
            }
        }
        $alertas = Usuario::getAlertas();

       $router->render('auth/crear', [
        'titulo' => 'Crea tu cuenta',
        'usuario' => $usuario,
        'alertas' => $alertas
         ]);
    }

    public static function olvide(Router $router){
        $alertas = [];

       if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();

            if(empty($alertas)){
                $usuario = Usuario::where('email', $usuario->email);
                if($usuario && $usuario->confirmado) {//=== "1" dara true ==1)
                   //generar nuevo token
                    unset($usuario->password2); //es opcional quitarlo ya que en las columnasDB no aparece, no dara error al guardar en ddbb
                    $usuario->token();
                    //actualizar usuario
                    $usuario->guardar();
                    //enviar un email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();
                    //imprimir alerta
                    Usuario::setAlerta('exito', 'Hemos enviado las instrucciones a tu email');
                }else{
                    Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');
                }
            }
       }

       $alertas = Usuario::getAlertas();

       $router->render('auth/olvide', [
        'titulo' => 'Olvide mi Password',
        'alertas' => $alertas,
         ]);
    }

    public static function reestablecer(Router $router){
        //token
        $token = s($_GET['token']);
        //creamos una variable para que desaparezca el campo de password una vez validado
        $mostrar = true;
        if(!$token) header('Location: /');
        //identificar el usuario
        $usuario = Usuario::where('token' , $token);
        if(empty($usuario)){
            Usuario::setAlerta('error' , 'Token no valido');
            $mostrar = false;
        }
        $alertas = [];
            if($_SERVER['REQUEST_METHOD'] === 'POST'){
                unset($usuario->password2);
                //añadir un nuevo password
                $usuario->sincronizar($_POST);
                //validar password
                $alertas = $usuario->validarPass();
            
                if(empty($alertas)){
                    //hashear password
                    $usuario->hashPassword();
                    //eliminar el token
                    $usuario->token = null;
                    //guardar el usuario
                    $resultado = $usuario->guardar();
                    //redireccionamos
                    if($resultado){
                        header('Location: /');
                    }else{
                        Usuario::setAlerta('error', 'Hubo un error, por favor intentelo más tarde');
                    }
                }
            }

        $alertas = Usuario::getAlertas();

        $router->render('auth/reestablecer', [
        'titulo' => 'Reestablecer Password',
        'alertas' => $alertas,
        'mostrar' => $mostrar
         ]);
    }

    public static function mensaje(Router $router){
        
        $router->render('auth/mensaje', [
            'titulo' => 'Cuenta Creada Exitosamente',
        ]);
    }

    public static function confirmar(Router $router){
        $token = s($_GET['token']);
        $alertas = [];
        if(!$token) header('Location: /');

        //encontrar al usuario
        $usuario = Usuario::where('token', $token);
        
        if(empty($usuario)){
            //no se encontro un usuario
            Usuario::setAlerta('error','Token No Valido');
        }else{
            //confirmar la cuenta
            $usuario->confirmado = 1;
            $usuario->token = null;
            unset($usuario->password2);
            
            //guardar en la bbdd
            $usuario->guardar(); //lo actualiza tiene id
            
            Usuario::setAlerta('exito','Cuenta confirmada correctamente');
        }
        $alertas = Usuario::getAlertas();

        $router->render('auth/confirmar', [
            'titulo' => 'Confirma tu cuenta Uptask',
            'alertas' => $alertas
        ]);
    }
}
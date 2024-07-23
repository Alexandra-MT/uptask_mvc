<?php

namespace Controllers;

use MVC\Router;
use Classes\Email;
use Model\Usuario;

class LoginController{
    public static function login(Router $router){
        

       if($_SERVER['REQUEST_METHOD'] === 'POST'){

       }

       $router->render('auth/login', [
            'titulo' => 'Iniciar sesión',
       ]);
    }

    public static function logout(Router $router){
        echo 'desde logout';

    }

    public static function crear(Router $router){
        $usuario = new Usuario();
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            //se podria validar los password aqui pero no es recomendable
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validar();
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

       if($_SERVER['REQUEST_METHOD'] === 'POST'){

       }

       $router->render('auth/olvide', [
        'titulo' => 'Olvide mi Password',
         ]);
    }

    public static function reestablecer(Router $router){
        

       if($_SERVER['REQUEST_METHOD'] === 'POST'){

       }
       $router->render('auth/reestablecer', [
        'titulo' => 'Reestablecer Password',
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
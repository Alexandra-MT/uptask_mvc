<?php

namespace Controllers;

use MVC\Router;

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

       if($_SERVER['REQUEST_METHOD'] === 'POST'){

       }

       $router->render('auth/crear', [
        'titulo' => 'Crea tu cuenta',
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

        $router->render('auth/confirmar', [
            'titulo' => 'Confirma tu cuenta Uptask',
        ]);
    }
}
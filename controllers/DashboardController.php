<?php

namespace Controllers;

use MVC\Router;

class DashboardController{

    public static function index(Router $router){
        //pierde la session desde LoginController hay que arrancarla de nuevo, las sesiones se mantienen en la memoria del servidor, duran 24 min pero se pueden modificar en php.ini
        session_start();
        //proteger la ruta 
        isAuth();

        $router->render('dashboard/index',[
            
        ]);
    }
}
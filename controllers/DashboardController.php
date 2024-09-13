<?php

namespace Controllers;

use MVC\Router;
use Model\Usuario;
use Model\Proyecto;

class DashboardController{

    public static function index(Router $router){
        //pierde la session desde LoginController hay que arrancarla de nuevo, las sesiones se mantienen en la memoria del servidor, duran 24 min pero se pueden modificar en php.ini
        session_start();
        //proteger la ruta 
        isAuth();

        $router->render('dashboard/index',[
            'titulo' => 'Proyectos'
        ]);
    }

    public static function crearProyecto(Router $router){
        session_start();
        isAuth();
        $alertas = [];
        
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $proyecto = new Proyecto($_POST);

            //validación
            $alertas = $proyecto->validarProyecto();

            if(empty($alertas)){
                //guardar el proyecto, necesitamos el url y el propietarioId

                //url unica md5('hola') necesita un string para hashear no recomendable para password, uniqid() hashea segun la hora actual puede haber el mismo resultado
                $proyecto->url = md5(uniqid()); //genera los 32 digitos.

                //almacenar el creador del proyecto
                $proyecto->propietarioId = $_SESSION['id']; //guardamos el id del usuario en la sesion y lo traemos aca
                
                //guardamos
                $proyecto->guardar();

                //redireccionamos
                header('Location:/proyecto?url='. $proyecto->url);


            }
            
        }

        $router->render('dashboard/crear-proyecto',[
            'titulo' => 'Crear Proyecto',
            'alertas' => $alertas
        ]);
    }

    public static function proyecto(Router $router){
        session_start();
        isAuth();

        $url = $_GET['url']; // registramos el url del proyecto
        $urlBBDD = Proyecto::where('url', $url); //comparamos el url del get y el url de bbdd
        //debuguear($urlBBDD);
        //revisar que la persona que visita el proyecto es quien lo creo
        if(!$url || is_null($urlBBDD)) header('Location:/dashboard');
        
        $router->render('dashboard/proyecto',[
            'titulo' => 'Nombre Proyecto'
        ]);
    }

    public static function perfil(Router $router){
        session_start();
        isAuth();
        $router->render('dashboard/perfil',[
            'titulo' => 'Perfil'
        ]);
    }
}
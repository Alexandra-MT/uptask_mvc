<?php

namespace Controllers;

use MVC\Router;
use Classes\Email;
use Model\Usuario;
use Model\Proyecto;

class DashboardController{

    public static function index(Router $router){
        //pierde la session desde LoginController hay que arrancarla de nuevo, las sesiones se mantienen en la memoria del servidor, duran 24 min pero se pueden modificar en php.ini
        session_start();
        //proteger la ruta 
        isAuth();

        //mostrar los proyectos
        $id = $_SESSION['id'];
        $proyectos = Proyecto::belongsTo('propietarioId', $id);

        $router->render('dashboard/index',[
            'titulo' => 'Proyectos',
            'proyectos' => $proyectos
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

        //url con get
        $url = $_GET['url']; // registramos el url del proyecto
        //url desde la BBDD
        $proyectoBBDD = Proyecto::where('url', $url); //comparamos el url del get y el url de bbdd
        //debuguear($proyectoBBDD);
        //revisar que la persona que visita el proyecto es quien lo creo
        if(!$url || is_null($proyectoBBDD)) header('Location:/dashboard');
        
        //si el id de la bbdd no coincide con el id de la session
        if($proyectoBBDD->propietarioId !== $_SESSION['id']){
            header('Location:/dashboard');
        }
        $router->render('dashboard/proyecto',[
            'titulo' => $proyectoBBDD->proyecto
        ]);
    }

    public static function perfil(Router $router){
        session_start();
        isAuth();
        $alertas = [];
        $usuario = Usuario::find($_SESSION['id']);

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validar_perfil();

            if(empty($alertas)){

                $emailRegistrado = $_SESSION['email'];
                $emailNuevo = $usuario->email;
                $nombreNuevo = $usuario->nombre;

                if($emailRegistrado !== $emailNuevo){
                //verificar si el email que estan colocando no pertenezca a otro usuario
                $existeUsuario = Usuario::where('email', $usuario->email);

                if($existeUsuario && $existeUsuario->id !== $usuario->id){
                    Usuario::setAlerta('error', 'El correo electronico ya existe');
                }else{
                    $usuario->token();
                    $usuario->confirmado = 0;
                    $resultado = $usuario->guardar();
                    if($resultado){
                        $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                        $email->enviarConfirmacion();
                        Usuario::setAlerta('exito', 'Hemos enviado las instrucciones a tu correo 
                        para confirmar tu cuenta. Recuerda confirmar antes de iniciar sesión.');
                    }
                }
            }else{
                if(strlen($nombreNuevo) >= 40){
                    Usuario::setAlerta('error', 'Maximo 40 caracteres');
                }else{
                    $usuario->guardar();
                    Usuario::setAlerta('exito', 'Perfil actualizado correctamente.');
                }
            }
                    //asignar nuevos valores a la barra
                    $_SESSION['nombre'] = $usuario->nombre;
                    $_SESSION['email'] = $usuario->email;
                    $alertas = Usuario::getAlertas();
                }  
            }

        $router->render('dashboard/perfil',[
            'titulo' => 'Perfil',
            'alertas' => $alertas,
            'usuario' => $usuario
        ]);
    }

    public static function cambiar_password(Router $router){
        session_start();
        isAuth();
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario = Usuario::find($_SESSION['id']);

            //sincronizar con los datos del usuario;
            $usuario->sincronizar($_POST);

            $alertas = $usuario->nuevo_password();
            //debuguear($usuario);

            if(empty($alertas)){
                $resultado = $usuario->comprobar_password();
                if($resultado){
                   //Asignar el nuevo password
                   $usuario->password = $usuario->password_nuevo;

                   //Eliminar propiedades no necesarias
                   unset($usuario->password_actual);
                   unset($usuario->password_nuevo);

                   //Hashear el nuevo password
                   $usuario->hashPassword();

                   //Actualizar
                   $resultado = $usuario->guardar();

                   if($resultado){
                        Usuario::setAlerta('exito', 'Password actualizado correctamente');
                   }else{
                        Usuario::setAlerta('error', 'Hubo un error');
                   }
                   
                }else{
                    Usuario::setAlerta('error', 'Password Incorrecto');
                }
            }
            $alertas = Usuario::getAlertas();
        }

        $router->render('dashboard/cambiar-password',[
            'titulo' => 'Cambiar Password',
            'alertas' => $alertas,

        ]);
    }
}
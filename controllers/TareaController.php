<?php

namespace Controllers;

use Model\Tarea;
use Model\Proyecto;

//no necesitamos Router porque no hace falta hacer render a la vista lo hacemos mediante api

class TareaController{
    //http://localhost:3000/api/tareas?url=e6cac8450429ccc314ad083e827cae3b(un proyecto)
    public static function index(){
        session_start();
        //debuguear($_GET);
        $proyectoId = $_GET['url'];

        if(!$proyectoId){
            header('Location: /dashboard');
        }

        //consultamos la BBDD
        $proyecto = Proyecto::where('url', $proyectoId);

         //mira las fk
        if(!$proyecto || $proyecto->propietarioId !== $_SESSION['id']){
            header('Location: /404');
        }

         //mira las fk
        $tareas = Tarea::belongsTo('proyectoId', $proyecto->id);
        //debuguear($tareas); //array de objetos
        echo json_encode(['tareas' => $tareas]); // a formato json

    }

    public static function crear(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
        //para poder verificar si el proyecto pertenece a la persona autenticada, iniciamos session
        session_start();
        //debuguear($_SESSION);
        //creamos la variable de proyectoId para compararla con el campo url de la tabla proyectos
        //debuguear($_POST);// en el POST ya tenemos los datos de nombre y proyecto id que nos viene desde js
        $proyectoId = $_POST['proyectoId'];
        //verificamos si hay proyecto con ese url
        $proyecto = Proyecto::where('url', $proyectoId);
        //debuguear($proyecto);

        //mira las fk
        if(!$proyecto || $proyecto->propietarioId !== $_SESSION['id']){//id usuario){
            $respuesta = [
                'tipo' => 'error',
                'mensaje' => 'Hubo un error al agregar la tarea'
            ];
            echo json_encode($respuesta);
            return; //para que no se ejecuten las siguientes lineas
        }

        //todo bien , instanciar y crear la tarea
            $tarea = new Tarea($_POST);
             //mira las fk
            $tarea->proyectoId = $proyecto->id;// ojo es necesario asignar el id del proyecto al proyectoId de la tarea ya que hasta aqui el valor era el url
            $resultado = $tarea->guardar();
            //debuguear($resultado); devuelve el id y si es true o false que se guardo
            $respuesta = [
                'id' => $resultado['id'],
                'tipo' => 'exito',
                'mensaje' => 'Tarea creada correctamente',
                'proyectoId' => $proyecto->id // para poder crear el objeto virtual DOM
            ];
           
            echo json_encode($respuesta);
        
    }
}

    public static function actualizar(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
        session_start();
        //validar si el proyecto existe
        $proyecto = Proyecto::where('url', $_POST['proyectoId']); 

        if(!$proyecto || $proyecto->propietarioId !== $_SESSION['id']){
            $respuesta = [
                'tipo' => 'error',
                'mensaje' => 'Hubo un error al actualizar la tarea'
            ];
            echo json_encode($respuesta);
            return;
        }

        //instanciar la tarea con el nuevo estado
        $tarea = new Tarea($_POST);
        $tarea->proyectoId = $proyecto->id;
        $resultado = $tarea->guardar();
        if($resultado){
            $respuesta = [
                'id' => $tarea->id, // en ActiveRecord al momento de actualizar no devuelve el id, como en el caso de crear, necesitamos pasar como valor el id de la tarea
                'tipo' => 'exito',
                'mensaje' => 'Actualizado correctamente',
                'proyectoId' => $proyecto->id
            ];
            echo json_encode($respuesta);
        }

        }
    }

    public static function eliminar(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            session_start();

            $proyecto = Proyecto::where('url', $_POST['proyectoId']);

            if(!$proyecto || $proyecto->propietarioId !== $_SESSION['id']){
                $respuesta =[
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error al eliminar la tarea', 
                ];
                echo json_encode($respuesta);
                return;  
            }

            $tarea = new Tarea($_POST);
            $tarea->proyectoId = $proyecto->id;// aqui ya no es necesario el cambio ya que lo elimina solo con el id
            $resultado = $tarea->eliminar();

            if($resultado){
                $respuesta = [
                    'tipo' => 'exito',
                    'mensaje' => 'tarea eliminada correctamente'

                ];
            }
            

            echo json_encode($respuesta);
        }
    }

}
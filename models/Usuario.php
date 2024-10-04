<?php

namespace Model;

class Usuario extends ActiveRecord{
    protected static $tabla = 'usuarios';
    protected static $columnasDB =['id', 'nombre', 'email', 'password', 'token', 'confirmado'];

    public $id;
    public $nombre;
    public $email;
    public $password;
    public $password_actual;
    public $password_nuevo;
    public $password2;
    public $token;
    public $confirmado;

    public function __construct($args=[]){
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password_actual = $args['password_actual'] ?? '';
        $this->password_nuevo = $args['password_nuevo'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? 0;
        
    }

    public function validarLogin(){
        if(!$this->email || !filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            self::setAlerta('error', 'El email no tiene un formato válido');
        }
        if(!$this->password){
            self::setAlerta('error', 'El Password no puede ir vacio');
        }
        return self::getAlertas();
    }

    public function validarCuentaNueva(){
        if(!$this->nombre){
            self::setAlerta('error', 'El Nombre es Obligatorio');
        }
        if(!$this->email || !filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            self::setAlerta('error', 'El email no tiene un formato válido');
        }
        if(!$this->password){
            self::setAlerta('error', 'El Password no puede ir vacio');
        }
        if(strlen($this->password) < 6){
            self::setAlerta('error', 'El Password debe contener al menos 6 caracteres');
        }
        if($this->password !== $this->password2){
            self::setAlerta('error', 'Los password son diferentes');
        }
        return self::getAlertas();
    }
    
    public function validarEmail(){
        if(!$this->email){
            self::setAlerta('error', 'El email es obligatorio');
        }
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            self::setAlerta('error', 'El email no tiene un formato valido');
        }
        return self::getAlertas();
    }

    public function validarPass(){
        if(!$this->password){
            self::setAlerta('error' , 'El password es obligatorio');
        }
        if(strlen($this->password) < 6){
            self::setAlerta('error' , 'El password debe contener al menos 6 caracteres');
        }
        return self::getAlertas();
    }

    public function validar_perfil(){
        if(!$this->nombre){
            self::setAlerta('error', 'El nombre es obligatorio');
        }
        if(!$this->email){
            self::setAlerta('error', 'El email es obligatorio');
        }
        return self::getAlertas();
    }

    public function nuevo_password() : array {
        if(!$this->password_actual){
            self::setAlerta('error', 'El password actual es obligatorio');
        }
        if(!$this->password_nuevo){
            self::setAlerta('error', 'El password nuevo es obligatorio');
        }
        if(strlen($this->password_actual) < 6 || strlen($this->password_nuevo) < 6){
            self::setAlerta('error', 'El password debe contener al menos 6 caracteres');
        }
        return self::getAlertas();
    }
    //password_verify();
    public function comprobar_password() : bool {
        return password_verify($this->password_actual, $this->password);
    }

    public function hashPassword() : void {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    public function token() : void {
        //$this->token = md5( uniqid() );
        $this->token = uniqid();
    }


}
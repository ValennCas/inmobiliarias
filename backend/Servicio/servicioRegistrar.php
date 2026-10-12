<?php

require_once '../api/conexion.php';
require_once '../api/registrar.php';

class servicioRegistrar {

    private Registrar $registrar;

    public function __construct(PDO $conexion) {
        $this->registrar = new Registrar($conexion);
    }

    public function registrarUsuario(){
        header("Content-Type: application/json; charset=utf-8");
        $metodo = $_SERVER["REQUEST_METHOD"];
    
        if($metodo == "POST"){
            $obtener_cuerpo = json_decode(file_get_contents("php://input"), true);
        }
        
        if(is_array($obtener_cuerpo)){
            http_response_code(400);
            echo json_encode(["error" => "Faltan completar datos"]);
            exit();
        }

        if(!isset($obtener_cuerpo["email"]) || !isset($obtener_cuerpo["password"]) || !isset($obtener_cuerpo["nombre_usuario"]) || 
            !isset($obtener_cuerpo["apellido_usuario"])){
            http_response_code(400);
            echo json_encode(["error" => "Faltan completar datos"]);
            exit();
        }

        $usuario_registrado = $this->registrar->buscarUsuario($obtener_cuerpo['email'], $obtener_cuerpo["nombre_usuario"]);

        if ($usuario_registrado != false){
            http_response_code(409);
            echo json_encode(["error" => "El email o usuario ya se encuentra registrado"]);
            exit();
        }

        $password_hash = password_hash($obtener_cuerpo["password"], PASSWORD_DEFAULT);
    
        try{
            $usuario_creado = $this->registrar->insertarUsuario($obtener_cuerpo["email"], $password_hash, $obtener_cuerpo["nombre_usuario"], $obtener_cuerpo["apellido_usuario"]);

            if ($usuario_creado){
                http_response_code(201);
                echo json_encode(["mensaje" => "Usuario registrado con exito"]);
                exit()
            }
            else{
                http_response_code(500);
                echo json_encode(["error" => "No se pudo registrar el usuario, intente mas tarde!"]);
                exit()
            }
        }
        catch (PDOException $error){
            http_response_code(500);
            echo json_encode(["error" => "Ocurrio un error al registrar el usuario, intente mas tarde!"]);
            exit();
        }
        
    }
}

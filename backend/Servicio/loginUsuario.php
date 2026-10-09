<?php
    require_once '../api/login.php';
    require_once '../api/conexion.php';

    class LoginUsuario{
        private Login $login;

        public function __construct(Login $login){
            $this->login = $login;
        }

        public function loguearUsuario(){
            header("Content-Type: application/json; charset= utf-8");
            $metodo = $_SERVER["REQUEST_METHOD"];
            if($metodo == "POST"){
                $obtener_cuerpo = json_decode(file_get_contents('php://input'), true);
            }
            else{
                http_response_code(405);
                echo json_encode(["error" => "Metodo no permitido"]);
                exit();
            }
            if(!isset($obtener_cuerpo["usuario"]) || !isset($obtener_cuerpo["password"])){
                http_response_code(400);
                echo json_encode(["error" => "Los campos estan incompletos"]);
                exit();
            }
            $usuario = $this->login->login($obtener_cuerpo["usuario"]);

            if($usuario == false){
                http_response_code(401);
                echo json_encode(["error" => "Usuario y/o contraseña incorrectos"]);
                exit();
            }

            $verificar_password = password_verify($obtener_cuerpo["password"], $usuario["password"]);
            
            if(!$verificar_password){
                http_response_code(401);
                echo json_encode(["error" => "Usuario y/o contraseña incorrectos"]);
                exit();
            }

            $token= $this->token->generarToken();
            http_response_code(200);
            echo json_encode(["token" => $token, "id_usuario" => $obtener_cuerpo["id_usuario"], "id_inmobiliaria" => $obtener_cuerpo["id_inmobiliaria"]]);
            exit();
        }

        
    }
?>
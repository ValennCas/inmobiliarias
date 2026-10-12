<?php
    require_once '../api/login.php';
    require_once '../api/conexion.php';

    class LoginUsuario{
        private Login $login;

        public function __construct(PDO $conexion){
            $this->login = new Login($conexion);
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

            if(is_array($obtener_cuerpo)){
                http_response_code(400);
                echo json_encode(["error" => "Faltan completar datos"]);
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

            $token_generado= $this->generarToken();
            $guardarToken= $this->login->guardarToken($usuario["id_usuario"], $token_generado["token"], $token_generado["fecha_expiracion"]);
            if(!$guardarToken){
                http_response_code(500);
                echo json_encode(["error" => "Credenciales invalidas"]);
                exit();
            }
            http_response_code(200);
            echo json_encode(["token" => $token_generado["token"], "id_usuario" => $usuario["id_usuario"], "id_inmobiliaria" => $usuario["id_inmobiliaria"]]);
            exit();
        }

        public function generarToken(){
            $token= bin2hex(random_bytes(32));
            $token_hash = hash('sha256', $token);
            $fecha_expiracion= new DateTime();
            $fecha_expiracion=> modify("+4 hours");
            return ["token" => $token_hash, "fecha_expiracion" => $fecha_expiracion];
        }
    }
?>
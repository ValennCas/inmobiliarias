<?php
    require_once '../api/seguridad.php';
    require_once '../api/conexion.php';

    class SeguridadServicio{
        private Seguridad $seguridad;

        public function __construct(Seguridad $seguridad){
            $this->seguridad = $seguridad;
        }

        public function verificarToken(){
            $headers = getallheaders();

            if(!isset($headers["Authorization"])){
                http_response_code(401);
                echo json_encode(["error" => "Acceso no autorizado"]);
                exit();
            }

            $autorizacion = $headers["Authorization"];
            $token_partes = explode(" ", $autorizacion);

            if(count($token_partes) != 2 || $token_partes[0] !== "Bearer"){
                http_response_code(401);
                echo json_encode(["error" => "Acceso no autorizado"]);
                exit();
            }

            $hashear_token = hash("sha256", $token_partes[1]);
            $obtener_token = $this->seguridad->validarToken($hashear_token);
            
        }
    }
?>
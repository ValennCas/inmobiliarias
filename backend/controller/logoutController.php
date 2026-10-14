<?php
    require_once __DIR__ . '/../config/cors.php';
    require_once __DIR__ . '/../api/conexion.php';
    require_once __DIR__ . '/../api/seguridad.php';
    require_once __DIR__ . '/../Servicio/logout.php';

    class LogoutController{
        private Logout $servicio;
        private Seguridad $seguridad;

        public function __construct(Logout $servicio, Seguridad $seguridad){
            $this->servicio = $servicio;
            $this->seguridad = $seguridad;
        }

        private function responder($codigo, $cuerpo = null){
            http_response_code($codigo);
            if($cuerpo !== null){
                echo json_encode($cuerpo, JSON_UNESCAPED_UNICODE);
            }
            exit();
        }

        public function atender(){
            $metodo = $_SERVER["REQUEST_METHOD"];
            $id_usuario = $_GET["id_usuario"] ?? null;
            $token = $_GET["token"] ?? null;

            try{
                $headers = getallheaders();
                $authorization = $headers["Authorization"] ?? null;

                if($authorization === null || !str_starts_with($authorization, "Bearer ")){
                    throw new Exception("Token no enviado", 401);
                }

                $token = substr($authorization, 7);
                $token_hash = hash("sha256", $token);

                $id_usuario_token = $this->seguridad->verificarToken($token_hash);
                
                if($id_usuario_token !== $_GET["id_usuario"]){
                    throw new Exception("Token no enviado", 401);
                }

                switch($metodo){
                    case "DELETE":
                        $this->responder(200, $this->servicio->cerrarSesion($id_usuario));
                    default:
                        $this->responder(405, ["error" => "Metodo no permitido"]);
                }
            }
            catch(PDOException $error){
                $this->responder(500, ["error" => "Ocurrio un error, intente mas tarde!"]);
            }
            catch(Exception $error){
                $this->responder($error->getCode(), ["error" => $error->getMessage()]);
            }
        }
    }

    $controller = new LogoutController(new Logout($conexion), new Seguridad($conexion));
    $controller->atender();
?>
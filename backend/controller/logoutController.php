<?php
    require_once __DIR__ . '/../config/cors.php';
    require_once __DIR__ . '/../api/conexion.php';
    require_once __DIR__ . '/../api/seguridad.php';
    require_once __DIR__ . '/../config/autenticacion.php';
    require_once __DIR__ . '/../Servicio/logout.php';

    class LogoutController{
        private Logout $servicio;
        private SeguridadServicio $seguridad;

        public function __construct(Logout $servicio, SeguridadServicio $seguridad){
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
                obtenerIdUsuarioAutenticado($this->seguridad, (int) $id_usuario);

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

    $controller = new LogoutController(new Logout($conexion), new SeguridadServicio(new Seguridad($conexion)));
    $controller->atender();
?>
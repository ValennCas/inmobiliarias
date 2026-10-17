<?php
    require_once __DIR__ . '/../config/cors.php';
    require_once __DIR__ . '/../api/conexion.php';
    require_once __DIR__ . '/../api/inmobiliaria.php';
    require_once __DIR__ . '/../api/seguridad.php';
    require_once __DIR__ . '/../config/autenticacion.php';
    require_once __DIR__ . '/../Servicio/seguridad.php';
    require_once __DIR__ . '/../Servicio/servicioInmobiliaria.php';

    class InmobiliariaController{
        private ServicioInmobiliaria $servicio;
        private SeguridadServicio $seguridad;

        public function __construct(ServicioInmobiliaria $servicio, SeguridadServicio $seguridad){
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
            $id_inmobiliaria = $_GET["id_inmobiliaria"] ?? null;
            $id_usuario = (int) ($_GET["id_usuario"] ?? 0);
            
            try{
                obtenerIdUsuarioAutenticado($this->seguridad, (int) $id_usuario);

                switch($metodo){
                    case "POST":
                        $cuerpo = json_decode(file_get_contents("php://input"), true) ?? [];
                        $this->responder(201, $this->servicio->crearInmobiliaria($id_usuario, $cuerpo));
                    case "GET":
                        if($id_inmobiliaria !== null || $id_usuario !== null){
                            $this->responder(200, $this->servicio->buscarInmobiliaria($id_usuario, $id_inmobiliaria));
                        }
                    case "PUT":
                        if($id_usuario !== null){
                            $cuerpo = json_decode(file_get_contents("php://input"), true) ?? [];
                            $this->responder(200, $this->servicio->actualizarInmobiliaria($id_usuario, $cuerpo));
                        }
                    default:
                        $this->responder(405, ["error" => "Metodo no permitido"]);
                }
            }
            catch(PDOException $error){
                $this->responder(500, ["error" => "Ocurrio un error, intente mas tarde!"]);
            }
            catch(Exception $error){
                $this->responder($error->getCode(),["error" => $error->getMessage()]);
            }
        }
    }

    $controller = new InmobiliariaController(new ServicioInmobiliaria(new Inmobiliaria($conexion)), new SeguridadServicio(new Seguridad($conexion)));
    $controller->atender();
?>
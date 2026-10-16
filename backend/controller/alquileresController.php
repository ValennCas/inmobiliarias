<?php
    // Punto de entrada: http://localhost/inmobiliarias/backend/controller/alquileresController.php
    require_once __DIR__ . '/../config/cors.php';
    require_once __DIR__ . '/../api/conexion.php';
    require_once __DIR__ . '/../api/alquileres.php';
    require_once __DIR__ . '/../Servicio/servicioAlquileres.php';
    require_once __DIR__ . '/../config/autenticacion.php';

    class AlquileresController{
        private ServicioAlquileres $servicio;
        private SeguridadServicio $seguridad;

        public function __construct(ServicioAlquileres $servicio, SeguridadServicio $seguridad){
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
            $id = $_GET["id_alquiler"] ?? null;

            try{
                obtenerIdUsuarioAutenticado($this->seguridad, (int) ($_GET["id_usuario"] ?? 0));

                switch($metodo){
                    case "GET":
                        if($id !== null){
                            $this->responder(200, $this->servicio->obtenerAlquiler($id));
                        }
                        $this->responder(200, $this->servicio->listarAlquileres($_GET["id_inquilino"] ?? null, $_GET["id_inmobiliaria"] ?? null));
                    case "POST":
                        $cuerpo = json_decode(file_get_contents("php://input"), true) ?? [];
                        $this->responder(201, $this->servicio->crearAlquiler($cuerpo));
                    case "PUT":
                        $cuerpo = json_decode(file_get_contents("php://input"), true) ?? [];
                        $this->responder(200, $this->servicio->modificarAlquiler($id, $cuerpo));
                    case "DELETE":
                        $this->servicio->borrarAlquiler($id);
                        $this->responder(204);
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

    $controller = new AlquileresController(new ServicioAlquileres(new Alquileres($conexion)), new SeguridadServicio(new Seguridad($conexion)));
    $controller->atender();
?>

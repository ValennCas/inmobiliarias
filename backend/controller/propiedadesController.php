<?php
    // Punto de entrada: http://localhost/inmobiliarias/backend/controller/propiedadesController.php
    require_once __DIR__ . '/../api/conexion.php';
    require_once __DIR__ . '/../api/propiedades.php';
    require_once __DIR__ . '/../Servicio/servicioPropiedades.php';

    class PropiedadesController{
        private ServicioPropiedades $servicio;

        public function __construct(ServicioPropiedades $servicio){
            $this->servicio = $servicio;
        }

        private function responder($codigo, $cuerpo = null){
            http_response_code($codigo);
            if($cuerpo !== null){
                echo json_encode($cuerpo, JSON_UNESCAPED_UNICODE);
            }
            exit();
        }

        public function atender(){
            header("Content-Type: application/json; charset=utf-8");

            $origen = $_SERVER["HTTP_ORIGIN"] ?? "";
            $origenes_permitidos = ["http://127.0.0.1:5500", "http://localhost:5500"];
            if(in_array($origen, $origenes_permitidos, true)){
                header("Access-Control-Allow-Origin: $origen");
                header("Vary: Origin");
            }
            header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
            header("Access-Control-Allow-Headers: Content-Type");

            $metodo = $_SERVER["REQUEST_METHOD"];
            $id = $_GET["id_propiedad"] ?? null;

            try{
                switch($metodo){
                    case "OPTIONS":
                        $this->responder(200);
                    case "GET":
                        if($id !== null){
                            $this->responder(200, $this->servicio->obtenerPropiedad($id));
                        }
                        $this->responder(200, $this->servicio->listarPropiedades($_GET["id_inmobiliaria"] ?? null));
                    case "POST":
                        $cuerpo = json_decode(file_get_contents("php://input"), true) ?? [];
                        $this->responder(201, $this->servicio->crearPropiedad($cuerpo));
                    case "PUT":
                        $cuerpo = json_decode(file_get_contents("php://input"), true) ?? [];
                        $this->responder(200, $this->servicio->modificarPropiedad($id, $cuerpo));
                    case "DELETE":
                        $this->servicio->borrarPropiedad($id);
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

    $controller = new PropiedadesController(new ServicioPropiedades(new Propiedades($conexion)));
    $controller->atender();
?>

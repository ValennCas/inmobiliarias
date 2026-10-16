<?php
    // Endpoint publico (sin token): una persona le escribe a la inmobiliaria duena de una propiedad
    require_once __DIR__ . '/../config/cors.php';
    require_once __DIR__ . '/../api/conexion.php';
    require_once __DIR__ . '/../api/contactos.php';
    require_once __DIR__ . '/../Servicio/servicioContacto.php';

    class ContactoController{
        private ServicioContacto $servicio;

        public function __construct(ServicioContacto $servicio){
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
            $metodo = $_SERVER["REQUEST_METHOD"];

            try{
                switch($metodo){
                    case "GET":
                        $this->responder(200, $this->servicio->datosFormulario($_GET["id_inmobiliaria"] ?? null, $_GET["id_propiedad"] ?? null));
                    case "POST":
                        $cuerpo = json_decode(file_get_contents("php://input"), true) ?? [];
                        $this->responder(200, $this->servicio->enviarConsulta($cuerpo));
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

    $controller = new ContactoController(new ServicioContacto(new Contactos($conexion)));
    $controller->atender();
?>

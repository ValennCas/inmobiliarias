<?php
    // Endpoint publico (sin token): una persona le escribe a la inmobiliaria duena de una propiedad
    // Carga la conexion y los archivos que necesita este controlador
    require_once __DIR__ . '/../config/cors.php';
    require_once __DIR__ . '/../api/conexion.php';
    require_once __DIR__ . '/../api/contactos.php';
    require_once __DIR__ . '/../Servicio/servicioContacto.php';

    class ContactoController{
        private ServicioContacto $servicio;

        // Guarda los servicios que va a usar el controlador
        public function __construct(ServicioContacto $servicio){
            $this->servicio = $servicio;
        }

        // Envia el codigo HTTP si hay datos, los devuelve en JSON antes de terminar la peticion
        private function responder($codigo, $cuerpo = null){
            http_response_code($codigo);
            if($cuerpo !== null){
                echo json_encode($cuerpo, JSON_UNESCAPED_UNICODE);
            }
            exit();
        }

        // Permite consultar los datos del formulario o enviar una consulta
        public function atender(){
            $metodo = $_SERVER["REQUEST_METHOD"];

            try{
                switch($metodo){
                    // Busca los datos del formulario para la inmobiliaria y la propiedad indicadas
                    case "GET":
                        $this->responder(200, $this->servicio->datosFormulario($_GET["id_inmobiliaria"] ?? null, $_GET["id_propiedad"] ?? null));
                    // Lee la consulta enviada en JSON y la pasa al servicio de contacto
                    case "POST":
                        $cuerpo = json_decode(file_get_contents("php://input"), true) ?? [];
                        $this->responder(200, $this->servicio->enviarConsulta($cuerpo));
                    // Rechaza los metodos que este controlador no admite
                    default:
                        $this->responder(405, ["error" => "Metodo no permitido"]);
                }
            }
            // Responde con el codigo 500 si falla una operacion de la base de datos
            catch(PDOException $error){
                $this->responder(500, ["error" => "Ocurrio un error, intente mas tarde!"]);
            }
            // Devuelve el codigo y el mensaje del error recibido
            catch(Exception $error){
                $this->responder($error->getCode(), ["error" => $error->getMessage()]);
            }
        }
    }

    // Crea el controlador con los servicios y la conexion a la base de datos
    $controller = new ContactoController(new ServicioContacto(new Contactos($conexion)));
    // Atiende la peticion que llego a este archivo
    $controller->atender();
?>

<?php
    // Punto de entrada: http://localhost/inmobiliarias/backend/controller/inquilinosController.php
    // Carga la conexion y los archivos que necesita este controlador
    require_once __DIR__ . '/../config/cors.php';
    require_once __DIR__ . '/../api/conexion.php';
    require_once __DIR__ . '/../api/inquilinos.php';
    require_once __DIR__ . '/../Servicio/servicioInquilinos.php';
    require_once __DIR__ . '/../config/autenticacion.php';

    class InquilinosController{
        private ServicioInquilinos $servicio;
        private SeguridadServicio $seguridad;

        // Guarda los servicios que va a usar el controlador
        public function __construct(ServicioInquilinos $servicio, SeguridadServicio $seguridad){
            $this->servicio = $servicio;
            $this->seguridad = $seguridad;
        }

        // Envia el codigo HTTP si hay datos, los devuelve en JSON antes de terminar la peticion
        private function responder($codigo, $cuerpo = null){
            http_response_code($codigo);
            if($cuerpo !== null){
                echo json_encode($cuerpo, JSON_UNESCAPED_UNICODE);
            }
            exit();
        }

        // Recibe el metodo y el id del inquilino para decidir que operacion realizar
        public function atender(){
            $metodo = $_SERVER["REQUEST_METHOD"];
            $id = $_GET["id_inquilino"] ?? null;

            try{
                // Comprueba la autenticacion del usuario antes de continuar
                obtenerIdUsuarioAutenticado($this->seguridad, (int) ($_GET["id_usuario"] ?? 0));

                switch($metodo){
                    // Busca un inquilino por su id o devuelve la lista completa
                    case "GET":
                        if($id !== null){
                            $this->responder(200, $this->servicio->obtenerInquilino($id));
                        }
                        $this->responder(200, $this->servicio->listarInquilinos());
                    // Lee los datos enviados en JSON y crea el inquilino
                    case "POST":
                        $cuerpo = json_decode(file_get_contents("php://input"), true) ?? [];
                        $this->responder(201, $this->servicio->crearInquilino($cuerpo));
                    // Lee los datos enviados en JSON y modifica el inquilino indicado
                    case "PUT":
                        $cuerpo = json_decode(file_get_contents("php://input"), true) ?? [];
                        $this->responder(200, $this->servicio->modificarInquilino($id, $cuerpo));
                    // Borra el inquilino y responde sin contenido
                    case "DELETE":
                        $this->servicio->borrarInquilino($id);
                        $this->responder(204);
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
    $controller = new InquilinosController(new ServicioInquilinos(new Inquilinos($conexion)), new SeguridadServicio(new Seguridad($conexion)));
    // Atiende la peticion que llego a este archivo
    $controller->atender();
?>

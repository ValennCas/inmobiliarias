<?php
    // Punto de entrada: http://localhost/inmobiliarias/backend/controller/alquileresController.php
    // Carga la conexion y los archivos que necesita este controlador
    require_once __DIR__ . '/../config/cors.php';
    require_once __DIR__ . '/../api/conexion.php';
    require_once __DIR__ . '/../api/alquileres.php';
    require_once __DIR__ . '/../Servicio/servicioAlquileres.php';
    require_once __DIR__ . '/../config/autenticacion.php';

    class AlquileresController{
        private ServicioAlquileres $servicio;
        private SeguridadServicio $seguridad;

        // Guarda los servicios que va a usar el controlador
        public function __construct(ServicioAlquileres $servicio, SeguridadServicio $seguridad){
            $this->servicio = $servicio;
            $this->seguridad = $seguridad;
        }

        // Envia el codigo HTTP si hay datos, los devuelve en formato JSON antes de terminar la peticion
        private function responder($codigo, $cuerpo = null){
            http_response_code($codigo);
            if($cuerpo !== null){
                echo json_encode($cuerpo, JSON_UNESCAPED_UNICODE);
            }
            exit();
        }

        // Recibe el metodo y el id del alquiler para decidir que operacion realizar
        public function atender(){
            $metodo = $_SERVER["REQUEST_METHOD"];
            $id = $_GET["id_alquiler"] ?? null;

            try{
                // Comprueba la autenticacion del usuario atraves del token antes de continuar
                obtenerIdUsuarioAutenticado($this->seguridad, (int) ($_GET["id_usuario"] ?? 0));

                switch($metodo){
                    // Busca un alquiler por su id o lista los alquileres con los filtros recibidos
                    case "GET":
                        if($id !== null){
                            $this->responder(200, $this->servicio->obtenerAlquiler($id));
                        }
                        $this->responder(200, $this->servicio->listarAlquileres($_GET["id_inquilino"] ?? null, $_GET["id_inmobiliaria"] ?? null));
                    // Lee los datos enviados en JSON y crea el alquiler
                    case "POST":
                        $cuerpo = json_decode(file_get_contents("php://input"), true) ?? [];
                        $this->responder(201, $this->servicio->crearAlquiler($cuerpo));
                    // Lee los cambios en JSON y modifica el alquiler indicado
                    case "PUT":
                        $cuerpo = json_decode(file_get_contents("php://input"), true) ?? [];
                        $this->responder(200, $this->servicio->modificarAlquiler($id, $cuerpo));
                    // Borra el alquiler y pasa al servicio el nuevo estado recibido, si lo hay
                    case "DELETE":
                        $cuerpo = json_decode(file_get_contents("php://input"), true ) ?? [];
                        $this->servicio->borrarAlquiler( $id, $cuerpo["estado_nuevo"] ?? null);
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
    $controller = new AlquileresController(new ServicioAlquileres(new Alquileres($conexion)), new SeguridadServicio(new Seguridad($conexion)));
    // Atiende la peticion que llego a este archivo
    $controller->atender();
?>

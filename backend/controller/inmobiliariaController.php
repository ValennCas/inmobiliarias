<?php
    // Carga la conexion y los archivos que necesita este controlador
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

        // Guarda los servicios que va a usar el controlador
        public function __construct(ServicioInmobiliaria $servicio, SeguridadServicio $seguridad){
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

        // Permite crear, buscar o actualizar la inmobiliaria del usuario autenticado, metodo que no este 
        //aceptado rechaza
        public function atender(){
            $metodo = $_SERVER["REQUEST_METHOD"];
            $id_inmobiliaria = $_GET["id_inmobiliaria"] ?? null;
            $id_usuario = (int) ($_GET["id_usuario"] ?? 0);
            
            try{
                // Comprueba la autenticacion del usuario antes de continuar
                obtenerIdUsuarioAutenticado($this->seguridad, (int) $id_usuario);

                switch($metodo){
                    // Lee los datos en JSON y crea la inmobiliaria para el usuario
                    case "POST":
                        $cuerpo = json_decode(file_get_contents("php://input"), true) ?? [];
                        $this->responder(201, $this->servicio->crearInmobiliaria($id_usuario, $cuerpo));
                    // Busca la inmobiliaria con los datos del usuario y de la inmobiliaria
                    case "GET":
                        if($id_inmobiliaria !== null || $id_usuario !== null){
                            $this->responder(200, $this->servicio->buscarInmobiliaria($id_usuario, $id_inmobiliaria));
                        }
                    // Lee los cambios en JSON y actualiza la inmobiliaria del usuario
                    case "PUT":
                        if($id_usuario !== null){
                            $cuerpo = json_decode(file_get_contents("php://input"), true) ?? [];
                            $this->responder(200, $this->servicio->actualizarInmobiliaria($id_usuario, $cuerpo));
                        }
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
                $this->responder($error->getCode(),["error" => $error->getMessage()]);
            }
        }
    }

    // Crea el controlador con los servicios y la conexion a la base de datos
    $controller = new InmobiliariaController(new ServicioInmobiliaria(new Inmobiliaria($conexion)), new SeguridadServicio(new Seguridad($conexion)));
    // Atiende la peticion que llego a este archivo
    $controller->atender();
?>
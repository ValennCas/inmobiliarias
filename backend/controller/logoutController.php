<?php
    // Carga la conexion y los archivos que necesita este controlador
    require_once __DIR__ . '/../config/cors.php';
    require_once __DIR__ . '/../api/conexion.php';
    require_once __DIR__ . '/../api/seguridad.php';
    require_once __DIR__ . '/../config/autenticacion.php';
    require_once __DIR__ . '/../Servicio/logout.php';

    class LogoutController{
        private Logout $servicio;
        private SeguridadServicio $seguridad;

        // Guarda los servicios que va a usar el controlador
        public function __construct(Logout $servicio, SeguridadServicio $seguridad){
            $this->servicio = $servicio;
            $this->seguridad = $seguridad;
        }

        // Envia el codigo HTTP si hay datos, los devuelve en JSON al Frontend antes de terminar la peticion
        private function responder($codigo, $cuerpo = null){
            http_response_code($codigo);
            if($cuerpo !== null){
                echo json_encode($cuerpo, JSON_UNESCAPED_UNICODE);
            }
            exit();
        }

        // Recibe los datos de la peticion y permite cerrar sesion mediante DELETE
        public function atender(){
            $metodo = $_SERVER["REQUEST_METHOD"];
            $id_usuario = $_GET["id_usuario"] ?? null;
            $token = $_GET["token"] ?? null;

            try{
                // Comprueba la autenticacion del usuario antes de continuar
                obtenerIdUsuarioAutenticado($this->seguridad, (int) $id_usuario);

                switch($metodo){
                    // Cierra la sesion del usuario autenticado
                    case "DELETE":
                        $this->responder(200, $this->servicio->cerrarSesion($id_usuario));
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
    $controller = new LogoutController(new Logout($conexion), new SeguridadServicio(new Seguridad($conexion)));
    // Atiende la peticion que llego a este archivo
    $controller->atender();
?>
<?php
    // Carga la conexion y los archivos que necesita este controlador
    require_once __DIR__ . '/../config/cors.php';
    require_once __DIR__ . '/../api/conexion.php';
    require_once __DIR__ . '/../Servicio/loginUsuario.php';

    class LoginController{
        private LoginUsuario $servicio;

        // Guarda los servicios que va a usar el controlador
        public function __construct(LoginUsuario $servicio){
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

        // Recibe la peticion de inicio de sesion y acepta el metodo POST
        public function atender(){
            $metodo = $_SERVER["REQUEST_METHOD"];
            try{
                switch($metodo){
                    // Lee los datos en JSON y los pasa al servicio para iniciar sesion
                    case "POST":
                        $cuerpo = json_decode(file_get_contents("php://input"), true) ?? [];
                        $this->responder(200, $this->servicio->loguearUsuario($cuerpo));

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
    $controller = new LoginController(new LoginUsuario($conexion));
    // Atiende la peticion que llego a este archivo
    $controller->atender();
?>
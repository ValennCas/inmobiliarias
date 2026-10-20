<?php
    // Endpoint publico (sin token): un usuario pide reestablecer contraseña
    // Carga la conexion y los archivos que necesita este controlador
    require_once __DIR__ . '/../config/cors.php';
    require_once __DIR__ . '/../api/conexion.php';
    require_once __DIR__ . '/../api/recuperarContrasenia.php';
    require_once __DIR__ . '/../Servicio/servicioRecuperarContrasenia.php';

    class RecuperarContraseniaController{
        private ServicioRecuperarContrasenia $servicio;

        // Guarda los servicios que va a usar el controlador
        public function __construct(ServicioRecuperarContrasenia $servicio){
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

        // Recibe la accion para solicitar el codigo, verificarlo o cambiar la contrasenia
        public function atender(){
            $metodo = $_SERVER["REQUEST_METHOD"];

            try{
                // Comprueba que la peticion use POST, sino rechaza
                if($metodo !== "POST"){
                    $this->responde(405, ["error" => "Metodo no permitido"]);
                }

                // Lee los datos enviados en JSON; si no hay datos, usa un arreglo vacio
                $datos = json_decode(file_get_contents("php://input"), true) ?? [];
                $accion = $datos["accion"] ?? "";
                switch($accion){
                    // Pide al servicio un codigo para recuperar la contrasenia
                    case 'solicitar':
                        $this->responder(200, $this->servicio->solicitarTokenRecuperacion($datos));
                    // Comprueba el codigo de recuperacion enviado por el usuario
                    case 'verificar':
                        $this->responder(200, $this->servicio->verificarTokenContra($datos));
                    // Pasa los datos al servicio para cambiar la contrasenia
                    case'cambiar':
                        $this->responder(200, $this->servicio->cambiarContrasenia($datos));
                    // Rechaza las acciones que no estan previstas
                    default:
                        $this->responder(400, ["error" => "Accion invalida."]);
                }
            }
            // Devuelve el error de base de datos con el codigo 500
            catch(PDOException $error){
                $this->responder(500, ["error" => $error->getMessage()]);
            }
            // Devuelve el codigo y el mensaje del error recibido
            catch(Exception $error){
                $codigo = $error->getCode();
                // Usa el codigo 500 si la excepcion no trae un codigo HTTP de error valido
                if($codigo < 400 || $codigo > 599){
                    $codigo = 500;
                }
                $this->responder($codigo, ["error" => $error->getMessage()]);
            }
        }
    }
    
    // Crea el controlador con los servicios y la conexion a la base de datos
    $controller = new RecuperarContraseniaController(new ServicioRecuperarContrasenia(new RecuperarContrasenia($conexion)));
    // Atiende la peticion que llego a este archivo
    $controller->atender();
?>
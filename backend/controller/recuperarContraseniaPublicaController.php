<?php
    // Endpoint publico (sin token): un usuario pide reestablecer contraseña
    require_once __DIR__ . '/../config/cors.php';
    require_once __DIR__ . '/../api/conexion.php';
    require_once __DIR__ . '/../api/recuperarContrasenia.php';
    require_once __DIR__ . '/../Servicio/servicioRecuperarContrasenia.php';

    class RecuperarContraseniaController{
        private ServicioRecuperarContrasenia $servicio;

        public function __construct(ServicioRecuperarContrasenia $servicio){
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
                if($metodo !== "POST"){
                    $this->responde(405, ["error" => "Metodo no permitido"]);
                }

                $datos = json_decode(file_get_contents("php://input"), true) ?? [];
                $accion = $datos["accion"] ?? "";
                switch($accion){
                    case 'solicitar':
                        $this->responder(200, $this->servicio->solicitarTokenRecuperacion($datos));
                    case 'verificar':
                        $this->responder(200, $this->servicio->verificarTokenContra($datos));
                    case'cambiar':
                        $this->responder(200, $this->servicio->cambiarContrasenia($datos));
                    default:
                        $this->responder(400, ["error" => "Accion invalida."]);
                }
            }
            catch(PDOException $error){
                $this->responder(500, ["error" => $error->getMessage()]);
            }
            catch(Exception $error){
                $codigo = $error->getCode();
                if($codigo < 400 || $codigo > 599){
                    $codigo = 500;
                }
                $this->responder($codigo, ["error" => $error->getMessage()]);
            }
        }
    }
    
    $controller = new RecuperarContraseniaController(new ServicioRecuperarContrasenia(new RecuperarContrasenia($conexion)));
    $controller->atender();
?>
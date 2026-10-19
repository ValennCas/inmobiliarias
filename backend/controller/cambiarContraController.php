<?php
    require_once __DIR__ . '/../config/cors.php';
    require_once __DIR__ . '/../api/conexion.php';
    require_once __DIR__ . '/../api/seguridad.php';
    require_once __DIR__ . '/../config/autenticacion.php';
    require_once __DIR__ . '/../api/usuario.php';
    require_once __DIR__ . '/../Servicio/servicioUsuario.php';

    class CambiarContraController{
        private ServicioUsuario $servicio;
        private SeguridadServicio $seguridad;

        public function __construct(ServicioUsuario $servicio, SeguridadServicio $seguridad){
            $this->servicio = $servicio;
            $this->seguridad = $seguridad;
        }

        private function responder($codigo, $cuerpo = null){
            http_response_code($codigo);
            if($cuerpo !== null){
                echo json_encode($cuerpo, JSON_UNESCAPED_UNICODE);
            }
            exit();
        }

        public function atender(){
            try{
                if($_SERVER["REQUEST_METHOD"] !== "POST"){
                    $this->responder(405, ["error" => "Metodo no permitido"]);
                }
                $id_usuario = obtenerIdUsuarioAutenticado($this->seguridad);
                $datos = json_decode(file_get_contents("php://input"), true) ?? [];
                $resultado = $this->servicio->cambiarContrasenia($id_usuario, $datos);
                $this->responder(200, $resultado);
            }
            catch(PDOException $error){
                $this->responder(500, ["error" => "Ocurrio un error, intente mas tarde"]);
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

    $controller = new CambiarContraController(new ServicioUsuario(new Usuario($conexion)), new SeguridadServicio(new Seguridad($conexion)));
    $controller->atender();
?>
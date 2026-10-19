<?php
// Punto de entrada: http://localhost/inmobiliarias/backend/controller/usuarioController.php
    require_once __DIR__ . '/../config/cors.php';
    require_once __DIR__ . '/../api/conexion.php';
    require_once __DIR__ . '/../api/usuario.php';
    require_once __DIR__ . '/../Servicio/servicioUsuario.php';
    require_once __DIR__ . '/../config/autenticacion.php';

    class UsuarioController{
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
            $metodo = $_SERVER["REQUEST_METHOD"];
            $id_usuario = (int) ($_GET["id_usuario"] ?? 0);

            try{
                if($id_usuario <= 0){
                    $this->responder(400, ["error" => "El id del usuario es invalido"]);
                }

                obtenerIdUsuarioAutenticado($this->seguridad, $id_usuario);

                switch($metodo){
                    case "GET":
                        $this->responder(200, $this->servicio->buscarUsuario($id_usuario));

                    case "PUT":
                        $cuerpo = json_decode(file_get_contents("php://input"), true) ?? [];
                        $this->responder(200, $this->servicio->actualizarUsuario($id_usuario, $cuerpo));

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

    $controller = new UsuarioController(new ServicioUsuario(new Usuario($conexion)), new SeguridadServicio(new Seguridad($conexion)));
    $controller->atender();
?>
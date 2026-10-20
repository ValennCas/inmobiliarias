<?php
// Punto de entrada: http://localhost/inmobiliarias/backend/controller/usuarioController.php
    // Carga la conexion y los archivos que necesita este controlador
    require_once __DIR__ . '/../config/cors.php';
    require_once __DIR__ . '/../api/conexion.php';
    require_once __DIR__ . '/../api/usuario.php';
    require_once __DIR__ . '/../Servicio/servicioUsuario.php';
    require_once __DIR__ . '/../config/autenticacion.php';

    class UsuarioController{
        private ServicioUsuario $servicio;
        private SeguridadServicio $seguridad;

        // Guarda los servicios que va a usar el controlador
        public function __construct(ServicioUsuario $servicio, SeguridadServicio $seguridad){
            $this->servicio = $servicio;
            $this->seguridad = $seguridad;
        }

        // Envia el codigo HTTP y, si hay datos, los devuelve en JSON antes de terminar la peticion
        private function responder($codigo, $cuerpo = null){
            http_response_code($codigo);
            if($cuerpo !== null){
                echo json_encode($cuerpo, JSON_UNESCAPED_UNICODE);
            }
            exit();
        }

        // Permite consultar o actualizar los datos del usuario con su autenticacion
        public function atender(){
            $metodo = $_SERVER["REQUEST_METHOD"];
            $id_usuario = (int) ($_GET["id_usuario"] ?? 0);

            try{
                // Rechaza la peticion si el id del usuario no es valido
                if($id_usuario <= 0){
                    $this->responder(400, ["error" => "El id del usuario es invalido"]);
                }

                // Comprueba la autenticacion del usuario antes de continuar
                obtenerIdUsuarioAutenticado($this->seguridad, $id_usuario);

                switch($metodo){
                    // Busca los datos del usuario autenticado
                    case "GET":
                        $this->responder(200, $this->servicio->buscarUsuario($id_usuario));

                    // Lee los cambios en JSON y actualiza los datos del usuario
                    case "PUT":
                        $cuerpo = json_decode(file_get_contents("php://input"), true) ?? [];
                        $this->responder(200, $this->servicio->actualizarUsuario($id_usuario, $cuerpo));

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
    $controller = new UsuarioController(new ServicioUsuario(new Usuario($conexion)), new SeguridadServicio(new Seguridad($conexion)));
    // Atiende la peticion que llego a este archivo
    $controller->atender();
?>
<?php
    // Carga la conexion y los archivos que necesita este controlador
    require_once __DIR__ . '/../config/cors.php';
    require_once __DIR__ . '/../api/conexion.php';
    require_once __DIR__ . '/../api/seguridad.php';
    require_once __DIR__ . '/../config/autenticacion.php';
    require_once __DIR__ . '/../api/usuario.php';
    require_once __DIR__ . '/../Servicio/servicioUsuario.php';

    class CambiarContraController{
        private ServicioUsuario $servicio;
        private SeguridadServicio $seguridad;

        // Guarda los servicios que va a usar el controlador
        public function __construct(ServicioUsuario $servicio, SeguridadServicio $seguridad){
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

        // Permite cambiar la contrasenia del usuario autenticado mediante POST
        public function atender(){
            try{
                // Acepta solamente peticiones mediante POST sin son distintas rechaza
                if($_SERVER["REQUEST_METHOD"] !== "POST"){
                    $this->responder(405, ["error" => "Metodo no permitido"]);
                }
                // Comprueba la autenticacion y obtiene el id del usuario que va a cambiar su contrasenia
                $id_usuario = obtenerIdUsuarioAutenticado($this->seguridad);
                // Lee los datos enviados en JSON; si no hay datos, usa un arreglo vacio
                $datos = json_decode(file_get_contents("php://input"), true) ?? [];
                // Pasa los datos al servicio para cambiar la contrasenia del usuario autenticado
                $resultado = $this->servicio->cambiarContrasenia($id_usuario, $datos);
                $this->responder(200, $resultado);
            }
            // Responde con el codigo 500 si falla una operacion de la base de datos
            catch(PDOException $error){
                $this->responder(500, ["error" => "Ocurrio un error, intente mas tarde"]);
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
    $controller = new CambiarContraController(new ServicioUsuario(new Usuario($conexion)), new SeguridadServicio(new Seguridad($conexion)));
    // Atiende la peticion que llego a este archivo
    $controller->atender();
?>
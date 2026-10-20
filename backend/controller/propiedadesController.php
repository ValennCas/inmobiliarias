<?php
    // Punto de entrada: http://localhost/inmobiliarias/backend/controller/propiedadesController.php
    // Carga la conexion y los archivos que necesita este controlador
    require_once __DIR__ . '/../config/cors.php';
    require_once __DIR__ . '/../api/conexion.php';
    require_once __DIR__ . '/../api/propiedades.php';
    require_once __DIR__ . '/../Servicio/servicioPropiedades.php';
    require_once __DIR__ . '/../config/autenticacion.php';

    class PropiedadesController{
        private ServicioPropiedades $servicio;
        private SeguridadServicio $seguridad;

        // Guarda los servicios que va a usar el controlador
        public function __construct(ServicioPropiedades $servicio, SeguridadServicio $seguridad){
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

        // Recibe el metodo, el id de la propiedad y las fotos enviadas en la peticion
        public function atender(){
            $metodo = $_SERVER["REQUEST_METHOD"];
            $id = $_GET["id_propiedad"] ?? null;
            $fotos = $_FILES["fotos"] ?? null;

            try{
                // Ver propiedades es publico; crear, modificar y borrar requieren token del usuario
                if($metodo !== "GET"){
                    // Comprueba la autenticacion del usuario antes de continuar
                    obtenerIdUsuarioAutenticado($this->seguridad, (int) ($_GET["id_usuario"] ?? 0));
                }

                switch($metodo){
                    // Busca una propiedad por su id o lista las propiedades de la inmobiliaria indicada
                    case "GET":
                        if($id !== null){
                            $this->responder(200, $this->servicio->obtenerPropiedad($id));
                        }
                        $this->responder(200, $this->servicio->listarPropiedades($_GET["id_inmobiliaria"] ?? null));
                    // Permite agregar fotos a una propiedad o crear una propiedad con sus fotos
                    case "POST":
                        if(isset($_GET["accion"]) && $_GET["accion"] === "fotos"){
                            $this->responder(201, $this->servicio->agregarFotos($id, $fotos));
                        }
                        // Toma los campos del formulario; las fotos llegan por separado en FILES
                        $cuerpo = $_POST;
                        $this->responder(201, $this->servicio->crearPropiedad($cuerpo, $fotos));
                    // Lee los cambios en JSON y modifica la propiedad indicada
                    case "PUT":
                        $cuerpo = json_decode(file_get_contents("php://input"), true) ?? [];
                        $this->responder(200, $this->servicio->modificarPropiedad($id, $cuerpo));
                    // Borra la foto indicada o, si no se indica una foto, borra la propiedad
                    case "DELETE":
                        if(isset($_GET["id_foto"])){
                            $this->servicio->borrarFoto($id, $_GET["id_foto"]);
                            $this->responder(204);
                        }
                        $this->servicio->borrarPropiedad($id);
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
    $controller = new PropiedadesController(new ServicioPropiedades(new Propiedades($conexion)), new SeguridadServicio(new Seguridad($conexion)));
    // Atiende la peticion que llego a este archivo
    $controller->atender();
?>

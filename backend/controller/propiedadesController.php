<?php
    // Punto de entrada: http://localhost/inmobiliarias/backend/controller/propiedadesController.php
    require_once __DIR__ . '/../config/cors.php';
    require_once __DIR__ . '/../api/conexion.php';
    require_once __DIR__ . '/../api/propiedades.php';
    require_once __DIR__ . '/../Servicio/servicioPropiedades.php';
    require_once __DIR__ . '/../config/autenticacion.php';

    class PropiedadesController{
        private ServicioPropiedades $servicio;
        private SeguridadServicio $seguridad;

        public function __construct(ServicioPropiedades $servicio, SeguridadServicio $seguridad){
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
            $id = $_GET["id_propiedad"] ?? null;
            $fotos = $_FILES["fotos"] ?? null;

            try{
                // Ver propiedades es publico; crear, modificar y borrar requieren token del usuario
                if($metodo !== "GET"){
                    obtenerIdUsuarioAutenticado($this->seguridad, (int) ($_GET["id_usuario"] ?? 0));
                }

                switch($metodo){
                    case "GET":
                        if($id !== null){
                            $this->responder(200, $this->servicio->obtenerPropiedad($id));
                        }
                        $this->responder(200, $this->servicio->listarPropiedades($_GET["id_inmobiliaria"] ?? null));
                    case "POST":
                        if(isset($_GET["accion"]) && $_GET["accion"] === "fotos"){
                            $cuerpo = json_decode(file_get_contents("php://input"), true) ?? [];
                            $this->responder(201, $this->servicio->crearPropiedad($cuerpo, $fotos));
                        }
                        $cuerpo = $_POST;
                        $this->responder(201, $this->servicio->crearPropiedad($cuerpo, $fotos));
                    case "PUT":
                        $cuerpo = json_decode(file_get_contents("php://input"), true) ?? [];
                        $this->responder(200, $this->servicio->modificarPropiedad($id, $cuerpo));
                    case "DELETE":
                        if(isset($_GET["id_foto"])){
                            $this->servicio->borrarFoto($id, $_GET["id_foto"]);
                            $this->responder(204);
                        }
                        $this->servicio->borrarPropiedad($id);
                        $this->responder(204);
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

    $controller = new PropiedadesController(new ServicioPropiedades(new Propiedades($conexion)), new SeguridadServicio(new Seguridad($conexion)));
    $controller->atender();
?>

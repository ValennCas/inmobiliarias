<?php
    // Endpoint publico (sin token), solo lectura.
    // Propiedades: http://localhost/inmobiliarias/backend/controller/propiedadesPublicasController.php
    // Estados:     http://localhost/inmobiliarias/backend/controller/propiedadesPublicasController.php?estados="ENVENTA"
    // Carga la conexion y los archivos que necesita este controlador
    require_once __DIR__ . '/../config/cors.php';
    require_once __DIR__ . '/../api/conexion.php';
    require_once __DIR__ . '/../api/propiedades.php';
    require_once __DIR__ . '/../Servicio/servicioPropiedades.php';

    class PropiedadesPublicasController{
        private ServicioPropiedades $servicio;

        // Guarda los servicios que va a usar el controlador
        public function __construct(ServicioPropiedades $servicio){
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

        // Permite consultar propiedades y estados sin iniciar sesion
        public function atender(){
            try{
                // Acepta solamente consultas mediante GET, otro metodo rechaza
                if($_SERVER["REQUEST_METHOD"] !== "GET"){
                    $this->responder(405, ["error" => "Metodo no permitido"]);
                }
                // Si viene el parametro estados, devuelve la lista de estados disponibles
                if(isset($_GET["estados"])){
                    $this->responder(200, $this->servicio->listarEstados());
                }
                // Usa los parametros de la URL para filtrar las propiedades
                $this->responder(200, $this->servicio->filtrarPropiedades($_GET));
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
    $controller = new PropiedadesPublicasController(new ServicioPropiedades(new Propiedades($conexion)));
    // Atiende la peticion que llego a este archivo
    $controller->atender();
?>

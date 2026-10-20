<?php
    // Carga la conexion y los archivos que necesita este controlador
    require_once __DIR__ . '/../config/cors.php';
    require_once __DIR__ . '/../api/conexion.php';
    require_once __DIR__ . '/../api/inmobiliaria.php';
    require_once __DIR__ . '/../Servicio/servicioInmobiliaria.php';

    class InmobiliariasPublicasController{
        private ServicioInmobiliaria $servicio;

        // Guarda los servicios que va a usar el controlador
        public function __construct(ServicioInmobiliaria $servicio){
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

        // Permite consultar la lista de inmobiliarias sin iniciar sesion para poder mostrarselas al usuario
        //En la interfaz publica
        public function atender(){
            try{
                // Acepta solamente consultas mediante GET, sino rechaza
                if($_SERVER["REQUEST_METHOD"] !== "GET"){
                    $this->responder(405, ["error" => "Metodo no permitido"]);
                }
                // Devuelve la lista de inmobiliarias
                $this->responder(200, $this->servicio->listarInmobiliaria());
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
    $controller = new InmobiliariasPublicasController(new ServicioInmobiliaria(new Inmobiliaria($conexion)));

    // Atiende la peticion que llego a este archivo
    $controller->atender();
?>
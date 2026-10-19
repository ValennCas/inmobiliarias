<?php
    require_once __DIR__ . '/../config/cors.php';
    require_once __DIR__ . '/../api/conexion.php';
    require_once __DIR__ . '/../api/inmobiliaria.php';
    require_once __DIR__ . '/../Servicio/servicioInmobiliaria.php';

    class InmobiliariasPublicasController{
        private ServicioInmobiliaria $servicio;

        public function __construct(ServicioInmobiliaria $servicio){
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
            try{
                if($_SERVER["REQUEST_METHOD"] !== "GET"){
                    $this->responder(405, ["error" => "Metodo no permitido"]);
                }
                $this->responder(200, $this->servicio->listarInmobiliaria());
            }
            catch(PDOException $error){
                $this->responder(500, ["error" => "Ocurrio un error, intente mas tarde!"]);
            }
            catch(Exception $error){
                $this->responder($error->getCode(), ["error" => $error->getMessage()]);
            }
        }
    }

    $controller = new InmobiliariasPublicasController(new ServicioInmobiliaria(new Inmobiliaria($conexion)));

    $controller->atender();
?>
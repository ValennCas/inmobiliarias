<?php
    // Endpoint publico (sin token), solo lectura.
    // Propiedades: http://localhost/inmobiliarias/backend/controller/propiedadesPublicasController.php
    // Estados:     http://localhost/inmobiliarias/backend/controller/propiedadesPublicasController.php?estados="ENVENTA"
    require_once __DIR__ . '/../config/cors.php';
    require_once __DIR__ . '/../api/conexion.php';
    require_once __DIR__ . '/../api/propiedades.php';
    require_once __DIR__ . '/../Servicio/servicioPropiedades.php';

    class PropiedadesPublicasController{
        private ServicioPropiedades $servicio;

        public function __construct(ServicioPropiedades $servicio){
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
                if(isset($_GET["estados"])){
                    $this->responder(200, $this->servicio->listarEstados());
                }
                $this->responder(200, $this->servicio->filtrarPropiedades($_GET));
            }
            catch(PDOException $error){
                $this->responder(500, ["error" => "Ocurrio un error, intente mas tarde!"]);
            }
            catch(Exception $error){
                $this->responder($error->getCode(), ["error" => $error->getMessage()]);
            }
        }
    }

    $controller = new PropiedadesPublicasController(new ServicioPropiedades(new Propiedades($conexion)));
    $controller->atender();
?>

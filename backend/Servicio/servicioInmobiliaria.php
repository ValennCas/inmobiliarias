<?php
    require_once '../api/conexion.php';
    require_once '../api/inmobiliaria.php';
    require_once 'seguridad.php';

    class ServicioInmobiliaria{
        private Inmobiliaria $inmobiliaria;
        private SeguridadServicio $seguridad;

        public function __construct(PDO $conexion){
            $this->inmobiliaria = new Inmobiliaria($conexion);
            $seguridad = new Seguridad($conexion);
            $this->seguridad = new SeguridadServicio($seguridad);
        }

        public function crearInmobiliaria(){
            header("Content-Type: application/json; charset=utf-8");
            $metodo = $_SERVER["REQUEST_METHOD"];
            if($metodo == "POST"){
                $obtener_datos = json_decode(file_get_contents('php://input'), true);
            }
            else{
                http_response_code(405);
                echo json_encode(["error" => "Metodo no permitido"]);
                exit();
            }

            $validarDatos = $this->validarDatos($obtener_datos);

            if (!$validarDatos){
                    http_response_code(400);
                    echo json_encode(["error" => "Datos invalidos o vacios, intente nuevamente!"]);
                    exit();
                }
            
            $id_usuario = $this->seguridad->verificarToken();
            $id_inmobiliaria = $this->inmobiliaria->obtenerInmobiliariaUsuario($id_usuario);

            if($id_inmobiliaria !== null){
                http_response_code(409);
                echo json_encode(["error" => "No se puede agregar mas de una inmobiliaria."]);
                exit();
            }
            
            try{
                $inmobiliaria = $this->inmobiliaria->crearInmobiliaria($id_usuario, $obtener_datos["nombre"], $obtener_datos["email"], $obtener_datos["area_telefono"],
                                    $obtener_datos["telefono"], $obtener_datos["provincia"], $obtener_datos["ciudad"], $obtener_datos["calle"], $obtener_datos["altura"]);    

                http_response_code(201);
                echo json_encode(["mensaje" => "Inmobiliaria agregada con exito", "inmobiliaria" => $inmobiliaria]);
                exit();
            }            
            catch(PDOException $error){
                http_response_code(500);
                echo json_encode(["error" => "No se pudo agregar la inmobiliaria, intente nuevamente!"]);
                exit();
            }      
        }

        public function actualizarInmobiliaria(){
            header("Content-Type: application/json; charset=utf-8");
            $metodo = $_SERVER["REQUEST_METHOD"];
            
            if($metodo == "PUT"){
                $obtener_datos = json_decode(file_get_contents("php://input"), true);
            }
            else{
                http_response_code(405);
                echo json_encode(["error" => "Metodo no permitido"]);
                exit();
            }
            $validarDatos = $this->validarDatos($obtener_datos);
                                                
            if (!$validarDatos){
                    http_response_code(400);
                    echo json_encode(["error" => "Datos invalidos o vacios, intente nuevamente!"]);
                    exit();
            }

            $id_usuario = $this->seguridad->verificarToken();
            $id_inmobiliaria = $this->inmobiliaria->obtenerInmobiliariaUsuario($id_usuario);

            if ($id_inmobiliaria === null){
                http_response_code(404);
                echo json_encode(["error" => "No se encontro una inmobiliaria asociada"]);
                exit();
            }
            
            try{
                $inmobiliaria = $this->inmobiliaria->actualizarInmobiliaria($id_inmobiliaria, $obtener_datos["nombre"], $obtener_datos["email"], $obtener_datos["area_telefono"], $obtener_datos["telefono"],
                                                $obtener_datos["provincia"], $obtener_datos["ciudad"], $obtener_datos["calle"], $obtener_datos["altura"]);
                http_response_code(200);
                echo json_encode(["mensaje" => "Datos actualizados con exito", "inmobiliaria" => $inmobiliaria]);
                exit();
            }
            catch(PDOException $error){
                http_response_code(500);
                echo json_encode(["error" => "No se pudo actualizar los datos, intente nuevamente!"]);  
                exit();              
            }
                               
        }

        public function buscarInmobiliaria(){
            header("Content-Type: application/json; charset=utf-8");
            $metodo = $_SERVER["REQUEST_METHOD"];

            if($metodo !== "GET"){
                http_response_code(405);
                echo json_encode(["error" => "Metodo no permitido"]);
                exit();
            }

            $id_usuario = $this->seguridad->verificarToken();
            $id_inmobiliaria = $this->inmobiliaria->obtenerInmobiliariaUsuario($id_usuario);
            
            if($id_inmobiliaria === null){
                http_response_code(404);
                echo json_encode(["error" => "No se encontro una inmobiliaria asociada"]);
                exit();
            }

            $inmobiliaria = $this->inmobiliaria->consultarInmobiliaria($id_inmobiliaria);
            http_response_code(200);
            echo json_encode(["inmobiliaria" => $inmobiliaria]);
            exit();
        }

        public function validarDatos($obtener_datos){
            if(!is_array($obtener_datos)){
                return false;
            }
            if (!isset($obtener_datos["nombre"]) || trim($obtener_datos["nombre"]) === ""
                || !isset($obtener_datos["email"]) || trim($obtener_datos["email"]) === ""
                || !filter_var($obtener_datos["email"], FILTER_VALIDATE_EMAIL)
                || !isset($obtener_datos["area_telefono"]) || trim($obtener_datos["area_telefono"]) === ""
                || !isset($obtener_datos["telefono"]) || trim($obtener_datos["telefono"]) === ""
                || !isset($obtener_datos["provincia"]) || trim($obtener_datos["provincia"]) === ""
                || !isset($obtener_datos["ciudad"]) || trim($obtener_datos["ciudad"]) === ""
                || !isset($obtener_datos["calle"]) || trim($obtener_datos["calle"]) === ""
                || !isset($obtener_datos["altura"]) || trim($obtener_datos["altura"]) === ""){
                    return false;
                }
            return true; 
        }
    }
?>
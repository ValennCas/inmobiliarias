<?php
    // Carga los archivos que necesita este servicio
    require_once __DIR__ . '/../api/login.php';

    class LoginUsuario{
        private Login $login;

        // Prepara el acceso a los datos que necesita este servicio
        public function __construct(PDO $conexion){
            $this->login = new Login($conexion);
        }

        // Comprueba el usuario y la contrasenia, guarda un token de sesion y devuelve los datos 
        // para iniciar sesion
        public function loguearUsuario($obtener_cuerpo){
            $this->validarDatos($obtener_cuerpo);

            $usuario = $this->login->login($obtener_cuerpo["usuario"]);

            if($usuario == false){
                throw new Exception("Usuario y/o contraseña incorrectos", 401);
            }

            // Compara la contrasenia ingresada con el hash guardado del usuario
            $verificar_password = password_verify($obtener_cuerpo["password"], $usuario["password"]);

            if(!$verificar_password){
                throw new Exception("Usuario y/o contraseña incorrectos", 401);
            }

            // Crea el token que se va a usar para autenticar las siguientes peticiones
            $token_generado = $this->generarToken();

            try{
                // Guarda el hash y el vencimiento del token; el valor original se devuelve al cliente
                $guardarToken = $this->login->guardarToken($usuario["id_usuario"], $token_generado["token_hash"], $token_generado["fecha_expiracion"]);
            }
            catch(PDOException $error){
                throw new Exception("No se pudo iniciar sesion, intente nuevamente", 500);
            }

            if(!$guardarToken){
                throw new Exception("No se pudo iniciar sesion, intente nuevamente", 500);
            }

            return [
                "token" => $token_generado["token"],
                "id_usuario" => $usuario["id_usuario"],
                "id_inmobiliaria" => $usuario["id_inmobiliaria"]
            ];
        }

        // Comprueba que lleguen los datos y que el usuario y la contrasenia esten completos
        private function validarDatos($obtener_cuerpo){
            if(!is_array($obtener_cuerpo)){
                throw new Exception("Datos invalidos", 400);
            }

            if(!isset($obtener_cuerpo["usuario"]) || trim($obtener_cuerpo["usuario"]) === ""){
                throw new Exception("Falta completar el usuario", 400);
            }

            if(!isset($obtener_cuerpo["password"]) || trim($obtener_cuerpo["password"]) === ""){
                throw new Exception("Falta completar la contraseña", 400);
            }
        }

        // Genera un token aleatorio, calcula su hash y le da una duracion de una hora
        private function generarToken(){
            $token = bin2hex(random_bytes(32));
            $token_hash = hash("sha256", $token);
            $fecha_expiracion = (new DateTime())->modify("+1 hours")->format("Y-m-d H:i:s");

            return [
                "token" => $token,
                "token_hash" => $token_hash,
                "fecha_expiracion" => $fecha_expiracion
            ];
        }
    }
?>
<?php
    // Carga los archivos que necesita este servicio
    require_once __DIR__ . '/../api/recuperarContrasenia.php';
    require_once __DIR__ . '/servicios.php';

    class ServicioRecuperarContrasenia{
        private RecuperarContrasenia $recuperarContrasenia;

        // Prepara el acceso a los datos que necesita este servicio
        public function __construct(RecuperarContrasenia $recuperarContrasenia){
            $this->recuperarContrasenia = $recuperarContrasenia;
        }

        // Busca al usuario por su email y le envia un codigo de cuatro numeros para recuperar la contrasenia
        public function solicitarTokenRecuperacion($datos){
            $email = trim($datos["email"] ?? "");
            if($email === ""){
                throw new Exception("Debe ingresar un correo electronico.", 400);
            }

            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                throw new Exception("El correo electronico no es valido.", 400);
            }

            $usuario = $this->recuperarContrasenia->buscarUsuarioPorEmail($email);

            if($usuario === false){
                throw new Exception("No existe un usuario registrado con ese correo.", 404);
            }

            // Elimina cualquier token anterior del usuario
            $this->recuperarContrasenia->eliminarTokensUsuario($usuario["id_usuario"]);

            // Token de exactamente 4 digitos
            $token = (string) random_int(1000, 9999);

            // Le colocamos una duracion de 10 minutos
            $fecha_expiracion = date("Y-m-d H:i:s", time() + 600);

            $this->recuperarContrasenia->guardarToken($usuario["id_usuario"], $token, $fecha_expiracion);

            // Arma el correo con el codigo de recuperacion y su tiempo de validez
            $html = "<p>Hola " . htmlspecialchars($usuario["nombre_usuario"]) . ",</p>"
                  . "<p>Recibimos una solicitud para recuperar tu contraseña de Habita.</p>"
                  . "<p>Tu codigo de recuperacion es:</p>"
                  . "<h2>" . htmlspecialchars($token) . "</h2>"
                  . "<p>Este codigo tiene una validez de 10 minutos.</p>"
                  . "<p>Si no solicitaste este cambio, podes ignorar este correo.</p>";

            $enviado = enviarCorreo($usuario["email"], "Codigo para recuperar tu contraseña", $html);

            // Si el correo no se pudo enviar, informa el error
            if(!$enviado){
                $this->recuperarContrasenia->eliminarTokensUsuario($usuario["id_usuario"]);
                throw new Exception("No se pudo enviar el correo de recuperacion.", 500);
            }

            return ["mensaje" => "Se envio un codigo de recuperacion a tu correo."];
        }

        // Comprueba el formato del codigo y busca si sigue disponible despues de limpiar los vencidos
        public function verificarTokenContra($datos){
            $token = trim((string)($datos["token"] ?? ""));

            if($token === ""){
                throw new Exception("Debe ingresar el codigo de recuperacion.", 400);
            }

            // Tiene que tener exactamente 4 numeros
            if(!preg_match('/^\d{4}$/', $token)){
                throw new Exception("El codigo debe tener exactamente 4 digitos.", 400);
            }

            // Limpia tokens que ya pasaron los 10 minutos
            $this->recuperarContrasenia->eliminarTokensVencidos();

            $recuperacion = $this->recuperarContrasenia->buscarToken($token);

            if($recuperacion === false){
                throw new Exception("El codigo es incorrecto o esta vencido.", 400);
            }

            return ["mensaje" => "Codigo verificado correctamente."];
        }

        // Valida el codigo y la nueva contrasenia, guarda su hash y elimina el codigo usado
        public function cambiarContrasenia($datos){
            $token = trim((string)($datos["token"] ?? ""));
            $contrasenia = $datos["password"] ?? "";
            $repetirContrasenia = $datos["repetirPassword"] ?? "";

            if(!preg_match('/^\d{4}$/', $token)){
                throw new Exception("El codigo de recuperacion es invalido.", 400);
            }

            if($contrasenia === "" || $repetirContrasenia === ""){
                throw new Exception("Debe completar ambas contraseñas.", 400);
            }
            if($contrasenia !== $repetirContrasenia){
                throw new Exception("Las contraseñas no coinciden.", 400);
            }

            if(strlen($contrasenia) < 6){
                throw new Exception("La contraseña debe tener al menos 6 caracteres.", 400);
            }

            if(strlen($contrasenia) > 100){
                throw new Exception("La contraseña es demasiado larga.", 400);
            }
            $this->recuperarContrasenia->eliminarTokensVencidos();
            $recuperacion = $this->recuperarContrasenia->buscarToken($token);
            if($recuperacion === false){
                throw new Exception("El codigo es incorrecto o esta vencido.", 400);
            }
            // Genera el hash de la nueva contrasenia antes de guardarla
            $contraseniaHash = password_hash($contrasenia, PASSWORD_DEFAULT);
            $this->recuperarContrasenia->cambiarContrasenia($recuperacion["id_usuario"], $contraseniaHash);
            // Borra el codigo usado para que no vuelva a servir para otro cambio
            $this->recuperarContrasenia->eliminarToken($recuperacion["id_recuperacion"]);

            return ["mensaje" => "La contraseña fue modificada correctamente."];
        }
    }
?>
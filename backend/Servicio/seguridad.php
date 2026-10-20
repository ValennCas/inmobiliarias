<?php
    // Carga los archivos que necesita este servicio
    require_once __DIR__ . '/../api/seguridad.php';

    class SeguridadServicio{
        private Seguridad $seguridad;

        // Prepara el acceso a los datos que necesita este servicio
        public function __construct(Seguridad $seguridad){
            $this->seguridad = $seguridad;
        }

        // Busca el hash del token, comprueba que no haya vencido y devuelve el id del usuario
        public function verificarToken($token_hash){
            $obtener_autorizacion = $this->seguridad->validarToken($token_hash);

            if($obtener_autorizacion == false){
                throw new Exception("Token invalido", 401);
            }

            // Prepara las fechas para comprobar si el token ya vencio
            $fecha_actual = new DateTime();
            $fecha_expiracion = new DateTime($obtener_autorizacion["fecha_expiracion"]);

            // Rechaza el token si su fecha de vencimiento ya paso
            if($fecha_expiracion < $fecha_actual){
                throw new Exception("Token vencido", 401);
            }

            return $obtener_autorizacion["id_usuario"];
        }
    }
?>
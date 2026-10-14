<?php
    require_once __DIR__ . '/../api/seguridad.php';

    class SeguridadServicio{
        private Seguridad $seguridad;

        public function __construct(Seguridad $seguridad){
            $this->seguridad = $seguridad;
        }

        public function verificarToken($token_hash){
            $obtener_autorizacion = $this->seguridad->validarToken($token_hash);

            if($obtener_autorizacion == false){
                throw new Exception("Token invalido", 401);
            }

            $fecha_actual = new DateTime();
            $fecha_expiracion = new DateTime($obtener_autorizacion["fecha_expiracion"]);

            if($fecha_expiracion < $fecha_actual){
                throw new Exception("Token vencido", 401);
            }

            return $obtener_autorizacion["id_usuario"];
        }
    }
?>
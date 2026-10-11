<?php
    require_once 'conexion.php'

    class Seguridad{
        private PDO $conexion;

        public function __construct(PDO $conexion){
            $this->conexion = $conexion; 
        }

        public function validarToken($token){
            $consulta = $this->conexion->prepare('SELECT id_usuario, fecha_expiracion FROM token WHERE token = :token');
            $consulta->execute([":token" => $token]);
            return $consulta->fetch(PDO::FETCH_ASSOC);
        }
    }
?>
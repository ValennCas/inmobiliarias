<?php
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

        public function eliminarToken($id_usuario){
            $consulta = $this->conexion->prepare('DELETE FROM token WHERE id_usuario = :id_usuario');
            return $consulta->execute([":id_usuario" => $id_usuario]);
        }
    }
?>
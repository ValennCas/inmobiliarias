<?php
    class Seguridad{
        private PDO $conexion;

        // Guarda la conexion a la base de datos para usarla en las consultas
        public function __construct(PDO $conexion){
            $this->conexion = $conexion; 
        }

        // Busca el token recibido y devuelve el usuario asociado y su fecha de vencimiento
        public function validarToken($token){
            $consulta = $this->conexion->prepare('SELECT id_usuario, fecha_expiracion FROM token WHERE token = :token');
            $consulta->execute([":token" => $token]);
            return $consulta->fetch(PDO::FETCH_ASSOC);
        }

        // Borra los tokens de sesion asociados al usuario indicado
        public function eliminarToken($id_usuario){
            $consulta = $this->conexion->prepare('DELETE FROM token WHERE id_usuario = :id_usuario');
            return $consulta->execute([":id_usuario" => $id_usuario]);
        }
    }
?>
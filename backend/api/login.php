<?php
    require_once 'conexion.php'

    class Login{
        private PDO $conexion;

        public function conexion(PDO $conexion){
            $this->conexion = $conexion;
        }

        public function login($usuario){
            $consulta = $this->conexion->prepare('SELECT * FROM usuario WHERE email = :usuario OR nombre_usuario = :usuario')
            $consulta->execute(["usuario" => $usuario])
            return $consulta->fetch(PDO::FETCH_ASSOC)
        }

        public function generarToken(){
            $token= bin2hex(random_bytes(32));

            return $token;
        }
    }

?>
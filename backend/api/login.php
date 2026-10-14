<?php
     class Login{
        private PDO $conexion;

        public function __construct(PDO $conexion){
            $this->conexion = $conexion;
        }

        public function login($usuario){
            $consulta = $this->conexion->prepare('SELECT * FROM usuario WHERE email = :usuario OR nombre_usuario = :usuario');
            $consulta->execute(["usuario" => $usuario]);
            return $consulta->fetch(PDO::FETCH_ASSOC);
        }

        public function guardarToken($id_usuario, $token, $fecha_expiracion){
            $guardarToken = $this->conexion->prepare(
                "INSERT INTO token(id_usuario, token, fecha_expiracion)
                VALUES(:id_usuario, :token, :fecha_expiracion)
                ON DUPLICATE KEY UPDATE
                    token= :token,
                    fecha_expiracion= :fecha_expiracion"
            );
            return $guardarToken->execute([
                ":id_usuario" => $id_usuario,
                ":token" => $token,
                ":fecha_expiracion" => $fecha_expiracion,
            ]);
        }
    }

?>
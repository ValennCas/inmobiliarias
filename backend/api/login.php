<?php
     class Login{
        private PDO $conexion;

        // Guarda la conexion a la base de datos para usarla en las consultas
        public function __construct(PDO $conexion){
            $this->conexion = $conexion;
        }

        // Busca una cuenta que coincida con el email o el nombre de usuario recibido
        public function login($usuario){
            $consulta = $this->conexion->prepare('SELECT * FROM usuario WHERE email = :usuario OR nombre_usuario = :usuario');
            $consulta->execute(["usuario" => $usuario]);
            return $consulta->fetch(PDO::FETCH_ASSOC);
        }

        // Guarda el token de sesion y su vencimiento; si hay una clave duplicada, actualiza esos valores
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
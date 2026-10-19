<?php
    class RecuperarContrasenia{
        private PDO $conexion;

        public function __construct(PDO $conexion){
            $this->conexion = $conexion;
        }

        public function buscarUsuarioPorEmail($email){
            $consulta = $this->conexion->prepare("SELECT id_usuario, email, nombre_usuario FROM usuario WHERE email = :email");
            $consulta->execute([":email" => $email]);
            return $consulta->fetch(PDO::FETCH_ASSOC);
        }

        public function eliminarTokensUsuario($id_usuario){
            $consulta = $this->conexion->prepare("DELETE FROM recuperacion_contrasenia WHERE id_usuario = :id_usuario");
            $consulta->execute([":id_usuario" => $id_usuario]);
        }

        public function guardarToken($id_usuario, $token, $fecha_expiracion){
            $consulta = $this->conexion->prepare("INSERT INTO recuperacion_contrasenia(id_usuario, token, fecha_expiracion) VALUES (:id_usuario, :token, :fecha_expiracion)");
            $consulta->execute([":id_usuario" => $id_usuario,":token" => $token, ":fecha_expiracion" => $fecha_expiracion]);
        }

        public function eliminarTokensVencidos(){
            $consulta = $this->conexion->prepare("DELETE FROM recuperacion_contrasenia WHERE fecha_expiracion <= NOW()");
            $consulta->execute();
        }

        public function buscarToken($token){
            $consulta = $this->conexion->prepare("SELECT id_recuperacion, id_usuario, token, fecha_expiracion FROM recuperacion_contrasenia WHERE token = :token AND fecha_expiracion > NOW() LIMIT 1");
            $consulta->execute([":token" => $token]);
            return $consulta->fetch(PDO::FETCH_ASSOC);
        }

        public function cambiarContrasenia($id_usuario, $password){
            $consulta = $this->conexion->prepare(
                "UPDATE usuario SET password = :password WHERE id_usuario = :id_usuario");
            $consulta->execute([":password" => $password, ":id_usuario" => $id_usuario]);
        }

        public function eliminarToken($id_recuperacion){
            $consulta = $this->conexion->prepare("DELETE FROM recuperacion_contrasenia WHERE id_recuperacion = :id_recuperacion");
            $consulta->execute([":id_recuperacion" => $id_recuperacion]);
        }
    }
?>
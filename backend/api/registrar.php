<?php
    require "conexion.php"

    class Registrar{
        private PDO $conexion;

        public function conexion(PDO $conexion){
            $this->conexion = $conexion;
        }    

        public function buscarUsuario($email){
            $consulta = $this->conexion->prepare('SELECT id_usuario FROM usuario WHERE email = :email');
            $consulta->execute([":email" => $email]);
            $usuario_registrado = $consulta->fetch(PDO::FETCH_ASSOC);

            return $usuario_registrado;
        }

        public function insertarUsuario($email, $password, $nombre_usuario, $apellido_usuario){
            $insertar = $this->conexion->prepare("INSERT INTO usuario(email, password, nombre_usuario, apellido_usuario) 
                                                VALUES(:email, :password, :nombre_usuario, :apellido_usuario)");
            return $insertar->execute([":email" => $email, ":password" => $password, ":nombre_usuario" => $nombre_usuario, ":apellido_usuario" => $apellido_usuario])

        } 
    }
    
    
?>
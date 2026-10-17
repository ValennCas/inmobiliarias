<?php
    class Usuario{
        private PDO $conexion;

        public function __construct(PDO $conexion){
            $this->conexion = $conexion;
        }

        public function obtenerUsuario($id_usuario){
            $usuario = $this->conexion->prepare('SELECT email, nombre_usuario, apellido_usuario FROM usuario WHERE id_usuario = :id_usuario');
            $usuario->execute([":id_usuario" => $id_usuario]);
            return $usuario->fetch(PDO::FETCH_ASSOC);
        }

        public function actualizarUsuario($id_usuario, $nombre_usuario, $apellido_usuario, $email){
            $usuario_editar = $this->conexion->prepare('UPDATE usuario SET email = :email, nombre_usuario = :nombre_usuario, apellido_usuario = :apellido_usuario WHERE id_usuario = :id_usuario');
            $usuario_editar->execute([":id_usuario" => $id_usuario, ":email" => $email, ":nombre_usuario" => $nombre_usuario, ":apellido_usuario" => $apellido_usuario]);
        }
    }
?>
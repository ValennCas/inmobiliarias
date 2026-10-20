<?php
    class Usuario{
        private PDO $conexion;

        // Guarda la conexion a la base de datos para usarla en las consultas
        public function __construct(PDO $conexion){
            $this->conexion = $conexion;
        }

        // Busca el email, el nombre y el apellido del usuario indicado
        public function obtenerUsuario($id_usuario){
            $usuario = $this->conexion->prepare('SELECT email, nombre_usuario, apellido_usuario FROM usuario WHERE id_usuario = :id_usuario');
            $usuario->execute([":id_usuario" => $id_usuario]);
            return $usuario->fetch(PDO::FETCH_ASSOC);
        }

        // Actualiza el email, el nombre y el apellido y devuelve la consulta ejecutada
        public function actualizarUsuario($id_usuario, $nombre_usuario, $apellido_usuario, $email){
            $usuario_editar = $this->conexion->prepare('UPDATE usuario SET email = :email, nombre_usuario = :nombre_usuario, apellido_usuario = :apellido_usuario WHERE id_usuario = :id_usuario');
            $usuario_editar->execute([":id_usuario" => $id_usuario, ":email" => $email, ":nombre_usuario" => $nombre_usuario, ":apellido_usuario" => $apellido_usuario]);
            return $usuario_editar;
        }

        //Buscamos el usuario por el ID y obtenemos su contraseña
        // Busca el id y la contrasenia guardada del usuario para que el servicio pueda comprobarla
        public function buscarPorId($id_usuario){
            $consulta = $this->conexion->prepare("SELECT id_usuario, password FROM usuario WHERE id_usuario = :id_usuario");
            $consulta->execute([":id_usuario" => $id_usuario]);
            return $consulta->fetch(PDO::FETCH_ASSOC);
        }

        //cambiamos la contraseña de la cuenta
        // Actualiza el campo password del usuario con el valor recibido del servicio
        public function cambiarContrasenia($id_usuario, $password){
           $consulta = $this->conexion->prepare("UPDATE usuario SET password = :password WHERE id_usuario = :id_usuario");
            $consulta->execute([":id_usuario" => $id_usuario, ":password" => $password]);
        }
    }
?>
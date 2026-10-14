<?php
    class Inquilinos{
        private PDO $conexion;

        public function __construct(PDO $conexion){
            $this->conexion = $conexion;
        }

        public function emailExiste($email, $id_excluido = null){
            $consulta = $this->conexion->prepare('SELECT id_inquilino FROM inquilino WHERE email = :email AND id_inquilino <> :id_excluido');
            $consulta->execute([":email" => $email, ":id_excluido" => $id_excluido ?? 0]);
            return $consulta->fetch(PDO::FETCH_ASSOC) !== false;
        }

        public function insertarInquilino($datos){
            $insertar = $this->conexion->prepare('INSERT INTO inquilino(nombre, apellido, email, area_telefono, telefono)
                                                VALUES(:nombre, :apellido, :email, :area_telefono, :telefono)');
            $insertar->execute([
                ":nombre" => $datos["nombre"],
                ":apellido" => $datos["apellido"],
                ":email" => $datos["email"],
                ":area_telefono" => $datos["area_telefono"],
                ":telefono" => $datos["telefono"]
            ]);
            return (int) $this->conexion->lastInsertId();
        }

        public function listarInquilinos(){
            $consulta = $this->conexion->query('SELECT id_inquilino, nombre, apellido, email, area_telefono, telefono FROM inquilino');
            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        }

        public function buscarInquilino($id_inquilino){
            $consulta = $this->conexion->prepare('SELECT id_inquilino, nombre, apellido, email, area_telefono, telefono
                                                FROM inquilino WHERE id_inquilino = :id_inquilino');
            $consulta->execute([":id_inquilino" => $id_inquilino]);
            return $consulta->fetch(PDO::FETCH_ASSOC);
        }

        public function modificarInquilino($id_inquilino, $datos){
            $modificar = $this->conexion->prepare('UPDATE inquilino
                                                SET nombre = :nombre, apellido = :apellido, email = :email,
                                                    area_telefono = :area_telefono, telefono = :telefono
                                                WHERE id_inquilino = :id_inquilino');
            $modificar->execute([
                ":nombre" => $datos["nombre"],
                ":apellido" => $datos["apellido"],
                ":email" => $datos["email"],
                ":area_telefono" => $datos["area_telefono"],
                ":telefono" => $datos["telefono"],
                ":id_inquilino" => $id_inquilino
            ]);
        }

        public function eliminarInquilino($id_inquilino){
            $eliminar = $this->conexion->prepare('DELETE FROM inquilino WHERE id_inquilino = :id_inquilino');
            $eliminar->execute([":id_inquilino" => $id_inquilino]);
        }
    }
?>

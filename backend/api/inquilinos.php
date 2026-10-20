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
            try{
                $this->conexion->beginTransaction();
                /*DEVOLVER LA PROPIEDAD A EN ALQUILER*/
                $actualizar = $this->conexion->prepare('UPDATE propiedad p
                                                        INNER JOIN inmobiliaria_propiedad ip
                                                            ON ip.id_propiedad = p.id_propiedad
                                                        INNER JOIN alquiler a
                                                            ON a.id_inmobiliaria_propiedad = ip.id_inmobiliaria_propiedad
                                                        INNER JOIN estado_propiedad ep
                                                            ON ep.estado = :estado_disponible
                                                        SET p.id_estado_propiedad = ep.id_estado_propiedad
                                                        WHERE a.id_inquilino = :id_inquilino');

                $actualizar->execute([":estado_disponible" => "EN ALQUILER", ":id_inquilino" => $id_inquilino]);
                /*ELIMINAR EL ALQUILER*/
                $eliminarAlquiler = $this->conexion->prepare('DELETE FROM alquiler
                                                            WHERE id_inquilino = :id_inquilino');
                $eliminarAlquiler->execute([":id_inquilino" => $id_inquilino]);
                /*ELIMINAR EL INQUILINO*/
                $eliminarInquilino = $this->conexion->prepare('DELETE FROM inquilino
                                                            WHERE id_inquilino = :id_inquilino');
                $eliminarInquilino->execute([":id_inquilino" => $id_inquilino]);
                $this->conexion->commit();
                return true;
            }
            catch(Exception $error){
                if($this->conexion->inTransaction()){
                    $this->conexion->rollBack();
                }
                throw $error;
            }
        }
    }
?>

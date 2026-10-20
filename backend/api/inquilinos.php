<?php
    class Inquilinos{
        private PDO $conexion;

        // Guarda la conexion a la base de datos para usarla en las consultas
        public function __construct(PDO $conexion){
            $this->conexion = $conexion;
        }

        // Busca si otro inquilino usa ese email, dejando afuera el id indicado
        public function emailExiste($email, $id_excluido = null){
            $consulta = $this->conexion->prepare('SELECT id_inquilino FROM inquilino WHERE email = :email AND id_inquilino <> :id_excluido');
            $consulta->execute([":email" => $email, ":id_excluido" => $id_excluido ?? 0]);
            return $consulta->fetch(PDO::FETCH_ASSOC) !== false;
        }

        // Guarda los datos del inquilino y devuelve el id generado
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

        // Devuelve los datos de todos los inquilinos
        public function listarInquilinos(){
            $consulta = $this->conexion->query('SELECT id_inquilino, nombre, apellido, email, area_telefono, telefono FROM inquilino');
            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        }

        // Busca los datos de un inquilino por su id
        public function buscarInquilino($id_inquilino){
            $consulta = $this->conexion->prepare('SELECT id_inquilino, nombre, apellido, email, area_telefono, telefono
                                                FROM inquilino WHERE id_inquilino = :id_inquilino');
            $consulta->execute([":id_inquilino" => $id_inquilino]);
            return $consulta->fetch(PDO::FETCH_ASSOC);
        }

        // Actualiza los datos personales y de contacto del inquilino indicado
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

        // Deja sus propiedades en alquiler y borra los alquileres y el inquilino en una misma transaccion
        public function eliminarInquilino($id_inquilino){
            try{
                // Inicia una transaccion para guardar juntos los cambios de esta operacion
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
                // Prepara el borrado de los alquileres que corresponden a esta operacion
                $eliminarAlquiler = $this->conexion->prepare('DELETE FROM alquiler
                                                            WHERE id_inquilino = :id_inquilino');
                $eliminarAlquiler->execute([":id_inquilino" => $id_inquilino]);
                /*ELIMINAR EL INQUILINO*/
                // Prepara el borrado del inquilino una vez eliminados los alquileres correspondientes
                $eliminarInquilino = $this->conexion->prepare('DELETE FROM inquilino
                                                            WHERE id_inquilino = :id_inquilino');
                $eliminarInquilino->execute([":id_inquilino" => $id_inquilino]);
                // Confirma los cambios de la transaccion
                $this->conexion->commit();
                return true;
            }
            catch(Exception $error){
                // Comprueba que haya una transaccion activa antes de intentar deshacerla
                if($this->conexion->inTransaction()){
                    // Deshace los cambios de la transaccion antes de devolver el error
                    $this->conexion->rollBack();
                }
                throw $error;
            }
        }
    }
?>

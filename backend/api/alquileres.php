<?php
    class Alquileres{
        private PDO $conexion;

        public function __construct(PDO $conexion){
            $this->conexion = $conexion;
        }

        public function inquilinoExiste($id_inquilino){
            $consulta = $this->conexion->prepare('SELECT id_inquilino FROM inquilino WHERE id_inquilino = :id');
            $consulta->execute([":id" => $id_inquilino]);
            return $consulta->fetch(PDO::FETCH_ASSOC) !== false;
        }

        // Devuelve la fila de inmobiliaria_propiedad con el estado actual de la propiedad, o false si no estan vinculadas
        public function buscarVinculo($id_inmobiliaria, $id_propiedad){
            $consulta = $this->conexion->prepare('SELECT ip.id_inmobiliaria_propiedad, e.estado
                                                FROM inmobiliaria_propiedad ip
                                                INNER JOIN propiedad p ON p.id_propiedad = ip.id_propiedad
                                                INNER JOIN estado_propiedad e ON e.id_estado_propiedad = p.id_estado_propiedad
                                                WHERE ip.id_inmobiliaria = :id_inmobiliaria AND ip.id_propiedad = :id_propiedad');
            $consulta->execute([":id_inmobiliaria" => $id_inmobiliaria, ":id_propiedad" => $id_propiedad]);
            return $consulta->fetch(PDO::FETCH_ASSOC);
        }

        // Crea el alquiler y cambia el estado de la propiedad en una sola transaccion
        public function insertarAlquiler($datos, $id_inmobiliaria_propiedad, $estado_nuevo){
            $this->conexion->beginTransaction();
            try{
                $alquiler = $this->conexion->prepare('INSERT INTO alquiler(id_inmobiliaria_propiedad, id_inquilino, precio, fecha_inicio, fecha_fin)
                                                    VALUES(:id_inmobiliaria_propiedad, :id_inquilino, :precio, :fecha_inicio, :fecha_fin)');
                $alquiler->execute([
                    ":id_inmobiliaria_propiedad" => $id_inmobiliaria_propiedad,
                    ":id_inquilino" => $datos["id_inquilino"],
                    ":precio" => $datos["precio"],
                    ":fecha_inicio" => $datos["fecha_inicio"],
                    ":fecha_fin" => $datos["fecha_fin"] ?? null
                ]);
                $id_alquiler = (int) $this->conexion->lastInsertId();

                $estado = $this->conexion->prepare('UPDATE propiedad p
                                                    INNER JOIN inmobiliaria_propiedad ip ON ip.id_propiedad = p.id_propiedad
                                                    SET p.id_estado_propiedad = (SELECT id_estado_propiedad FROM estado_propiedad WHERE estado = :estado)
                                                    WHERE ip.id_inmobiliaria_propiedad = :id_inmobiliaria_propiedad');
                $estado->execute([":estado" => $estado_nuevo, ":id_inmobiliaria_propiedad" => $id_inmobiliaria_propiedad]);

                $this->conexion->commit();
                return $id_alquiler;
            }
            catch(PDOException $error){
                $this->conexion->rollBack();
                throw $error;
            }
        }

        // Los filtros son opcionales: id_inquilino e id_inmobiliaria
        public function listarAlquileres($id_inquilino = null, $id_inmobiliaria = null){
            $consulta = $this->conexion->prepare('SELECT a.id_alquiler, a.precio, a.fecha_inicio, a.fecha_fin,
                                                        a.id_inquilino, i.nombre AS nombre_inquilino, i.apellido AS apellido_inquilino,
                                                        ip.id_inmobiliaria, ip.id_propiedad, p.titulo, p.descripcion,
                                                        d.provincia, d.ciudad, d.calle, d.altura,
                                                        (SELECT pf.ruta_foto
                                                            FROM propiedad_foto pf
                                                            WHERE pf.id_propiedad = p.id_propiedad
                                                            ORDER BY pf.orden ASC
                                                            LIMIT 1) AS foto
                                                FROM alquiler a
                                                INNER JOIN inquilino i ON i.id_inquilino = a.id_inquilino
                                                INNER JOIN inmobiliaria_propiedad ip ON ip.id_inmobiliaria_propiedad = a.id_inmobiliaria_propiedad
                                                INNER JOIN propiedad p ON p.id_propiedad = ip.id_propiedad
                                                INNER JOIN direccion d ON d.id_direccion = p.id_direccion
                                                INNER JOIN estado_propiedad ep ON ep.id_estado_propiedad = p.id_estado_propiedad
                                                WHERE ep.estado = "ALQUILADO"
                                                AND (:id_inquilino IS NULL OR a.id_inquilino = :id_inquilino2)
                                                AND (:id_inmobiliaria IS NULL OR ip.id_inmobiliaria = :id_inmobiliaria2)');
            $consulta->execute([
                ":id_inquilino" => $id_inquilino,
                ":id_inquilino2" => $id_inquilino,
                ":id_inmobiliaria" => $id_inmobiliaria,
                ":id_inmobiliaria2" => $id_inmobiliaria
            ]);
            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        }

        public function buscarAlquiler($id_alquiler){
            $consulta = $this->conexion->prepare('SELECT a.id_alquiler, a.precio, a.fecha_inicio, a.fecha_fin,
                                                        a.id_inquilino, i.nombre AS nombre_inquilino, i.apellido AS apellido_inquilino,
                                                        i.email AS email_inquilino,
                                                        i.area_telefono AS area_telefono_inquilino,
                                                        i.telefono AS telefono_inquilino,
                                                        ip.id_inmobiliaria, ip.id_propiedad, p.titulo, p.descripcion,
                                                        d.provincia, d.ciudad, d.calle, d.altura,
                                                        (SELECT pf.ruta_foto
                                                            FROM propiedad_foto pf
                                                            WHERE pf.id_propiedad = p.id_propiedad
                                                            ORDER BY pf.orden ASC
                                                            LIMIT 1) AS foto
                                                FROM alquiler a
                                                INNER JOIN inquilino i ON i.id_inquilino = a.id_inquilino
                                                INNER JOIN inmobiliaria_propiedad ip ON ip.id_inmobiliaria_propiedad = a.id_inmobiliaria_propiedad
                                                INNER JOIN propiedad p ON p.id_propiedad = ip.id_propiedad
                                                INNER JOIN direccion d ON d.id_direccion = p.id_direccion
                                                WHERE a.id_alquiler = :id_alquiler');
            $consulta->execute([":id_alquiler" => $id_alquiler]);
            return $consulta->fetch(PDO::FETCH_ASSOC);
        }

        // Solo se pueden cambiar precio y fechas; la propiedad y el inquilino no cambian
        public function modificarAlquiler($id_alquiler, $datos){
            $modificar = $this->conexion->prepare('UPDATE alquiler
                                                SET precio = :precio, fecha_inicio = :fecha_inicio, fecha_fin = :fecha_fin
                                                WHERE id_alquiler = :id_alquiler');
            $modificar->execute([
                ":precio" => $datos["precio"],
                ":fecha_inicio" => $datos["fecha_inicio"],
                ":fecha_fin" => $datos["fecha_fin"] ?? null,
                ":id_alquiler" => $id_alquiler
            ]);
        }

        // Borra el alquiler, elimina el inquilino y deja la propiedad en el estado indicado
        public function eliminarAlquiler($id_alquiler, $estado_nuevo){
            $this->conexion->beginTransaction();
            try{
                $consultaInquilino = $this->conexion->prepare('SELECT id_inquilino
                                                            FROM alquiler
                                                            WHERE id_alquiler = :id_alquiler');

                $consultaInquilino->execute([
                    ":id_alquiler" => $id_alquiler
                ]);
                $alquiler = $consultaInquilino->fetch(PDO::FETCH_ASSOC);
                if($alquiler === false){
                    throw new Exception("No existe el alquiler", 404);
                }
                $id_inquilino = $alquiler["id_inquilino"];
                $estado = $this->conexion->prepare('UPDATE propiedad p
                                                    INNER JOIN inmobiliaria_propiedad ip
                                                        ON ip.id_propiedad = p.id_propiedad
                                                    INNER JOIN alquiler a
                                                        ON a.id_inmobiliaria_propiedad = ip.id_inmobiliaria_propiedad
                                                    SET p.id_estado_propiedad = (
                                                        SELECT id_estado_propiedad
                                                        FROM estado_propiedad
                                                        WHERE estado = :estado
                                                    )
                                                    WHERE a.id_alquiler = :id_alquiler');
                $estado->execute([
                    ":estado" => $estado_nuevo,
                    ":id_alquiler" => $id_alquiler
                ]);
                $eliminarAlquiler = $this->conexion->prepare('DELETE FROM alquiler
                                                            WHERE id_alquiler = :id_alquiler');
                $eliminarAlquiler->execute([
                    ":id_alquiler" => $id_alquiler
                ]);
                $eliminarInquilino = $this->conexion->prepare('DELETE FROM inquilino
                                                            WHERE id_inquilino = :id_inquilino');
                $eliminarInquilino->execute([
                    ":id_inquilino" => $id_inquilino
                ]);
                $this->conexion->commit();
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

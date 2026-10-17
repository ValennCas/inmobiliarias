<?php
    class Propiedades{
        private PDO $conexion;

        public function __construct(PDO $conexion){
            $this->conexion = $conexion;
        }

        public function estadoExiste($id_estado_propiedad){
            $consulta = $this->conexion->prepare('SELECT id_estado_propiedad FROM estado_propiedad WHERE id_estado_propiedad = :id');
            $consulta->execute([":id" => $id_estado_propiedad]);
            return $consulta->fetch(PDO::FETCH_ASSOC) !== false;
        }

        public function inmobiliariaExiste($id_inmobiliaria){
            $consulta = $this->conexion->prepare('SELECT id_inmobiliaria FROM inmobiliaria WHERE id_inmobiliaria = :id');
            $consulta->execute([":id" => $id_inmobiliaria]);
            return $consulta->fetch(PDO::FETCH_ASSOC) !== false;
        }

        // Crea direccion, propiedad y la relacion con la inmobiliaria en una sola transaccion
        public function insertarPropiedad($datos){
            $this->conexion->beginTransaction();
            try{
                $direccion = $this->conexion->prepare('INSERT INTO direccion(provincia, ciudad, calle, altura)
                                                    VALUES(:provincia, :ciudad, :calle, :altura)');
                $direccion->execute([
                    ":provincia" => $datos["provincia"],
                    ":ciudad" => $datos["ciudad"],
                    ":calle" => $datos["calle"],
                    ":altura" => $datos["altura"]
                ]);
                $id_direccion = (int) $this->conexion->lastInsertId();

                $propiedad = $this->conexion->prepare('INSERT INTO propiedad(id_direccion, id_estado_propiedad, titulo, descripcion, precio)
                                                    VALUES(:id_direccion, :id_estado_propiedad, :titulo, :descripcion, :precio)');
                $propiedad->execute([
                    ":id_direccion" => $id_direccion,
                    ":id_estado_propiedad" => $datos["id_estado_propiedad"],
                    ":titulo" => $datos["titulo"],
                    ":descripcion" => $datos["descripcion"],
                    ":precio" => $datos["precio"]
                ]);
                $id_propiedad = (int) $this->conexion->lastInsertId();

                $relacion = $this->conexion->prepare('INSERT INTO inmobiliaria_propiedad(id_inmobiliaria, id_propiedad)
                                                    VALUES(:id_inmobiliaria, :id_propiedad)');
                $relacion->execute([
                    ":id_inmobiliaria" => $datos["id_inmobiliaria"],
                    ":id_propiedad" => $id_propiedad
                ]);

                $this->conexion->commit();
                return $id_propiedad;
            }
            catch(PDOException $error){
                $this->conexion->rollBack();
                throw $error;
            }
        }

        public function listarPropiedades($id_inmobiliaria = null){
            if($id_inmobiliaria !== null){
                $consulta = $this->conexion->prepare('SELECT p.id_propiedad, p.descripcion, p.titulo, p.precio,
                           p.id_estado_propiedad, e.estado,
                           d.id_direccion, d.provincia, d.ciudad, d.calle, d.altura,
                           ip.id_inmobiliaria
                    FROM propiedad p
                    INNER JOIN direccion d ON d.id_direccion = p.id_direccion
                    INNER JOIN estado_propiedad e ON e.id_estado_propiedad = p.id_estado_propiedad
                    INNER JOIN inmobiliaria_propiedad ip ON ip.id_propiedad = p.id_propiedad
                    WHERE ip.id_inmobiliaria = :id_inmobiliaria');
                $consulta->execute([":id_inmobiliaria" => $id_inmobiliaria]);
            }
            else{
                $consulta = $this->conexion->query('SELECT p.id_propiedad, p.descripcion, p.titulo, p.precio,
                           p.id_estado_propiedad, e.estado,
                           d.id_direccion, d.provincia, d.ciudad, d.calle, d.altura,
                           ip.id_inmobiliaria
                    FROM propiedad p
                    INNER JOIN direccion d ON d.id_direccion = p.id_direccion
                    INNER JOIN estado_propiedad e ON e.id_estado_propiedad = p.id_estado_propiedad
                    INNER JOIN inmobiliaria_propiedad ip ON ip.id_propiedad = p.id_propiedad');
            }
            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        }

        public function listarEstados(){
            $consulta = $this->conexion->query('SELECT id_estado_propiedad, estado FROM estado_propiedad ORDER BY id_estado_propiedad');
            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        }

        // Todos los filtros son opcionales (null = no filtra). ciudad y provincia buscan por coincidencia parcial.
        public function filtrarPropiedades($filtros){
            $consulta = $this->conexion->prepare('SELECT p.id_propiedad, p.descripcion, p.precio,
                           p.id_estado_propiedad, e.estado,
                           d.id_direccion, d.provincia, d.ciudad, d.calle, d.altura,
                           ip.id_inmobiliaria
                    FROM propiedad p
                    INNER JOIN direccion d ON d.id_direccion = p.id_direccion
                    INNER JOIN estado_propiedad e ON e.id_estado_propiedad = p.id_estado_propiedad
                    INNER JOIN inmobiliaria_propiedad ip ON ip.id_propiedad = p.id_propiedad
                    WHERE (:ciudad IS NULL OR d.ciudad LIKE :ciudad2)
                    AND (:provincia IS NULL OR d.provincia LIKE :provincia2)
                    AND (:precio_min IS NULL OR p.precio >= :precio_min2)
                    AND (:precio_max IS NULL OR p.precio <= :precio_max2)
                    AND (:id_estado_propiedad IS NULL OR p.id_estado_propiedad = :id_estado_propiedad2)
                    AND (:id_inmobiliaria IS NULL OR ip.id_inmobiliaria = :id_inmobiliaria2)
                    ORDER BY p.id_propiedad DESC');
            $consulta->execute([
                ":ciudad" => $filtros["ciudad"],
                ":ciudad2" => $filtros["ciudad"] === null ? null : "%" . $filtros["ciudad"] . "%",
                ":provincia" => $filtros["provincia"],
                ":provincia2" => $filtros["provincia"] === null ? null : "%" . $filtros["provincia"] . "%",
                ":precio_min" => $filtros["precio_min"],
                ":precio_min2" => $filtros["precio_min"],
                ":precio_max" => $filtros["precio_max"],
                ":precio_max2" => $filtros["precio_max"],
                ":id_estado_propiedad" => $filtros["id_estado_propiedad"],
                ":id_estado_propiedad2" => $filtros["id_estado_propiedad"],
                ":id_inmobiliaria" => $filtros["id_inmobiliaria"],
                ":id_inmobiliaria2" => $filtros["id_inmobiliaria"]
            ]);
            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        }

        public function buscarPropiedad($id_propiedad){
            $consulta = $this->conexion->prepare('SELECT p.id_propiedad, p.descripcion, p.titulo, p.precio,
                           p.id_estado_propiedad, e.estado,
                           d.id_direccion, d.provincia, d.ciudad, d.calle, d.altura,
                           ip.id_inmobiliaria
                    FROM propiedad p
                    INNER JOIN direccion d ON d.id_direccion = p.id_direccion
                    INNER JOIN estado_propiedad e ON e.id_estado_propiedad = p.id_estado_propiedad
                    INNER JOIN inmobiliaria_propiedad ip ON ip.id_propiedad = p.id_propiedad
                    WHERE p.id_propiedad = :id_propiedad');
            $consulta->execute([":id_propiedad" => $id_propiedad]);
            return $consulta->fetch(PDO::FETCH_ASSOC);
        }

        public function modificarPropiedad($id_propiedad, $datos){
            $this->conexion->beginTransaction();
            try{
                $direccion = $this->conexion->prepare('UPDATE direccion d
                                                    INNER JOIN propiedad p ON p.id_direccion = d.id_direccion
                                                    SET d.provincia = :provincia, d.ciudad = :ciudad, d.calle = :calle, d.altura = :altura
                                                    WHERE p.id_propiedad = :id_propiedad');
                $direccion->execute([
                    ":provincia" => $datos["provincia"],
                    ":ciudad" => $datos["ciudad"],
                    ":calle" => $datos["calle"],
                    ":altura" => $datos["altura"],
                    ":id_propiedad" => $id_propiedad
                ]);

                $propiedad = $this->conexion->prepare('UPDATE propiedad
                                                    SET id_estado_propiedad = :id_estado_propiedad, titulo = :titulo, descripcion = :descripcion, precio = :precio
                                                    WHERE id_propiedad = :id_propiedad');
                $propiedad->execute([
                    ":id_estado_propiedad" => $datos["id_estado_propiedad"],
                    ":titulo" => $datos["titulo"],
                    ":descripcion" => $datos["descripcion"],
                    ":precio" => $datos["precio"],
                    ":id_propiedad" => $id_propiedad
                ]);

                $this->conexion->commit();
            }
            catch(PDOException $error){
                $this->conexion->rollBack();
                throw $error;
            }
        }

        // Si la propiedad tiene alquileres, la FK lanza PDOException (codigo 23000) y se hace rollback
        public function eliminarPropiedad($id_propiedad){
            $this->conexion->beginTransaction();
            try{
                $buscar = $this->conexion->prepare('SELECT id_direccion FROM propiedad WHERE id_propiedad = :id_propiedad');
                $buscar->execute([":id_propiedad" => $id_propiedad]);
                $fila = $buscar->fetch(PDO::FETCH_ASSOC);

                $relacion = $this->conexion->prepare('DELETE FROM inmobiliaria_propiedad WHERE id_propiedad = :id_propiedad');
                $relacion->execute([":id_propiedad" => $id_propiedad]);

                $propiedad = $this->conexion->prepare('DELETE FROM propiedad WHERE id_propiedad = :id_propiedad');
                $propiedad->execute([":id_propiedad" => $id_propiedad]);

                $direccion = $this->conexion->prepare('DELETE FROM direccion WHERE id_direccion = :id_direccion');
                $direccion->execute([":id_direccion" => $fila["id_direccion"]]);

                $this->conexion->commit();
            }
            catch(PDOException $error){
                $this->conexion->rollBack();
                throw $error;
            }
        }
        
        /*INSERTAR FOTO DE UNA PROPIEDAD*/
        public function insertarFoto($id_propiedad, $ruta_foto, $orden){
            $consulta = $this->conexion->prepare('INSERT INTO propiedad_foto(id_propiedad, ruta_foto, orden)
                                                VALUES(:id_propiedad, :ruta_foto, :orden)');
            $consulta->execute([
                ":id_propiedad" => $id_propiedad,
                ":ruta_foto" => $ruta_foto,
                ":orden" => $orden
            ]);
        }

        /*OBTENER FOTOS DE UNA PROPIEDAD*/
        public function obtenerFotos($id_propiedad){
            $consulta = $this->conexion->prepare('SELECT id_foto, ruta_foto, orden
                                                FROM propiedad_foto
                                                WHERE id_propiedad = :id_propiedad
                                                ORDER BY orden');
            $consulta->execute([":id_propiedad" => $id_propiedad]);
            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        }

        /*CONTAR FOTOS DE UNA PROPIEDAD*/
        public function contarFotos($id_propiedad){
            $consulta = $this->conexion->prepare('SELECT COUNT(*) AS cantidad
                                                FROM propiedad_foto
                                                WHERE id_propiedad = :id_propiedad');
            $consulta->execute([":id_propiedad" => $id_propiedad]);
            $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
            return (int) $resultado["cantidad"];
        }

        /*OBTENER UNA FOTO*/
        public function buscarFoto($id_foto){
            $consulta = $this->conexion->prepare('SELECT id_foto, id_propiedad, ruta_foto, orden
                                                FROM propiedad_foto
                                                WHERE id_foto = :id_foto');
            $consulta->execute([":id_foto" => $id_foto]);
            return $consulta->fetch(PDO::FETCH_ASSOC);
        }

        /*ELIMINAR FOTO*/
        public function eliminarFoto($id_foto){
            $consulta = $this->conexion->prepare('DELETE FROM propiedad_foto WHERE id_foto = :id_foto');
            $consulta->execute([":id_foto" => $id_foto]);
        }
    }
?>

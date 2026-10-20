<?php
    class Propiedades{
        private PDO $conexion;

        // Guarda la conexion a la base de datos para usarla en las consultas
        public function __construct(PDO $conexion){
            $this->conexion = $conexion;
        }

        // Comprueba si existe un estado de propiedad con el id indicado
        public function estadoExiste($id_estado_propiedad){
            $consulta = $this->conexion->prepare('SELECT id_estado_propiedad FROM estado_propiedad WHERE id_estado_propiedad = :id');
            $consulta->execute([":id" => $id_estado_propiedad]);
            return $consulta->fetch(PDO::FETCH_ASSOC) !== false;
        }

        //Buscamos el id del estado por su nombre (en venta, en alquiler, vendido, alquilado)
        // Busca el id del estado por su nombre y devuelve false si no lo encuentra
        public function obtenerIdEstado($estado){
            $consulta = $this->conexion->prepare('SELECT id_estado_propiedad FROM estado_propiedad WHERE estado = :estado');
            $consulta->execute([":estado" => $estado]);
            $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

            if($resultado === false){
                return false;
            }
            return $resultado["id_estado_propiedad"];
        }

        // Comprueba si existe la inmobiliaria con el id indicado
        public function inmobiliariaExiste($id_inmobiliaria){
            $consulta = $this->conexion->prepare('SELECT id_inmobiliaria FROM inmobiliaria WHERE id_inmobiliaria = :id');
            $consulta->execute([":id" => $id_inmobiliaria]);
            return $consulta->fetch(PDO::FETCH_ASSOC) !== false;
        }

        // Crea direccion, propiedad y la relacion con la inmobiliaria en una sola transaccion
        // Crea la direccion y la propiedad, la vincula con la inmobiliaria y devuelve el id generado
        public function insertarPropiedad($datos){
            // Inicia una transaccion para guardar juntos los cambios de esta operacion
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
                // Recupera el id de la direccion creada para asociarla con la propiedad
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
                // Recupera el id de la propiedad creada para vincularla con la inmobiliaria
                $id_propiedad = (int) $this->conexion->lastInsertId();

                // Prepara el vinculo entre la propiedad creada y su inmobiliaria
                $relacion = $this->conexion->prepare('INSERT INTO inmobiliaria_propiedad(id_inmobiliaria, id_propiedad)
                                                    VALUES(:id_inmobiliaria, :id_propiedad)');
                $relacion->execute([
                    ":id_inmobiliaria" => $datos["id_inmobiliaria"],
                    ":id_propiedad" => $id_propiedad
                ]);

                // Confirma los cambios de la transaccion
                $this->conexion->commit();
                return $id_propiedad;
            }
            catch(PDOException $error){
                // Deshace los cambios de la transaccion antes de devolver el error
                $this->conexion->rollBack();
                throw $error;
            }
        }

        // Lista las propiedades con su direccion y estado; si llega una inmobiliaria, filtra por ella
        public function listarPropiedades($id_inmobiliaria = null){
            // Si se indico una inmobiliaria, consulta solamente las propiedades vinculadas a ella
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

        // Devuelve los estados de las propiedades ordenados por su id
        public function listarEstados(){
            $consulta = $this->conexion->query('SELECT id_estado_propiedad, estado FROM estado_propiedad ORDER BY id_estado_propiedad');
            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        }

        // Todos los filtros son opcionales (null = no filtra). ciudad y provincia buscan por coincidencia parcial.
        // Busca propiedades con los filtros recibidos y muestra primero las de mayor id
        public function filtrarPropiedades($filtros){
            $consulta = $this->conexion->prepare('SELECT p.id_propiedad, p.titulo, p.descripcion, p.precio,
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
                    AND (:estado IS NULL OR e.estado = :estado2)
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
                ":estado" => $filtros["estado"],
                ":estado2" => $filtros["estado"],
                ":id_inmobiliaria" => $filtros["id_inmobiliaria"],
                ":id_inmobiliaria2" => $filtros["id_inmobiliaria"]
            ]);
            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        }

        // Busca la propiedad con su direccion, estado e id de inmobiliaria
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

        // Actualiza la direccion y los datos de la propiedad dentro de la misma transaccion
        public function modificarPropiedad($id_propiedad, $datos){
            // Inicia una transaccion para guardar juntos los cambios de esta operacion
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

                // Confirma los cambios de la transaccion
                $this->conexion->commit();
            }
            catch(PDOException $error){
                // Deshace los cambios de la transaccion antes de devolver el error
                $this->conexion->rollBack();
                throw $error;
            }
        }

        // Si la propiedad tiene alquileres, la FK lanza PDOException (codigo 23000) y se hace rollback
        // Borra el vinculo con la inmobiliaria, la propiedad y su direccion dentro de la misma transaccion
        public function eliminarPropiedad($id_propiedad){
            // Inicia una transaccion para guardar juntos los cambios de esta operacion
            $this->conexion->beginTransaction();
            try{
                $buscar = $this->conexion->prepare('SELECT id_direccion FROM propiedad WHERE id_propiedad = :id_propiedad');
                $buscar->execute([":id_propiedad" => $id_propiedad]);
                $fila = $buscar->fetch(PDO::FETCH_ASSOC);

                // Borra el vinculo con la inmobiliaria antes de eliminar la propiedad
                $relacion = $this->conexion->prepare('DELETE FROM inmobiliaria_propiedad WHERE id_propiedad = :id_propiedad');
                $relacion->execute([":id_propiedad" => $id_propiedad]);

                $propiedad = $this->conexion->prepare('DELETE FROM propiedad WHERE id_propiedad = :id_propiedad');
                $propiedad->execute([":id_propiedad" => $id_propiedad]);

                // Borra la direccion que pertenecia a la propiedad eliminada
                $direccion = $this->conexion->prepare('DELETE FROM direccion WHERE id_direccion = :id_direccion');
                $direccion->execute([":id_direccion" => $fila["id_direccion"]]);

                // Confirma los cambios de la transaccion
                $this->conexion->commit();
            }
            catch(PDOException $error){
                // Deshace los cambios de la transaccion antes de devolver el error
                $this->conexion->rollBack();
                throw $error;
            }
        }
        
        /*INSERTAR FOTO DE UNA PROPIEDAD*/
        // Guarda la ruta y el orden de una foto asociada a la propiedad
        public function insertarFoto($id_propiedad, $ruta_foto, $orden){
            $consulta = $this->conexion->prepare('INSERT INTO propiedad_foto(id_propiedad, ruta_foto, orden)
                                                VALUES(:id_propiedad, :ruta_foto, :orden)');
            $consulta->execute([
                ":id_propiedad" => $id_propiedad,
                ":ruta_foto" => $ruta_foto,
                ":orden" => $orden
            ]);
        }

        /*INSERTAR UNA FOTO GUARDANDO LA IMAGEN EN LA BASE*/
        // Guarda la imagen en la base de datos y completa su ruta con el id de la foto generada
        public function insertarFotoEnBase($id_propiedad, $contenido, $tipo, $orden){
            // Inicia una transaccion para guardar juntos los cambios de esta operacion
            $this->conexion->beginTransaction();
            try{
                // ruta_foto no admite NULL: se inserta vacia y se completa con el id que le toco a la foto
                $consulta = $this->conexion->prepare('INSERT INTO propiedad_foto(id_propiedad, ruta_foto, orden, contenido, tipo)
                                                    VALUES(:id_propiedad, :ruta_foto, :orden, :contenido, :tipo)');
                $consulta->bindValue(":id_propiedad", $id_propiedad);
                $consulta->bindValue(":ruta_foto", "");
                $consulta->bindValue(":orden", $orden);
                // Enlaza el contenido de la imagen como dato binario
                $consulta->bindValue(":contenido", $contenido, PDO::PARAM_LOB);
                $consulta->bindValue(":tipo", $tipo);
                $consulta->execute();

                $id_foto = (int) $this->conexion->lastInsertId();
                // Arma la ruta que permite pedir esta imagen al controlador de fotos
                $ruta = "controller/fotoController.php?id_foto=" . $id_foto;

                $actualizar = $this->conexion->prepare('UPDATE propiedad_foto SET ruta_foto = :ruta_foto WHERE id_foto = :id_foto');
                $actualizar->execute([":ruta_foto" => $ruta, ":id_foto" => $id_foto]);

                // Confirma los cambios de la transaccion
                $this->conexion->commit();
                return $ruta;
            }
            catch(PDOException $error){
                // Deshace los cambios de la transaccion antes de devolver el error
                $this->conexion->rollBack();
                throw $error;
            }
        }

        /*OBTENER EL CONTENIDO DE UNA FOTO GUARDADA EN LA BASE*/
        // Busca el tipo y el contenido binario de la foto indicada
        public function buscarContenidoFoto($id_foto){
            $consulta = $this->conexion->prepare('SELECT tipo, contenido FROM propiedad_foto WHERE id_foto = :id_foto');
            $consulta->execute([":id_foto" => $id_foto]);
            return $consulta->fetch(PDO::FETCH_ASSOC);
        }

        /*OBTENER FOTOS DE UNA PROPIEDAD*/
        // Devuelve los ids, rutas y orden de las fotos de la propiedad, ordenadas para mostrarlas
        public function obtenerFotos($id_propiedad){
            $consulta = $this->conexion->prepare('SELECT id_foto, ruta_foto, orden
                                                FROM propiedad_foto
                                                WHERE id_propiedad = :id_propiedad
                                                ORDER BY orden');
            $consulta->execute([":id_propiedad" => $id_propiedad]);
            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        }

        /*CONTAR FOTOS DE UNA PROPIEDAD*/
        // Cuenta las fotos de la propiedad y devuelve la cantidad como numero entero
        public function contarFotos($id_propiedad){
            $consulta = $this->conexion->prepare('SELECT COUNT(*) AS cantidad
                                                FROM propiedad_foto
                                                WHERE id_propiedad = :id_propiedad');
            $consulta->execute([":id_propiedad" => $id_propiedad]);
            $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
            return (int) $resultado["cantidad"];
        }

        /*OBTENER UNA FOTO*/
        // Busca los datos de una foto y el id de la propiedad a la que pertenece
        public function buscarFoto($id_foto){
            $consulta = $this->conexion->prepare('SELECT id_foto, id_propiedad, ruta_foto, orden
                                                FROM propiedad_foto
                                                WHERE id_foto = :id_foto');
            $consulta->execute([":id_foto" => $id_foto]);
            return $consulta->fetch(PDO::FETCH_ASSOC);
        }

        /*ELIMINAR FOTO*/
        // Borra el registro de la foto indicada
        public function eliminarFoto($id_foto){
            $consulta = $this->conexion->prepare('DELETE FROM propiedad_foto WHERE id_foto = :id_foto');
            $consulta->execute([":id_foto" => $id_foto]);
        }
    }
?>

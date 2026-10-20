<?php
    class Inmobiliaria{
        private PDO $conexion;

        // Guarda la conexion a la base de datos para usarla en las consultas
        public function __construct(PDO $conexion){
            $this->conexion = $conexion;
        }

        // Crea la direccion y la inmobiliaria, la asocia al usuario y devuelve sus datos
        public function crearInmobiliaria($id_usuario, $nombre, $email, $area_telefono, $telefono, $provincia, $ciudad, $calle, $altura){
            try{
                // Inicia una transaccion para guardar juntos los cambios de esta operacion
                $this->conexion->beginTransaction();

                $insertarDireccion = $this->conexion->prepare('INSERT INTO direccion(provincia, ciudad, calle, altura) VALUES(:provincia, :ciudad, :calle, :altura)');
                $insertarDireccion->execute([":provincia" => $provincia, ":ciudad" => $ciudad, ":calle" => $calle, ":altura" => $altura]);
                // Recupera el id de la direccion creada para asociarla con la inmobiliaria
                $id_direccion = $this->conexion->lastInsertId();

                $insertarInmobiliaria = $this->conexion->prepare('INSERT INTO inmobiliaria(id_direccion, nombre, email, area_telefono, telefono) VALUES(:id_direccion, :nombre, :email, :area_telefono, :telefono)');
                $insertarInmobiliaria->execute([":id_direccion" => $id_direccion, ":nombre" => $nombre, ":email" => $email, ":area_telefono" => $area_telefono, ":telefono" => $telefono]);
                $id_inmobiliaria = $this->conexion->lastInsertId();

                // Asocia al usuario con la inmobiliaria que se acaba de crear
                $asociarUsuario = $this->conexion->prepare('UPDATE usuario SET id_inmobiliaria = :id_inmobiliaria WHERE id_usuario = :id_usuario');
                $asociarUsuario->execute([":id_inmobiliaria" => $id_inmobiliaria, ":id_usuario" => $id_usuario]);
                // Confirma los cambios de la transaccion
                $this->conexion->commit();
                
                $obtener_inmobiliaria = $this->consultarInmobiliaria($id_inmobiliaria);
                return $obtener_inmobiliaria;      
            }
            catch (PDOException $error){
                // Deshace los cambios de la transaccion antes de devolver el error
                $this->conexion->rollBack();
                throw $error;
            } 
        }

        // Actualiza los datos de la inmobiliaria y su direccion dentro de la misma transaccion
        public function actualizarInmobiliaria($id_inmobiliaria, $nombre, $email, $area_telefono, $telefono, $provincia, $ciudad, $calle, $altura){
            try{
                // Inicia una transaccion para guardar juntos los cambios de esta operacion
                $this->conexion->beginTransaction();
                $actualizarInmobiliaria = $this->conexion->prepare('UPDATE inmobiliaria SET nombre = :nombre, email = :email, area_telefono = :area_telefono, telefono = :telefono WHERE id_inmobiliaria = :id_inmobiliaria');
                $actualizarInmobiliaria->execute([":nombre" => $nombre, ":email" => $email, ":area_telefono" => $area_telefono, ":telefono" => $telefono, ":id_inmobiliaria" => $id_inmobiliaria]);
                
                $actualizarDireccion = $this->conexion->prepare('UPDATE direccion as d JOIN inmobiliaria AS i ON i.id_direccion = d.id_direccion
                    SET d.provincia = :provincia, d.ciudad = :ciudad, d.calle = :calle, d.altura = :altura WHERE i.id_inmobiliaria = :id_inmobiliaria');
                $actualizarDireccion->execute([":provincia" => $provincia, ":ciudad" => $ciudad, ":calle" => $calle, ":altura" => $altura, ":id_inmobiliaria" => $id_inmobiliaria]);
                
                // Confirma los cambios de la transaccion
                $this->conexion->commit();

                $obtener_inmobiliaria = $this->consultarInmobiliaria($id_inmobiliaria);
                return $obtener_inmobiliaria;                
            }
            catch (PDOException $error){
                // Deshace los cambios de la transaccion antes de devolver el error
                $this->conexion->rollBack();
                throw $error;
            }
        }

        // Busca los datos de la inmobiliaria junto con su direccion
        public function consultarInmobiliaria($id_inmobiliaria){
            $consulta = $this->conexion->prepare('SELECT i.id_inmobiliaria, i.nombre, i.email, i.area_telefono, i.telefono, d.id_direccion, d.provincia, d.ciudad, d.calle, d.altura
                FROM inmobiliaria AS i JOIN direccion AS d ON i.id_direccion = d.id_direccion WHERE i.id_inmobiliaria = :id_inmobiliaria');
            $consulta->execute([':id_inmobiliaria' => $id_inmobiliaria]);
            
            return $consulta->fetch(PDO::FETCH_ASSOC);
        }

        // Devuelve los ids y nombres de las inmobiliarias ordenados por nombre
        public function listarInmobiliaria(){
            $consulta = $this->conexion->prepare('SELECT id_inmobiliaria, nombre FROM inmobiliaria ORDER BY nombre');
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        }

        // Busca el id de la inmobiliaria asociada al usuario; si no encuentra el usuario, devuelve null
        public function obtenerInmobiliariaUsuario($id_usuario){
            $consulta = $this->conexion->prepare('SELECT id_inmobiliaria FROM usuario WHERE id_usuario = :id_usuario');
            $consulta->execute([":id_usuario" => $id_usuario]);
            $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

            if($resultado === false){
                return null;
            }
        
            return $resultado["id_inmobiliaria"];
        }
    }
?>
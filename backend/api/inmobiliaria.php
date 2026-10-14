<?php
    class Inmobiliaria{
        private PDO $conexion;

        public function __construct(PDO $conexion){
            $this->conexion = $conexion;
        }

        public function crearInmobiliaria($id_usuario, $nombre, $email, $area_telefono, $telefono, $provincia, $ciudad, $calle, $altura){
            try{
                $this->conexion->beginTransaction();

                $insertarDireccion = $this->conexion->prepare('INSERT INTO direccion(provincia, ciudad, calle, altura) VALUES(:provincia, :ciudad, :calle, :altura)');
                $insertarDireccion->execute([":provincia" => $provincia, ":ciudad" => $ciudad, ":calle" => $calle, ":altura" => $altura]);
                $id_direccion = $this->conexion->lastInsertId();

                $insertarInmobiliaria = $this->conexion->prepare('INSERT INTO inmobiliaria(id_direccion, nombre, email, area_telefono, telefono) VALUES(:id_direccion, :nombre, :email, :area_telefono, :telefono)');
                $insertarInmobiliaria->execute([":id_direccion" => $id_direccion, ":nombre" => $nombre, ":email" => $email, ":area_telefono" => $area_telefono, ":telefono" => $telefono]);
                $id_inmobiliaria = $this->conexion->lastInsertId();

                $asociarUsuario = $this->conexion->prepare('UPDATE usuario SET id_inmobiliaria = :id_inmobiliaria WHERE id_usuario = :id_usuario');
                $asociarUsuario->execute([":id_inmobiliaria" => $id_inmobiliaria, ":id_usuario" => $id_usuario]);
                $this->conexion->commit();
                
                $obtener_inmobiliaria = $this->consultarInmobiliaria($id_inmobiliaria);
                return $obtener_inmobiliaria;      
            }
            catch (PDOException $error){
                $this->conexion->rollBack();
                throw $error;
            } 
        }

        public function actualizarInmobiliaria($id_inmobiliaria, $nombre, $email, $area_telefono, $telefono, $provincia, $ciudad, $calle, $altura){
            try{
                $this->conexion->beginTransaction();
                $actualizarInmobiliaria = $this->conexion->prepare('UPDATE inmobiliaria SET nombre = :nombre, email = :email, area_telefono = :area_telefono, telefono = :telefono WHERE id_inmobiliaria = :id_inmobiliaria');
                $actualizarInmobiliaria->execute([":nombre" => $nombre, ":email" => $email, ":area_telefono" => $area_telefono, ":telefono" => $telefono, ":id_inmobiliaria" => $id_inmobiliaria]);
                
                $actualizarDireccion = $this->conexion->prepare('UPDATE direccion as d JOIN inmobiliaria AS i ON i.id_direccion = d.id_direccion
                    SET d.provincia = :provincia, d.ciudad = :ciudad, d.calle = :calle, d.altura = :altura WHERE i.id_inmobiliaria = :id_inmobiliaria');
                $actualizarDireccion->execute([":provincia" => $provincia, ":ciudad" => $ciudad, ":calle" => $calle, ":altura" => $altura, ":id_inmobiliaria" => $id_inmobiliaria]);
                
                $this->conexion->commit();

                $obtener_inmobiliaria = $this->consultarInmobiliaria($id_inmobiliaria);
                return $obtener_inmobiliaria;                
            }
            catch (PDOException $error){
                $this->conexion->rollBack();
                throw $error;
            }
        }

        public function consultarInmobiliaria($id_inmobiliaria){
            $consulta = $this->conexion->prepare('SELECT i.id_inmobiliaria, i.nombre, i.email, i.area_telefono, i.telefono, d.id_direccion, d.provincia, d.ciudad, d.calle, d.altura
                FROM inmobiliaria AS i JOIN direccion AS d ON i.id_direccion = d.id_direccion WHERE i.id_inmobiliaria = :id_inmobiliaria');
            $consulta->execute([':id_inmobiliaria' => $id_inmobiliaria]);
            
            return $consulta->fetch(PDO::FETCH_ASSOC);
        }

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
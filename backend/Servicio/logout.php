<?php
    require_once __DIR__ . '/../api/seguridad.php';

    class Logout{
        private Seguridad $seguridad;

        public function __construct(PDO $conexion){
            $this->seguridad = new Seguridad($conexion);
        }

        public function cerrarSesion($id_usuario){
            try{
                $this->seguridad->eliminarToken($id_usuario);
                return [
                    "mensaje" => "Sesion cerrada correctamente"
                ];
            }
            catch(PDOException $error){
                throw new Exception("No se pudo cerrar la sesion", 500);
            }
        }
    }
?>
<?php
    // Carga los archivos que necesita este servicio
    require_once __DIR__ . '/../api/seguridad.php';

    class Logout{
        private Seguridad $seguridad;

        // Prepara el acceso a los datos que necesita este servicio
        public function __construct(PDO $conexion){
            $this->seguridad = new Seguridad($conexion);
        }

        // Elimina el token del usuario para cerrar su sesion
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
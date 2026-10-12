<?php
    require_once '../api/seguridad.php';
    require_once 'seguridad.php';

    class Logout{
        private Seguridad $seguridad;
        private SeguridadServicio $seguridadServicio;

        public function __construct(PDO $conexion){
            $this->seguridad = new Seguridad($conexion);
            $this->seguridadServicio = new SeguridadServicio($this->seguridad);
        }

         public function cerrarSesion(){

         $id_usuario = $this->seguridadServicio->verificarToken();
            try{
                $this->seguridad->eliminarToken($id_usuario);

                http_response_code(200);
                echo json_encode(["mensaje" => "Sesion cerrado correctamente"]);
                exit();
            }
            catch(PDOException $error){
                http_response_code(500);
                echo json_encode(["error" => "No se pudo cerrar la sesion"]);
                exit();
            }
        }
    }
?>
<?php
    require_once __DIR__ . '/../api/usuario.php';

    class ServicioUsuario{
        private Usuario $usuario;

        public function __construct(Usuario $usuario){
            $this->usuario = $usuario;
        }

        public function actualizarUsuario($id_usuario, $obtener_datos){
            if($id_usuario === null || $id_usuario <= 0){
                throw new Exception("El id del usuario es invalido", 400);
            }

            $this->validarDatos($obtener_datos);

            $usuarioEncontrado = $this->usuario->obtenerUsuario($id_usuario);

            if($usuarioEncontrado === false){
                throw new Exception("No se encontro una cuenta asociada", 404);
            }

            try{
                $this->usuario->actualizarUsuario(
                    $id_usuario,
                    $obtener_datos["nombre_usuario"],
                    $obtener_datos["apellido_usuario"],
                    $obtener_datos["email"]
                );

                $usuarioActualizado = $this->usuario->obtenerUsuario($id_usuario);

                return [
                    "mensaje" => "Datos actualizados con exito",
                    "usuario" => $usuarioActualizado
                ];
            }
            catch(PDOException $error){
                throw new Exception("No se pudo actualizar los datos, intente nuevamente!", 500);
            }
        }

        public function buscarUsuario($id_usuario){
            if($id_usuario === null || $id_usuario <= 0){
                throw new Exception("El id del usuario es invalido", 400);
            }

            $usuario = $this->usuario->obtenerUsuario($id_usuario);

            if($usuario === false){
                throw new Exception("No se encontro una cuenta asociada", 404);
            }

            return $usuario;
        }

        private function validarDatos($datos){
            foreach(["nombre_usuario", "apellido_usuario", "email"] as $campo){
                if(!isset($datos[$campo]) || trim((string) $datos[$campo]) === ""){
                    throw new Exception("Falta completar el campo $campo", 400);
                }
            }
        }
    }
?>
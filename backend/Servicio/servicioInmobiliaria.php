<?php
    // Carga los archivos que necesita este servicio
    require_once __DIR__ . '/../api/inmobiliaria.php';

    class ServicioInmobiliaria{
        private Inmobiliaria $inmobiliaria;

        // Prepara el acceso a los datos que necesita este servicio
        public function __construct(Inmobiliaria $inmobiliaria){
            $this->inmobiliaria = $inmobiliaria;
        }

        // Valida los datos y crea la inmobiliaria si el usuario todavia no tiene una asociada
        public function crearInmobiliaria($id_usuario, $obtener_datos){
            if($id_usuario === null || $id_usuario <= 0){
                throw new Exception("El id del usuario es invalido", 400);
            }

            $this->validarDatos($obtener_datos);
            $id_inmobiliaria = $this->inmobiliaria->obtenerInmobiliariaUsuario($id_usuario);

            // Impide agregar otra inmobiliaria cuando el usuario ya tiene una
            if($id_inmobiliaria !== null){
                throw new Exception("No se puede agregar mas de una inmobiliaria", 400);
            }

            try{
                return $this->inmobiliaria->crearInmobiliaria($id_usuario, $obtener_datos["nombre"], $obtener_datos["email"], $obtener_datos["area_telefono"], $obtener_datos["telefono"], $obtener_datos["provincia"], $obtener_datos["ciudad"], $obtener_datos["calle"], $obtener_datos["altura"]);
            }
            catch(PDOException $error){
                throw new Exception("No se pudo agregar la inmobiliaria, intente nuevamente!", 500);
            }
        }

        // Busca la inmobiliaria del usuario y guarda los datos despues de validarlos
        public function actualizarInmobiliaria($id_usuario, $obtener_datos){
            if($id_usuario === null || $id_usuario <= 0){
                throw new Exception("El id del usuario es invalido", 400);
            }
            $this->validarDatos($obtener_datos);
            $id_inmobiliaria = $this->inmobiliaria->obtenerInmobiliariaUsuario($id_usuario);
            if($id_inmobiliaria === null){
                throw new Exception("No se encontro una inmobiliaria asociada", 404);
            }

            try{
                $inmobiliaria = $this->inmobiliaria->actualizarInmobiliaria($id_inmobiliaria, $obtener_datos["nombre"], $obtener_datos["email"], $obtener_datos["area_telefono"], $obtener_datos["telefono"], $obtener_datos["provincia"], $obtener_datos["ciudad"], $obtener_datos["calle"], $obtener_datos["altura"]);

                return [
                    "mensaje" => "Datos actualizados con exito",
                    "inmobiliaria" => $inmobiliaria
                ];
            }
            catch(PDOException $error){
                throw new Exception("No se pudo actualizar los datos, intente nuevamente!", 500);
            }
        }

        // Busca la inmobiliaria del usuario y comprueba que coincida con la solicitada, si se indica una
        public function buscarInmobiliaria($id_usuario, $id_inmobiliaria = null){
            if($id_usuario === null || $id_usuario <= 0){
                throw new Exception("El id del usuario es invalido", 400);
            }

            $id_inmobiliaria_usuario = $this->inmobiliaria->obtenerInmobiliariaUsuario($id_usuario);

            if($id_inmobiliaria_usuario === null){
                throw new Exception("No se encontro una inmobiliaria asociada", 404);
            }
            // Impide consultar una inmobiliaria distinta de la asociada al usuario
            if($id_inmobiliaria !== null && $id_inmobiliaria_usuario != $id_inmobiliaria){
                throw new Exception("No tiene permiso para consultar esta inmobiliaria", 403);
            }
            return $this->inmobiliaria->consultarInmobiliaria($id_inmobiliaria_usuario);
        }

        // Devuelve la lista de inmobiliarias
        public function listarInmobiliaria(){
            return $this->inmobiliaria->listarInmobiliaria();
        }

        // Comprueba que esten completos los datos de contacto y la direccion de la inmobiliaria
        private function validarDatos($datos){
            foreach(["nombre", "email", "area_telefono", "telefono", "provincia", "ciudad", "calle", "altura"] as $campo){
                if(!isset($datos[$campo]) || trim((string) $datos[$campo]) === ""){
                    throw new Exception("Falta completar el campo $campo", 400);
                }
            }
        }
    }
?>
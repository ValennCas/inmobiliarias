<?php
    require_once __DIR__ . '/../api/propiedades.php';

    class ServicioPropiedades{
        private Propiedades $propiedades;

        public function __construct(Propiedades $propiedades){
            $this->propiedades = $propiedades;
        }

        // Valida los datos de alta y de modificacion
        private function validar($datos){
            foreach(["provincia", "ciudad", "calle", "altura", "descripcion"] as $campo){
                if(!isset($datos[$campo]) || trim((string) $datos[$campo]) === ""){
                    throw new Exception("Falta completar el campo $campo", 400);
                }
            }
            if(!filter_var($datos["altura"], FILTER_VALIDATE_INT) || $datos["altura"] <= 0){
                throw new Exception("La altura debe ser un numero entero mayor a 0", 400);
            }
            if(!isset($datos["precio"]) || !is_numeric($datos["precio"]) || $datos["precio"] <= 0){
                throw new Exception("El precio debe ser un numero mayor a 0", 400);
            }
            if(!isset($datos["id_estado_propiedad"]) || !filter_var($datos["id_estado_propiedad"], FILTER_VALIDATE_INT)){
                throw new Exception("El estado de la propiedad es invalido", 400);
            }
            if(!$this->propiedades->estadoExiste($datos["id_estado_propiedad"])){
                throw new Exception("El estado de la propiedad no existe", 400);
            }
        }

        private function validarId($id){
            if($id === null || !filter_var($id, FILTER_VALIDATE_INT)){
                throw new Exception("El id de la propiedad es invalido", 400);
            }
        }

        private function existePropiedad($id){
            $this->validarId($id);
            $propiedad = $this->propiedades->buscarPropiedad($id);
            if($propiedad === false){
                throw new Exception("No existe una propiedad con ese id", 404);
            }
            return $propiedad;
        }

        public function listarPropiedades($id_inmobiliaria){
            if($id_inmobiliaria !== null && !filter_var($id_inmobiliaria, FILTER_VALIDATE_INT)){
                throw new Exception("El id de la inmobiliaria es invalido", 400);
            }
            return $this->propiedades->listarPropiedades($id_inmobiliaria);
        }

        public function obtenerPropiedad($id){
            return $this->existePropiedad($id);
        }

        public function crearPropiedad($datos){
            if(!isset($datos["id_inmobiliaria"]) || !filter_var($datos["id_inmobiliaria"], FILTER_VALIDATE_INT)){
                throw new Exception("Falta la inmobiliaria", 400);
            }
            if(!$this->propiedades->inmobiliariaExiste($datos["id_inmobiliaria"])){
                throw new Exception("La inmobiliaria no existe", 404);
            }
            $this->validar($datos);

            $id_propiedad = $this->propiedades->insertarPropiedad($datos);
            return $this->propiedades->buscarPropiedad($id_propiedad);
        }

        public function modificarPropiedad($id, $datos){
            $this->existePropiedad($id);
            $this->validar($datos);

            $this->propiedades->modificarPropiedad($id, $datos);
            return $this->propiedades->buscarPropiedad($id);
        }

        public function borrarPropiedad($id){
            $this->existePropiedad($id);
            try{
                $this->propiedades->eliminarPropiedad($id);
            }
            catch(PDOException $error){
                if($error->getCode() == "23000"){
                    throw new Exception("La propiedad tiene alquileres asociados y no se puede borrar", 409);
                }
                throw $error;
            }
        }
    }
?>

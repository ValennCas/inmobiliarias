<?php
    require_once __DIR__ . '/../api/inquilinos.php';

    // En el servicio controlamos los datos, aca encapsulamos la lógica del negocio
    class ServicioInquilinos{
        private Inquilinos $inquilinos;

        public function __construct(Inquilinos $inquilinos){
            $this->inquilinos = $inquilinos;
        }

        // Valida los datos de alta y de modificacion
        private function validar($datos, $id_excluido = null){
            $largos = ["nombre" => 100, "apellido" => 100, "email" => 200, "area_telefono" => 10, "telefono" => 20];
            foreach($largos as $campo => $largo){
                if(!isset($datos[$campo]) || !is_string($datos[$campo]) || trim($datos[$campo]) === ""){
                    throw new Exception("Falta completar el campo $campo", 400);
                }
                if(strlen($datos[$campo]) > $largo){
                    throw new Exception("El campo $campo no puede superar los $largo caracteres", 400);
                }
            }

            
            if(!filter_var($datos["email"], FILTER_VALIDATE_EMAIL)){
                throw new Exception("El email es invalido", 400);
            }
            if(!ctype_digit($datos["area_telefono"]) || !ctype_digit($datos["telefono"])){
                throw new Exception("El area y el telefono solo pueden tener numeros", 400);
            }
            if($this->inquilinos->emailExiste($datos["email"], $id_excluido)){
                throw new Exception("Ya existe un inquilino con ese email", 409);
            }
        }

        private function validarId($id){
            if($id === null || !filter_var($id, FILTER_VALIDATE_INT)){
                throw new Exception("El id del inquilino es invalido", 400);
            }
        }

        private function existeInquilino($id){
            $this->validarId($id);
            $inquilino = $this->inquilinos->buscarInquilino($id);
            if($inquilino === false){
                throw new Exception("No existe un inquilino con el id: $id", 404);
            }
            return $inquilino;
        }

        public function listarInquilinos(){
            return $this->inquilinos->listarInquilinos();
        }

        public function obtenerInquilino($id){
            return $this->existeInquilino($id);
        }

        public function crearInquilino($datos){
            $this->validar($datos);
            $id_inquilino = $this->inquilinos->insertarInquilino($datos);
            return $this->inquilinos->buscarInquilino($id_inquilino);
        }

        public function modificarInquilino($id, $datos){
            $this->existeInquilino($id);
            $this->validar($datos, $id);
            $this->inquilinos->modificarInquilino($id, $datos);
            return $this->inquilinos->buscarInquilino($id);
        }

        public function borrarInquilino($id){
            $this->existeInquilino($id);
            try{
                $this->inquilinos->eliminarInquilino($id);
            }
            catch(PDOException $error){
                if($error->getCode() == "23000"){
                    throw new Exception("El inquilino tiene alquileres asociados y no se puede borrar", 409);
                }
                throw $error;
            }
        }
    }
?>

<?php
    // Carga los archivos que necesita este servicio
    require_once __DIR__ . '/../api/inquilinos.php';

    // En el servicio controlamos los datos, aca encapsulamos la lógica del negocio
    class ServicioInquilinos{
        private Inquilinos $inquilinos;

        // Prepara el acceso a los datos que necesita este servicio
        public function __construct(Inquilinos $inquilinos){
            $this->inquilinos = $inquilinos;
        }

        // Valida los datos de alta y de modificacion
        // Revisa los campos obligatorios, sus largos, el email y el telefono, y comprueba que el email no este usado
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
            // El area y el telefono deben estar formados solamente por numeros
            if(!ctype_digit($datos["area_telefono"]) || !ctype_digit($datos["telefono"])){
                throw new Exception("El area y el telefono solo pueden tener numeros", 400);
            }
            // Busca si otro inquilino ya usa ese email; al modificar, excluye el registro actual
            if($this->inquilinos->emailExiste($datos["email"], $id_excluido)){
                throw new Exception("Ya existe un inquilino con ese email", 409);
            }
        }

        // Comprueba que el id recibido no sea nulo y pase la validacion de numero entero
        private function validarId($id){
            if($id === null || !filter_var($id, FILTER_VALIDATE_INT)){
                throw new Exception("El id del inquilino es invalido", 400);
            }
        }

        // Valida el id y busca el inquilino; si no existe, devuelve un error
        private function existeInquilino($id){
            $this->validarId($id);
            $inquilino = $this->inquilinos->buscarInquilino($id);
            if($inquilino === false){
                throw new Exception("No existe un inquilino con el id: $id", 404);
            }
            return $inquilino;
        }

        // Devuelve la lista de inquilinos
        public function listarInquilinos(){
            return $this->inquilinos->listarInquilinos();
        }

        // Busca un inquilino por su id y comprueba que exista
        public function obtenerInquilino($id){
            return $this->existeInquilino($id);
        }

        // Valida los datos, crea el inquilino y devuelve el registro guardado
        public function crearInquilino($datos){
            $this->validar($datos);
            $id_inquilino = $this->inquilinos->insertarInquilino($datos);
            return $this->inquilinos->buscarInquilino($id_inquilino);
        }

        // Valida los cambios sin contar al propio inquilino al comprobar si el email ya esta usado
        public function modificarInquilino($id, $datos){
            $this->existeInquilino($id);
            $this->validar($datos, $id);
            $this->inquilinos->modificarInquilino($id, $datos);
            return $this->inquilinos->buscarInquilino($id);
        }

        // Comprueba que el inquilino exista e intenta borrarlo; si tiene alquileres asociados, informa el conflicto
        public function borrarInquilino($id){
            $this->existeInquilino($id);
            try{
                $this->inquilinos->eliminarInquilino($id);
            }
            catch(PDOException $error){
                // Trata el error de integridad como un conflicto por alquileres asociados
                if($error->getCode() == "23000"){
                    throw new Exception("El inquilino tiene alquileres asociados y no se puede borrar", 409);
                }
                throw $error;
            }
        }
    }
?>

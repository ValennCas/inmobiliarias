<?php
    // Carga los archivos que necesita este servicio
    require_once __DIR__ . '/../api/alquileres.php';

    // Reglas de negocio, ante un problema lanza Exception con el codigo HTTP como codigo de la excepcion.
    class ServicioAlquileres{
        private const ESTADO_DISPONIBLE = "EN ALQUILER";
        private const ESTADO_ALQUILADO = "ALQUILADO";

        private Alquileres $alquileres;

        // Prepara el acceso a los datos que necesita este servicio
        public function __construct(Alquileres $alquileres){
            $this->alquileres = $alquileres;
        }

        // Comprueba que la fecha exista y este escrita con el formato anio-mes-dia
        private function esFechaValida($fecha){
            if(!is_string($fecha)){
                return false;
            }
            $date = DateTime::createFromFormat("Y-m-d", $fecha);
            return $date !== false && $date->format("Y-m-d") === $fecha;
        }

        // Valida precio y fechas
        // Revisa el precio y las fechas; si hay fecha de fin, debe ser posterior a la de inicio
        private function validarPrecioYFechas($datos){
            if(!isset($datos["precio"]) || !is_numeric($datos["precio"]) || $datos["precio"] <= 0){
                throw new Exception("El precio debe ser un numero mayor a 0", 400);
            }
            if(!isset($datos["fecha_inicio"]) || !$this->esFechaValida($datos["fecha_inicio"])){
                throw new Exception("La fecha de inicio es invalida (formato AAAA-MM-DD)", 400);
            }
            if(isset($datos["fecha_fin"])){
                if(!$this->esFechaValida($datos["fecha_fin"])){
                    throw new Exception("La fecha de fin es invalida (formato AAAA-MM-DD)", 400);
                }
                if($datos["fecha_fin"] <= $datos["fecha_inicio"]){
                    throw new Exception("La fecha de fin debe ser posterior a la de inicio", 400);
                }
            }
        }

        // Comprueba que el id recibido no sea nulo y pase la validacion de numero entero
        private function validarId($id, $nombre){
            if($id === null || !filter_var($id, FILTER_VALIDATE_INT)){
                throw new Exception("El $nombre es invalido", 400);
            }
        }

        // Valida el id y busca el alquiler, si no existe devuelve un error
        private function existeAlquiler($id){
            $this->validarId($id, "id del alquiler");
            $alquiler = $this->alquileres->buscarAlquiler($id);
            if($alquiler === false){
                throw new Exception("No existe un alquiler con ese id", 404);
            }
            return $alquiler;
        }

        // Valida los filtros recibidos y lista los alquileres por inquilino o inmobiliaria
        public function listarAlquileres($id_inquilino, $id_inmobiliaria){
            if($id_inquilino !== null){
                $this->validarId($id_inquilino, "id del inquilino");
            }
            // Impide agregar otra inmobiliaria cuando el usuario ya tiene una
            if($id_inmobiliaria !== null){
                $this->validarId($id_inmobiliaria, "id de la inmobiliaria");
            }
            return $this->alquileres->listarAlquileres($id_inquilino, $id_inmobiliaria);
        }

        // Busca un alquiler por su id y comprueba que exista
        public function obtenerAlquiler($id){
            return $this->existeAlquiler($id);
        }

        // Comprueba los datos y la disponibilidad de la propiedad antes de crear el alquiler
        public function crearAlquiler($datos){
            $this->validarId($datos["id_inmobiliaria"] ?? null, "id de la inmobiliaria");
            $this->validarId($datos["id_propiedad"] ?? null, "id de la propiedad");
            $this->validarId($datos["id_inquilino"] ?? null, "id del inquilino");
            $this->validarPrecioYFechas($datos);

            // Comprueba que el inquilino exista antes de asociarlo al alquiler
            if(!$this->alquileres->inquilinoExiste($datos["id_inquilino"])){
                throw new Exception("El inquilino no existe", 404);
            }
            // Busca la relacion entre la propiedad y la inmobiliaria que va a alquilarla
            $vinculo = $this->alquileres->buscarVinculo($datos["id_inmobiliaria"], $datos["id_propiedad"]);
            if($vinculo === false){
                throw new Exception("La propiedad no pertenece a esa inmobiliaria", 404);
            }
            // Solo permite crear el alquiler si la propiedad esta en estado EN ALQUILER
            if($vinculo["estado"] !== self::ESTADO_DISPONIBLE){
                throw new Exception("La propiedad no esta disponible para alquilar (estado: " . $vinculo["estado"] . ")", 409);
            }

            // Crea el alquiler pasando ALQUILADO como nuevo estado de la propiedad
            $id_alquiler = $this->alquileres->insertarAlquiler($datos, $vinculo["id_inmobiliaria_propiedad"], self::ESTADO_ALQUILADO);
            return $this->alquileres->buscarAlquiler($id_alquiler);
        }

        // Comprueba que el alquiler exista, valida el precio y las fechas y guarda los cambios
        public function modificarAlquiler($id, $datos){
            $this->existeAlquiler($id);
            $this->validarPrecioYFechas($datos);

            $this->alquileres->modificarAlquiler($id, $datos);
            return $this->alquileres->buscarAlquiler($id);
        }

        // Comprueba el alquiler y el nuevo estado de la propiedad antes de eliminarlo
        public function borrarAlquiler($id, $estado_nuevo){
            $this->existeAlquiler($id);
            // Al borrar el alquiler, solo admite dejar la propiedad en venta o en alquiler
            if($estado_nuevo !== "EN VENTA" && $estado_nuevo !== "EN ALQUILER" ){
                throw new Exception("El estado seleccionado es invalido", 400);
            }
            $this->alquileres->eliminarAlquiler($id, $estado_nuevo);
        }
    }
?>

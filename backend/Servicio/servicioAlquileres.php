<?php
    require_once __DIR__ . '/../api/alquileres.php';

    // Reglas de negocio. Ante un problema lanza Exception con el codigo HTTP como codigo de la excepcion.
    class ServicioAlquileres{
        private const ESTADO_DISPONIBLE = "EN ALQUILER";
        private const ESTADO_ALQUILADO = "ALQUILADO";

        private Alquileres $alquileres;

        public function __construct(Alquileres $alquileres){
            $this->alquileres = $alquileres;
        }

        private function esFechaValida($fecha){
            if(!is_string($fecha)){
                return false;
            }
            $date = DateTime::createFromFormat("Y-m-d", $fecha);
            return $date !== false && $date->format("Y-m-d") === $fecha;
        }

        // Valida precio y fechas (comun al alta y a la modificacion). fecha_fin es opcional.
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

        private function validarId($id, $nombre){
            if($id === null || !filter_var($id, FILTER_VALIDATE_INT)){
                throw new Exception("El $nombre es invalido", 400);
            }
        }

        private function existeAlquiler($id){
            $this->validarId($id, "id del alquiler");
            $alquiler = $this->alquileres->buscarAlquiler($id);
            if($alquiler === false){
                throw new Exception("No existe un alquiler con ese id", 404);
            }
            return $alquiler;
        }

        public function listarAlquileres($id_inquilino, $id_inmobiliaria){
            if($id_inquilino !== null){
                $this->validarId($id_inquilino, "id del inquilino");
            }
            if($id_inmobiliaria !== null){
                $this->validarId($id_inmobiliaria, "id de la inmobiliaria");
            }
            return $this->alquileres->listarAlquileres($id_inquilino, $id_inmobiliaria);
        }

        public function obtenerAlquiler($id){
            return $this->existeAlquiler($id);
        }

        public function crearAlquiler($datos){
            $this->validarId($datos["id_inmobiliaria"] ?? null, "id de la inmobiliaria");
            $this->validarId($datos["id_propiedad"] ?? null, "id de la propiedad");
            $this->validarId($datos["id_inquilino"] ?? null, "id del inquilino");
            $this->validarPrecioYFechas($datos);

            if(!$this->alquileres->inquilinoExiste($datos["id_inquilino"])){
                throw new Exception("El inquilino no existe", 404);
            }
            $vinculo = $this->alquileres->buscarVinculo($datos["id_inmobiliaria"], $datos["id_propiedad"]);
            if($vinculo === false){
                throw new Exception("La propiedad no pertenece a esa inmobiliaria", 404);
            }
            if($vinculo["estado"] !== self::ESTADO_DISPONIBLE){
                throw new Exception("La propiedad no esta disponible para alquilar (estado: " . $vinculo["estado"] . ")", 409);
            }

            $id_alquiler = $this->alquileres->insertarAlquiler($datos, $vinculo["id_inmobiliaria_propiedad"], self::ESTADO_ALQUILADO);
            return $this->alquileres->buscarAlquiler($id_alquiler);
        }

        public function modificarAlquiler($id, $datos){
            $this->existeAlquiler($id);
            $this->validarPrecioYFechas($datos);

            $this->alquileres->modificarAlquiler($id, $datos);
            return $this->alquileres->buscarAlquiler($id);
        }

        public function borrarAlquiler($id, $estado_nuevo){
            $this->existeAlquiler($id);
            if($estado_nuevo !== "EN VENTA" && $estado_nuevo !== "EN ALQUILER" ){
                throw new Exception("El estado seleccionado es invalido", 400);
            }
            $this->alquileres->eliminarAlquiler($id, $estado_nuevo);
        }
    }
?>

<?php
    require_once __DIR__ . '/../api/propiedades.php';

    class ServicioPropiedades{
        private Propiedades $propiedades;

        public function __construct(Propiedades $propiedades){
            $this->propiedades = $propiedades;
        }

        // Valida los datos de alta y de modificacion
        private function validar($datos){
            foreach(["provincia", "ciudad", "calle", "altura", "descripcion", "titulo"] as $campo){
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
            $propiedades = $this->propiedades->listarPropiedades($id_inmobiliaria);
            foreach($propiedades as &$propiedad){
                $propiedad["fotos"] = $this->propiedades->obtenerFotos($propiedad["id_propiedad"]);
            }
            return $propiedades;
        }

        public function listarEstados(){
            return $this->propiedades->listarEstados();
        }

        public function filtrarPropiedades($parametros){
            $filtros = [];
            foreach(["ciudad", "provincia"] as $campo){
                $valor = trim($parametros[$campo] ?? "");
                $filtros[$campo] = $valor === "" ? null : $valor;
            }

            foreach(["precio_min", "precio_max"] as $campo){
                $valor = $parametros[$campo] ?? "";
                if($valor !== "" && (!is_numeric($valor) || $valor < 0)){
                    throw new Exception("El filtro $campo debe ser un numero mayor o igual a 0", 400);
                }
                $filtros[$campo] = $valor === "" ? null : $valor;
            }
            if($filtros["precio_min"] !== null && $filtros["precio_max"] !== null && $filtros["precio_min"] > $filtros["precio_max"]){
                throw new Exception("El precio minimo no puede ser mayor al maximo", 400);
            }

            foreach(["id_inmobiliaria"] as $campo){
                $valor = $parametros[$campo] ?? "";
                if($valor !== "" && !filter_var($valor, FILTER_VALIDATE_INT)){
                    throw new Exception("El filtro $campo es invalido", 400);
                }
                $filtros[$campo] = $valor === "" ? null : $valor;
            }
            $estado = trim($parametros["estado"] ?? "");
            if($estado !== "" && $estado !== "EN VENTA" && $estado !== "EN ALQUILER"){
                throw new Exception("El estado es invalido", 400);
            }

            $filtros["estado"] = $estado === "" ? null : $estado;

            $propiedades = $this->propiedades->filtrarPropiedades($filtros);
            foreach($propiedades as &$propiedad){
                $propiedad["fotos"] = $this->propiedades->obtenerFotos($propiedad["id_propiedad"]);
            }
            return $propiedades;
        }

        public function obtenerPropiedad($id){
            $propiedades = $this->existePropiedad($id);
            $propiedades["fotos"] = $this->propiedades->obtenerFotos($id);
            return $propiedades;
        }

        public function crearPropiedad($datos, $fotos = null){
            if(!isset($datos["id_inmobiliaria"]) || !filter_var($datos["id_inmobiliaria"], FILTER_VALIDATE_INT)){
                throw new Exception("Falta la inmobiliaria", 400);
            }
            if(!$this->propiedades->inmobiliariaExiste($datos["id_inmobiliaria"])){
                throw new Exception("La inmobiliaria no existe", 404);
            }

            $this->validar($datos);
            $this->validarFotos($fotos);

            $id_propiedad = $this->propiedades->insertarPropiedad($datos);
            $this->guardarFotos($id_propiedad, $fotos);
            $propiedad = $this->propiedades->buscarPropiedad($id_propiedad);
            return $this->propiedades->obtenerFotos($id_propiedad);
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

        /*VALIDAR FOTOS*/
        private function validarFotos($fotos){
            if($fotos === null || !isset($fotos["name"])){
                return;
            }

            $cantidad = count($fotos["name"]);
            if($cantidad > 5){
                throw new Exception("Solo se pueden cargar hasta 5 imagenes", 400);
            }

            $limite = 20 * 1024 * 1024;
            for($i = 0; $i < $cantidad; $i++){
                if($fotos["size"][$i] > $limite){
                    throw new Exception("Cada imagen puede pesar como maximo 20 MB", 400);
                }
            }
        }

        /*GUARDAR FOTOS*/
        private function guardarFotos($id_propiedad, $fotos){
            if($fotos === null || !isset($fotos["name"])){
                return;
            }

            $carpeta = __DIR__ . "/../cargasImagenes/fotosPropiedades/" . $id_propiedad . "/";
            if(!is_dir($carpeta)){
                mkdir($carpeta, 0777, true);
            }

            $cantidad_actual = $this->propiedades->contarFotos($id_propiedad);
            for($i = 0; $i < count($fotos["name"]); $i++){
                if($fotos["error"][$i] !== UPLOAD_ERR_OK){
                    continue;
                }
                $extension = strtolower(pathinfo($fotos["name"][$i], PATHINFO_EXTENSION));
                if(!in_array($extension, ["jpg", "jpeg", "png", "webp"], true)){
                    throw new Exception("Formato de imagen no permitido", 400);
                }
                $nombre = uniqid("foto_") . "." . $extension;
                $ruta_completa = $carpeta . $nombre;
                if(!move_uploaded_file($fotos["tmp_name"][$i], $ruta_completa)){
                    throw new Exception("No se pudo guardar una imagen", 500);
                }
                $ruta_bd = "cargasImagenes/fotosPropiedades/" . $id_propiedad . "/" . $nombre;
                $orden = $cantidad_actual + $i + 1;

                $this->propiedades->insertarFoto($id_propiedad, $ruta_bd, $orden);
            }
        }

        public function agregarFotos($id, $fotos){
            $this->existePropiedad($id);
            $this->validarFotos($fotos);

            $cantidad_actual = $this->propiedades->contarFotos($id);
            $cantidad_nuevas = $fotos !== null && isset($fotos["name"]) ? count($fotos["name"]) : 0;

            if($cantidad_actual + $cantidad_nuevas > 5){
                throw new Exception("La propiedad no puede tener mas de 5 imagenes", 400);
            }

            $this->guardarFotos($id, $fotos);

            $propiedad = $this->propiedades->buscarPropiedad($id);
            $propiedad["fotos"] = $this->propiedades->obtenerFotos($id);
            return $propiedad;
        }

        public function borrarFoto($id_propiedad, $id_foto){
            $this->existePropiedad($id_propiedad);
            $foto = $this->propiedades->buscarFoto($id_foto);
            if($foto === false || (int) $foto["id_propiedad"] !== (int) $id_propiedad){
                throw new Exception("La foto no pertenece a la propiedad", 404);
            }

            $ruta = __DIR__ . "/../" . $foto["ruta_foto"];
            
            if(file_exists($ruta)){
                unlink($ruta);
            }
            $this->propiedades->eliminarFoto($id_foto);
        }
    }
?>

<?php
    // Carga los archivos que necesita este servicio
    require_once __DIR__ . '/../api/propiedades.php';

    class ServicioPropiedades{
        private Propiedades $propiedades;

        // Prepara el acceso a los datos que necesita este servicio
        public function __construct(Propiedades $propiedades){
            $this->propiedades = $propiedades;
        }

        // Valida los datos de alta y de modificacion
        // Comprueba los campos obligatorios de la propiedad, la altura, el precio y la presencia del estado
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
            if(!isset($datos["estado"]) || trim($datos["estado"]) === ""){
                throw new Exception("El estado de la propiedad es invalido", 400);
            }
        }

        // Comprueba que el id recibido no sea nulo y pase la validacion de numero entero
        private function validarId($id){
            if($id === null || !filter_var($id, FILTER_VALIDATE_INT)){
                throw new Exception("El id de la propiedad es invalido", 400);
            }
        }

        // Valida el id y busca la propiedad; si no existe, devuelve un error
        private function existePropiedad($id){
            $this->validarId($id);
            $propiedad = $this->propiedades->buscarPropiedad($id);
            if($propiedad === false){
                throw new Exception("No existe una propiedad con ese id", 404);
            }
            return $propiedad;
        }

        // Lista las propiedades de la inmobiliaria indicada y agrega las fotos de cada una
        public function listarPropiedades($id_inmobiliaria){
            if($id_inmobiliaria !== null && !filter_var($id_inmobiliaria, FILTER_VALIDATE_INT)){
                throw new Exception("El id de la inmobiliaria es invalido", 400);
            }
            $propiedades = $this->propiedades->listarPropiedades($id_inmobiliaria);
            // Agrega las fotos a cada propiedad de la lista
            foreach($propiedades as &$propiedad){
                $propiedad["fotos"] = $this->propiedades->obtenerFotos($propiedad["id_propiedad"]);
            }
            return $propiedades;
        }

        // Devuelve los estados disponibles para las propiedades
        public function listarEstados(){
            return $this->propiedades->listarEstados();
        }

        // Valida los filtros de busqueda y devuelve las propiedades encontradas con sus fotos
        public function filtrarPropiedades($parametros){
            $filtros = [];
            // Quita espacios de los filtros de ubicacion y usa null cuando estan vacios
            foreach(["ciudad", "provincia"] as $campo){
                $valor = trim($parametros[$campo] ?? "");
                $filtros[$campo] = $valor === "" ? null : $valor;
            }

            // Comprueba que los limites de precio sean numeros mayores o iguales a cero
            foreach(["precio_min", "precio_max"] as $campo){
                $valor = $parametros[$campo] ?? "";
                if($valor !== "" && (!is_numeric($valor) || $valor < 0)){
                    throw new Exception("El filtro $campo debe ser un numero mayor o igual a 0", 400);
                }
                $filtros[$campo] = $valor === "" ? null : $valor;
            }
            // Rechaza un rango donde el precio minimo supera al maximo
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
            // Agrega las fotos a cada propiedad de la lista
            foreach($propiedades as &$propiedad){
                $propiedad["fotos"] = $this->propiedades->obtenerFotos($propiedad["id_propiedad"]);
            }
            return $propiedades;
        }

        // Busca la propiedad y agrega sus fotos antes de devolverla
        public function obtenerPropiedad($id){
            $propiedades = $this->existePropiedad($id);
            $propiedades["fotos"] = $this->propiedades->obtenerFotos($id);
            return $propiedades;
        }

        // Valida la inmobiliaria, los datos y las fotos, crea la propiedad y devuelve sus fotos
        public function crearPropiedad($datos, $fotos = null){
            if(!isset($datos["id_inmobiliaria"]) || !filter_var($datos["id_inmobiliaria"], FILTER_VALIDATE_INT)){
                throw new Exception("Falta la inmobiliaria", 400);
            }
            if(!$this->propiedades->inmobiliariaExiste($datos["id_inmobiliaria"])){
                throw new Exception("La inmobiliaria no existe", 404);
            }

            $this->validar($datos);
            $estado = trim($datos["estado"]);

            if($estado !== "EN VENTA" && $estado !== "EN ALQUILER"){
                throw new Exception("El estado de la propiedad es invalido", 400);
            }

            // Busca el id del estado para guardarlo con los datos de la propiedad
            $id_estado_propiedad = $this->propiedades->obtenerIdEstado($estado);
            if($id_estado_propiedad === false){
                throw new Exception("El estado de la propiedad no existe", 400);
            }

            $datos["id_estado_propiedad"] = $id_estado_propiedad;

            $this->validarFotos($fotos);

            $id_propiedad = $this->propiedades->insertarPropiedad($datos);
            $this->guardarFotos($id_propiedad, $fotos);
            $propiedad = $this->propiedades->buscarPropiedad($id_propiedad);

            return $this->propiedades->obtenerFotos($id_propiedad);
        }

        // Comprueba que la propiedad exista, valida los datos y el estado y guarda los cambios
        public function modificarPropiedad($id, $datos){
            $this->existePropiedad($id);
            $this->validar($datos);

            $estado = trim($datos["estado"]);
            if($estado !== "EN VENTA" && $estado !== "EN ALQUILER" && $estado !== "VENDIDO"){
                throw new Exception("El estado de la propiedad es invalido", 400);
            }

            // Busca el id del estado para guardarlo con los datos de la propiedad
            $id_estado_propiedad = $this->propiedades->obtenerIdEstado($estado);

            if($id_estado_propiedad === false){
                throw new Exception("El estado de la propiedad no existe", 400);
            }

            $datos["id_estado_propiedad"] = $id_estado_propiedad;
            $this->propiedades->modificarPropiedad($id, $datos);

            return $this->propiedades->buscarPropiedad($id);
        }

        // Intenta borrar la propiedad y avisa si tiene alquileres asociados que lo impiden
        public function borrarPropiedad($id){
            $this->existePropiedad($id);
            try{
                $this->propiedades->eliminarPropiedad($id);
            }
            catch(PDOException $error){
                // Trata el error de integridad como un conflicto por alquileres asociados
                if($error->getCode() == "23000"){
                    throw new Exception("La propiedad tiene alquileres asociados y no se puede borrar", 409);
                }
                throw $error;
            }
        }

        /*VALIDAR FOTOS*/
        // Permite hasta cinco imagenes y comprueba que cada una no supere los 20 MB
        private function validarFotos($fotos){
            // Si no llegaron fotos, termina sin intentar procesarlas
            if($fotos === null || !isset($fotos["name"])){
                return;
            }

            $cantidad = count($fotos["name"]);
            if($cantidad > 5){
                throw new Exception("Solo se pueden cargar hasta 5 imagenes", 400);
            }

            // Expresa el limite de 20 MB en bytes para compararlo con el peso de cada imagen
            $limite = 20 * 1024 * 1024;
            for($i = 0; $i < $cantidad; $i++){
                if($fotos["size"][$i] > $limite){
                    throw new Exception("Cada imagen puede pesar como maximo 20 MB", 400);
                }
            }
        }

        /*GUARDAR FOTOS*/
        // Revisa las imagenes recibidas y guarda su contenido, tipo y orden en la base de datos
        private function guardarFotos($id_propiedad, $fotos){
            // Si no llegaron fotos, termina sin intentar procesarlas
            if($fotos === null || !isset($fotos["name"])){
                return;
            }

            // Las fotos se guardan en la base de datos y no en el disco (ya que el disco de Render es temporal)
            $cantidad_actual = $this->propiedades->contarFotos($id_propiedad);
            for($i = 0; $i < count($fotos["name"]); $i++){
                if($fotos["error"][$i] === UPLOAD_ERR_INI_SIZE || $fotos["error"][$i] === UPLOAD_ERR_FORM_SIZE){
                    throw new Exception("Una imagen supera el tamaño maximo permitido por el servidor", 400);
                }
                // Salta los archivos que no se subieron correctamente
                if($fotos["error"][$i] !== UPLOAD_ERR_OK){
                    continue;
                }

                // Se mira el contenido real del archivo, no solo la extension
                $info = @getimagesize($fotos["tmp_name"][$i]);
                if($info === false || !in_array($info["mime"], ["image/jpeg", "image/png", "image/webp"], true)){
                    throw new Exception("Formato de imagen no permitido", 400);
                }
                // Lee los bytes del archivo temporal para guardarlos en la base de datos
                $contenido = file_get_contents($fotos["tmp_name"][$i]);
                if($contenido === false){
                    throw new Exception("No se pudo leer una imagen", 500);
                }
                // Calcula el orden de la foto a partir de las que ya tiene la propiedad
                $orden = $cantidad_actual + $i + 1;

                $this->propiedades->insertarFotoEnBase($id_propiedad, $contenido, $info["mime"], $orden);
            }
        }

        /*DEVOLVER EL CONTENIDO DE UNA FOTO GUARDADA EN LA BASE*/
        // Busca la foto y devuelve su tipo y contenido; si llega como recurso, lo convierte en texto binario
        public function obtenerContenidoFoto($id_foto){
            if($id_foto === null || !filter_var($id_foto, FILTER_VALIDATE_INT)){
                throw new Exception("El id de la foto es invalido", 400);
            }
            $foto = $this->propiedades->buscarContenidoFoto($id_foto);
            if($foto === false || $foto["contenido"] === null){
                throw new Exception("No existe esa foto", 404);
            }
            return [
                "tipo" => $foto["tipo"],
                "contenido" => is_resource($foto["contenido"]) ? stream_get_contents($foto["contenido"]) : $foto["contenido"]
            ];
        }

        // Comprueba que la propiedad no supere las cinco fotos y devuelve sus datos con las fotos actualizadas
        public function agregarFotos($id, $fotos){
            $this->existePropiedad($id);
            $this->validarFotos($fotos);

            $cantidad_actual = $this->propiedades->contarFotos($id);
            $cantidad_nuevas = $fotos !== null && isset($fotos["name"]) ? count($fotos["name"]) : 0;

            // Cuenta las fotos guardadas y las nuevas para no superar el limite total
            if($cantidad_actual + $cantidad_nuevas > 5){
                throw new Exception("La propiedad no puede tener mas de 5 imagenes", 400);
            }

            $this->guardarFotos($id, $fotos);

            $propiedad = $this->propiedades->buscarPropiedad($id);
            $propiedad["fotos"] = $this->propiedades->obtenerFotos($id);
            return $propiedad;
        }

        // Comprueba que la foto sea de esa propiedad, borra el archivo si existe y elimina el registro
        public function borrarFoto($id_propiedad, $id_foto){
            $this->existePropiedad($id_propiedad);
            $foto = $this->propiedades->buscarFoto($id_foto);
            if($foto === false || (int) $foto["id_propiedad"] !== (int) $id_propiedad){
                throw new Exception("La foto no pertenece a la propiedad", 404);
            }

            $ruta = __DIR__ . "/../" . $foto["ruta_foto"];
            
            // Borra el archivo del disco solamente si esa ruta existe
            if(file_exists($ruta)){
                unlink($ruta);
            }
            $this->propiedades->eliminarFoto($id_foto);
        }
    }
?>

<?php
    require_once __DIR__ . '/../api/contactos.php';
    require_once __DIR__ . '/servicios.php';

    // Reglas de negocio. Ante un problema lanza Exception con el codigo HTTP como codigo de la excepcion.
    class ServicioContacto{
        private Contactos $contactos;

        public function __construct(Contactos $contactos){
            $this->contactos = $contactos;
        }

        private function validarId($id, $nombre){
            if($id === null || !filter_var($id, FILTER_VALIDATE_INT)){
                throw new Exception("El $nombre es invalido", 400);
            }
        }

        // Valida un texto obligatorio con largo maximo
        private function validarTexto($datos, $campo, $nombre, $maximo){
            if(!isset($datos[$campo]) || !is_string($datos[$campo]) || trim($datos[$campo]) === ""){
                throw new Exception("Falta completar $nombre", 400);
            }
            if(mb_strlen(trim($datos[$campo])) > $maximo){
                throw new Exception("$nombre no puede superar los $maximo caracteres", 400);
            }
        }

        // Datos para mostrar en el formulario (nunca incluye el email de la inmobiliaria)
        public function datosFormulario($id_inmobiliaria, $id_propiedad){
            $this->validarId($id_inmobiliaria, "id de la inmobiliaria");
            $this->validarId($id_propiedad, "id de la propiedad");

            $propiedad = $this->contactos->buscarPropiedadConInmobiliaria($id_inmobiliaria, $id_propiedad);
            if($propiedad === false){
                throw new Exception("La propiedad no pertenece a esa inmobiliaria", 404);
            }

            return [
                "id_propiedad" => (int) $propiedad["id_propiedad"],
                "id_inmobiliaria" => (int) $propiedad["id_inmobiliaria"],
                "nombre_inmobiliaria" => $propiedad["nombre_inmobiliaria"],
                "descripcion" => $propiedad["descripcion"],
                "precio" => $propiedad["precio"],
                "provincia" => $propiedad["provincia"],
                "ciudad" => $propiedad["ciudad"],
                "calle" => $propiedad["calle"],
                "altura" => $propiedad["altura"]
            ];
        }

        // Le manda a la inmobiliaria la consulta de una persona interesada en una de sus propiedades
        public function enviarConsulta($datos){
            $this->validarId($datos["id_inmobiliaria"] ?? null, "id de la inmobiliaria");
            $this->validarId($datos["id_propiedad"] ?? null, "id de la propiedad");
            $this->validarTexto($datos, "nombre", "nombre de quien consulta", 50);
            $this->validarTexto($datos, "apellido", "apellido de quien consulta", 50);
            $this->validarTexto($datos, "contacto", "la forma de contacto", 100);
            $this->validarTexto($datos, "descripcion", "la descripcion", 1000);

            $propiedad = $this->contactos->buscarPropiedadConInmobiliaria($datos["id_inmobiliaria"], $datos["id_propiedad"]);
            if($propiedad === false){
                throw new Exception("La propiedad no pertenece a esa inmobiliaria", 404);
            }
            if(!filter_var($propiedad["email_inmobiliaria"], FILTER_VALIDATE_EMAIL)){
                throw new Exception("La inmobiliaria no tiene un email de contacto cargado", 422);
            }
            
            $nombre = trim($datos["nombre"]);
            $apellido = trim($datos["apellido"]);
            $contacto = trim($datos["contacto"]);
            $descripcion = trim($datos["descripcion"]);
            $direccion = $propiedad["calle"] . " " . $propiedad["altura"] . ", " . $propiedad["ciudad"] . ", " . $propiedad["provincia"];

            $html = "<p>Hola " . htmlspecialchars($propiedad["nombre_inmobiliaria"]) . ", recibiste una consulta por una de tus propiedades.</p>"
                  . "<p><b>Propiedad:</b> " . htmlspecialchars($direccion) . " (codigo " . (int) $propiedad["id_propiedad"] . ")<br>"
                  . "<b>Descripcion:</b> " . htmlspecialchars($propiedad["descripcion"]) . "<br>"
                  . "<b>Precio:</b> $" . htmlspecialchars($propiedad["precio"]) . "</p>"
                  . "<p><b>Nombre del interesado:</b> " . htmlspecialchars($nombre) . " " . htmlspecialchars($apellido) . "</p>"
                  . "<p><b>Forma de contacto de la persona interesada:</b> " . htmlspecialchars($contacto) . "</p>"
                  . "<p><b>Mensaje:</b><br>" . nl2br(htmlspecialchars($descripcion)) . "</p>";

            // si la persona dejo un email, la inmobiliaria puede contestarle directo con "Responder"
            $enviado = enviarCorreo($propiedad["email_inmobiliaria"], "Consulta por la propiedad de " . $direccion, $html, $contacto);
            if(!$enviado){
                throw new Exception("No se pudo enviar tu consulta, intenta nuevamente mas tarde", 500);
            }

            return ["mensaje" => "Tu consulta fue enviada a la inmobiliaria"];
        }
    }
?>

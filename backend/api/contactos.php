<?php
    class Contactos{
        private PDO $conexion;

        // Guarda la conexion a la base de datos para usarla en las consultas
        public function __construct(PDO $conexion){
            $this->conexion = $conexion;
        }

        // Devuelve la propiedad con su direccion y los datos de la inmobiliaria (incluido su email),
        // o false si esa propiedad no pertenece a esa inmobiliaria
        // Busca la propiedad con su direccion y los datos de contacto de la inmobiliaria indicada
        public function buscarPropiedadConInmobiliaria($id_inmobiliaria, $id_propiedad){
            $consulta = $this->conexion->prepare('SELECT p.id_propiedad, p.descripcion, p.precio,
                           d.provincia, d.ciudad, d.calle, d.altura,
                           i.id_inmobiliaria, i.nombre AS nombre_inmobiliaria, i.email AS email_inmobiliaria
                    FROM inmobiliaria_propiedad ip
                    INNER JOIN propiedad p ON p.id_propiedad = ip.id_propiedad
                    INNER JOIN direccion d ON d.id_direccion = p.id_direccion
                    INNER JOIN inmobiliaria i ON i.id_inmobiliaria = ip.id_inmobiliaria
                    WHERE ip.id_inmobiliaria = :id_inmobiliaria AND ip.id_propiedad = :id_propiedad');
            $consulta->execute([":id_inmobiliaria" => $id_inmobiliaria, ":id_propiedad" => $id_propiedad]);
            return $consulta->fetch(PDO::FETCH_ASSOC);
        }
    }
?>

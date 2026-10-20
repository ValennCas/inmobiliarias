<?php
    // Toma el origen enviado por el navegador o usa un texto vacio si no llego
    $origen = $_SERVER["HTTP_ORIGIN"] ?? "";

    // Orígenes de desarrollo + los que se agreguen en CORS_ORIGINS (separados por coma).
    // Si el front y el backend salen del mismo dominio (Render) no hace falta agregar nada.
    require_once __DIR__ . '/entorno.php';
    // Junta los origenes locales con los configurados, separados por coma y sin espacios en los extremos
    $origenes_permitidos = array_merge(
        ["http://127.0.0.1:5500", "http://localhost:5500"],
        array_filter(array_map("trim", explode(",", entorno("CORS_ORIGINS"))))
    );

    // Autoriza la lectura de la respuesta desde ese origen solamente si esta en la lista permitida
    if(in_array($origen, $origenes_permitidos, true)){
        header("Access-Control-Allow-Origin: $origen");
    }

    // Indica que la respuesta puede variar segun el origen para que las caches lo tengan en cuenta
    header("Vary: Origin");
    // Indica que la respuesta usa JSON con caracteres UTF-8
    header("Content-Type: application/json; charset=utf-8");
    // Informa al navegador los metodos y encabezados admitidos para las peticiones entre origenes
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");

    // Responde sin contenido a la consulta previa del navegador y termina la peticion
    if($_SERVER["REQUEST_METHOD"] === "OPTIONS"){
        http_response_code(204);
        exit();
    }
?>
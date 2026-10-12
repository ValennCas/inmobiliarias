<?php
    $origen = $_SERVER["HTTP_ORIGIN"] ?? "";

    $origenes_permitidos = ["http://127.0.0.1:5500", "http://localhost:5500"];

    if(in_array($origen, $origenes_permitidos, true)){
        header("Access-Control-Allow-Origin: $origen");
    }

    header("Vary: Origin");
    header("Content-Type: application/json; charset=utf-8");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");

    if($_SERVER["REQUEST_METHOD"] === "OPTIONS"){
        http_response_code(204);
        exit();
    }
?>
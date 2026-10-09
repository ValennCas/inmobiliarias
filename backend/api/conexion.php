<?php
    try{
        $conexion = new PDO("mysql:host = localhost; dbname = inmobiliaria; charset = utf8mb4",
            "root",
            ""
        );
        $conexion-> setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );  
    }
    catch(PDOException $error){
        http_response_code(500);
        header("Content-Type: application/json; charset= utf-8");
        echo json_encode([
            "error"=> "No se puede conectar en este momento, intentelo mas tarde!"
        ]);
        exit();
    }
?>
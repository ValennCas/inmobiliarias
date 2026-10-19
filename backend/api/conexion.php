<?php
    require_once __DIR__ . '/../config/entorno.php';

    // Los datos de la base salen de las variables de entorno (backend/.env.local en tu maquina, el panel de Render en linea).
    // Sin configurar, usa los valores de XAMPP.
    $certificados = null;
    try{
        $opciones = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

        if(in_array(strtolower(entorno("DB_SSL")), ["1", "true", "si"], true)){
            $certificados = entorno("DB_SSL_CA", "/etc/ssl/certs/ca-certificates.crt");
            if(file_exists($certificados)){
                $opciones[PDO::MYSQL_ATTR_SSL_CA] = $certificados;
            }
        }

        $conexion = new PDO(
            "mysql:host=" . entorno("DB_HOST", "localhost") . ";port=" . entorno("DB_PORT", "3306") . ";dbname=" . entorno("DB_NAME", "inmobiliaria") . ";charset=utf8mb4",
            entorno("DB_USER", "root"),
            entorno("DB_PASS", ""),
            $opciones
        );
    }
    catch(PDOException $error){
        error_log("No se pudo conectar a la base: " . $error->getMessage()
            . " | host=" . entorno("DB_HOST", "localhost") . " puerto=" . entorno("DB_PORT", "3306")
            . " | certificado=" . ($certificados ?? "(sin SSL)") . ($certificados !== null ? " existe=" . (file_exists($certificados) ? "si" : "no") : ""));
        http_response_code(500);
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode([
            "error"=> "No se puede conectar en este momento, intentelo mas tarde!"
        ]);
        exit();
    }
?>

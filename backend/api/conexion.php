<?php
    require_once __DIR__ . '/../config/entorno.php';

    // Los datos de la base salen de las variables de entorno (backend/.env.local en tu maquina, el panel de Render en linea).
    // Sin configurar, usa los valores de XAMPP.
    $certificados = null;
    try{
        // Configura PDO para que los errores de la base de datos lancen excepciones
        $opciones = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

        // Si se activo SSL, busca el certificado configurado para la conexion
        if(in_array(strtolower(entorno("DB_SSL")), ["1", "true", "si"], true)){
            $certificados = entorno("DB_SSL_CA", "/etc/ssl/certs/ca-certificates.crt");
            // Agrega el certificado a las opciones solamente si el archivo existe
            if(file_exists($certificados)){
                $opciones[PDO::MYSQL_ATTR_SSL_CA] = $certificados;
            }
        }

        // Abre la conexion MySQL con los datos del entorno y el juego de caracteres utf8mb4
        $conexion = new PDO(
            "mysql:host=" . entorno("DB_HOST", "localhost") . ";port=" . entorno("DB_PORT", "3306") . ";dbname=" . entorno("DB_NAME", "inmobiliaria") . ";charset=utf8mb4",
            entorno("DB_USER", "root"),
            entorno("DB_PASS", ""),
            $opciones
        );

        // Los vencimientos que PHP guarda (los codigos para cambiar la contraseña)
        // se comparan contra NOW() de la base, que en servicios en linea esta en UTC, y vencen antes de tiempo
        $zona = entorno("APP_TIMEZONE", "America/Argentina/Buenos_Aires");
        // Usa la zona de Buenos Aires si la configurada no es valida
        if(!in_array($zona, DateTimeZone::listIdentifiers(), true)){
            $zona = "America/Argentina/Buenos_Aires";
        }
        // Aplica la misma zona horaria a PHP y a esta conexion de MySQL para comparar los vencimientos
        date_default_timezone_set($zona);
        $conexion->exec("SET time_zone = '" . (new DateTime("now"))->format("P") . "'");
    }
    // Registra el detalle del error y responde con un mensaje general en JSON
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

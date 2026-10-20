<?php
    // Entrega una foto guardada en la base de datos (publico, igual que ver las propiedades).
    // Punto de entrada: http://localhost/inmobiliarias/backend/controller/fotoController.php?id_foto=1
    require_once __DIR__ . '/../api/conexion.php';
    require_once __DIR__ . '/../api/propiedades.php';
    require_once __DIR__ . '/../Servicio/servicioPropiedades.php';

    $servicio = new ServicioPropiedades(new Propiedades($conexion));

    try{
        $foto = $servicio->obtenerContenidoFoto($_GET["id_foto"] ?? null);

        header("Content-Type: " . $foto["tipo"]);
        header("Content-Length: " . strlen($foto["contenido"]));
        // una foto nunca cambia (si se borra, su id no se reutiliza): el navegador la puede guardar
        header("Cache-Control: public, max-age=31536000, immutable");
        header("X-Content-Type-Options: nosniff");
        echo $foto["contenido"];
    }
    catch(PDOException $error){
        http_response_code(500);
    }
    catch(Exception $error){
        http_response_code($error->getCode());
    }
?>

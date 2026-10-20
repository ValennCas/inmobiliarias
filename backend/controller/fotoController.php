<?php
    // Entrega una foto guardada en la base de datos (publico, igual que ver las propiedades).
    // Punto de entrada: http://localhost/inmobiliarias/backend/controller/fotoController.php?id_foto=1
    // Carga la conexion y los archivos que necesita este controlador
    require_once __DIR__ . '/../api/conexion.php';
    require_once __DIR__ . '/../api/propiedades.php';
    require_once __DIR__ . '/../Servicio/servicioPropiedades.php';

    // Prepara el servicio que permite obtener las fotos guardadas
    $servicio = new ServicioPropiedades(new Propiedades($conexion));

    try{
        // Busca el tipo y el contenido de la foto indicada en la URL
        $foto = $servicio->obtenerContenidoFoto($_GET["id_foto"] ?? null);

        // Indica el tipo de imagen y la cantidad de bytes que se van a enviar
        header("Content-Type: " . $foto["tipo"]);
        header("Content-Length: " . strlen($foto["contenido"]));
        // una foto nunca cambia (si se borra, su id no se reutiliza): el navegador la puede guardar
        header("Cache-Control: public, max-age=31536000, immutable");
        // Indica al navegador que respete el tipo de contenido informado
        header("X-Content-Type-Options: nosniff");
        // Envia el contenido de la imagen directamente en la respuesta
        echo $foto["contenido"];
    }
    // Responde con el codigo 500 si falla una operacion de la base de datos
    catch(PDOException $error){
        http_response_code(500);
    }
    // Responde con el codigo del error recibido
    catch(Exception $error){
        http_response_code($error->getCode());
    }
?>

<?php

    // Carga los archivos que necesita este servicio
    require_once __DIR__ . '/../config/entorno.php';
    // Usa la zona horaria configurada o la de Buenos Aires si no se definio otra
    date_default_timezone_set(entorno("APP_TIMEZONE", "America/Argentina/Buenos_Aires"));

    // Saca saltos de linea de un valor que va en un header del correo
    // Quita los saltos de linea y los espacios de los extremos de un valor para el encabezado del correo
    function limpiarHeader($valor){
        return trim(str_replace(["\r", "\n"], " ", (string) $valor));
    }

    // Envia el correo por la API web de Brevo (HTTPS, puerto 443). Render gratis bloquea el SMTP, esto no
    // MAIL_REMITENTE tiene que ser una casilla verificada en Brevo
    // Arma el correo y lo envia por la API de Brevo con la configuracion del entorno
    function enviarCorreoPorApi($destinatario, $asunto, $html, $responderA = null){
        $remitente = entorno("MAIL_REMITENTE");
        if(!filter_var($remitente, FILTER_VALIDATE_EMAIL)){
            error_log("Correo no enviado: con MAIL_API_KEY hace falta MAIL_REMITENTE (una casilla verificada en Brevo)");
            return false;
        }

        // Prepara el remitente, el destinatario, el asunto y el contenido del correo para la API
        $cuerpo = [
            "sender" => ["name" => limpiarHeader(entorno("MAIL_NOMBRE", "Inmobiliarias")), "email" => $remitente],
            "to" => [["email" => $destinatario]],
            "subject" => $asunto,
            "htmlContent" => $html
        ];
        $responderA = limpiarHeader($responderA);
        if(filter_var($responderA, FILTER_VALIDATE_EMAIL)){
            $cuerpo["replyTo"] = ["email" => $responderA];
        }

        // Configura la peticion a la API con el contenido JSON y la clave de acceso
        $curl = curl_init(entorno("MAIL_API_URL", "https://api.brevo.com/v3/smtp/email"));
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($cuerpo, JSON_UNESCAPED_UNICODE));
        curl_setopt($curl, CURLOPT_HTTPHEADER, ["accept: application/json", "content-type: application/json", "api-key: " . entorno("MAIL_API_KEY")]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 15);
        curl_setopt($curl, CURLOPT_SSL_OPTIONS, CURLSSLOPT_NATIVE_CA);

        // Envia la peticion y consulta el codigo HTTP para saber si la API la acepto
        $respuesta = curl_exec($curl);
        $estado = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if($respuesta === false){
            error_log("No se pudo enviar el correo por la API (cURL " . curl_errno($curl) . "): " . curl_error($curl));
            curl_close($curl);
            return false;
        }
        curl_close($curl);

        // Considera fallido el envio si la API devuelve un codigo fuera del rango de exito
        if($estado < 200 || $estado >= 300){
            error_log("No se pudo enviar el correo por la API (HTTP " . $estado . "): " . substr($respuesta, 0, 200));
            return false;
        }
        return true;
    }

    // Envia un correo: por la API web si hay MAIL_API_KEY, y si no por SMTP usando la casilla de MAIL_DSN
    // Devuelve true si salio y false si fallo
    // Valida el destinatario y elige el envio por API o por SMTP segun la configuracion
    function enviarCorreo($destinatario, $asunto, $html, $responderA = null){
        $destinatario = limpiarHeader($destinatario);
        $asunto = limpiarHeader($asunto);

        if(!filter_var($destinatario, FILTER_VALIDATE_EMAIL)){
            error_log("Correo no enviado: destinatario invalido");
            return false;
        }

        // Si hay una clave de API configurada, usa ese medio para enviar el correo
        if(entorno("MAIL_API_KEY") !== ""){
            return enviarCorreoPorApi($destinatario, $asunto, $html, $responderA);
        }

        // Separa los datos de la conexion SMTP y comprueba el formato configurado
        $dsn = parse_url(entorno("MAIL_DSN"));
        if($dsn === false || !isset($dsn["scheme"], $dsn["host"]) || !in_array($dsn["scheme"], ["smtp", "smtps"], true)){
            error_log("Correo no enviado: MAIL_DSN invalido (formato smtps://usuario:clave@servidor:puerto)");
            return false;
        }
        // Usa el puerto indicado o el valor habitual segun el tipo de conexion
        $puerto = $dsn["port"] ?? ($dsn["scheme"] === "smtps" ? 465 : 587);
        $usuario = isset($dsn["user"]) ? urldecode($dsn["user"]) : "";
        $clave = isset($dsn["pass"]) ? urldecode($dsn["pass"]) : "";

        $remitente = entorno("MAIL_REMITENTE") !== "" ? entorno("MAIL_REMITENTE") : $usuario;
        if(!filter_var($remitente, FILTER_VALIDATE_EMAIL)){
            error_log("Correo no enviado: falta un remitente valido (MAIL_REMITENTE o usuario del DSN)");
            return false;
        }

        // Si se indica un email para responder, quien recibe el correo contesta directo a esa persona
        $responderA = limpiarHeader($responderA);
        $headerResponderA = filter_var($responderA, FILTER_VALIDATE_EMAIL) ? "Reply-To: <" . $responderA . ">\r\n" : "";

        // Arma los encabezados y el contenido HTML del correo usando base64 para el envio
        $mensaje = "From: =?UTF-8?B?" . base64_encode(limpiarHeader(entorno("MAIL_NOMBRE", "Inmobiliarias"))) . "?= <" . $remitente . ">\r\n"
                 . "To: <" . $destinatario . ">\r\n"
                 . $headerResponderA
                 . "Subject: =?UTF-8?B?" . base64_encode($asunto) . "?=\r\n"
                 . "Date: " . date("r") . "\r\n"
                 . "Message-ID: <" . bin2hex(random_bytes(12)) . "@" . $dsn["host"] . ">\r\n"
                 . "MIME-Version: 1.0\r\n"
                 . "Content-Type: text/html; charset=UTF-8\r\n"
                 . "Content-Transfer-Encoding: base64\r\n"
                 . "\r\n"
                 . chunk_split(base64_encode($html));

        // Prepara la lectura del mensaje por partes para enviarlo mediante cURL
        $posicion = 0;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $dsn["scheme"] . "://" . $dsn["host"] . ":" . $puerto);
        curl_setopt($curl, CURLOPT_MAIL_FROM, "<" . $remitente . ">");
        curl_setopt($curl, CURLOPT_MAIL_RCPT, ["<" . $destinatario . ">"]);
        curl_setopt($curl, CURLOPT_UPLOAD, true);
        // Entrega a cURL el siguiente trozo del mensaje y avanza la posicion de lectura
        curl_setopt($curl, CURLOPT_READFUNCTION, function($curl, $archivo, $largo) use ($mensaje, &$posicion){
            $trozo = substr($mensaje, $posicion, $largo);
            $posicion += strlen($trozo);
            return $trozo;
        });
        curl_setopt($curl, CURLOPT_TIMEOUT, 15);
        curl_setopt($curl, CURLOPT_SSL_OPTIONS, CURLSSLOPT_NATIVE_CA);
        // Solicita una conexion cifrada para SMTP cuando se usa un puerto distinto de 25
        if($dsn["scheme"] === "smtp" && $puerto !== 25){
            curl_setopt($curl, CURLOPT_USE_SSL, CURLUSESSL_ALL);   
        }
        // Configura las credenciales SMTP si se indico un usuario
        if($usuario !== ""){
            curl_setopt($curl, CURLOPT_USERNAME, $usuario);
            curl_setopt($curl, CURLOPT_PASSWORD, $clave);
        }

        $resultado = curl_exec($curl);
        if($resultado === false){
            error_log("No se pudo enviar el correo (cURL " . curl_errno($curl) . "): " . curl_error($curl));
        }
        curl_close($curl);

        return $resultado !== false;
    }
?>

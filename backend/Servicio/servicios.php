<?php

    require_once __DIR__ . '/../config/entorno.php';
    date_default_timezone_set(entorno("APP_TIMEZONE", "America/Argentina/Buenos_Aires"));

    // Saca saltos de linea de un valor que va en un header del correo
    function limpiarHeader($valor){
        return trim(str_replace(["\r", "\n"], " ", (string) $valor));
    }

    // Envia un correo por SMTP usando la casilla de MAIL_DSN
    // Devuelve true si salio (o si quedo anotado en modo simulacion) y false si fallo
    function enviarCorreo($destinatario, $asunto, $html, $responderA = null){
        $destinatario = limpiarHeader($destinatario);
        $asunto = limpiarHeader($asunto);

        if(!filter_var($destinatario, FILTER_VALIDATE_EMAIL)){
            error_log("Correo no enviado: destinatario invalido");
            return false;
        }

        $dsn = parse_url(entorno("MAIL_DSN"));
        if($dsn === false || !isset($dsn["scheme"], $dsn["host"]) || !in_array($dsn["scheme"], ["smtp", "smtps"], true)){
            error_log("Correo no enviado: MAIL_DSN invalido (formato smtps://usuario:clave@servidor:puerto)");
            return false;
        }
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

        $posicion = 0;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $dsn["scheme"] . "://" . $dsn["host"] . ":" . $puerto);
        curl_setopt($curl, CURLOPT_MAIL_FROM, "<" . $remitente . ">");
        curl_setopt($curl, CURLOPT_MAIL_RCPT, ["<" . $destinatario . ">"]);
        curl_setopt($curl, CURLOPT_UPLOAD, true);
        curl_setopt($curl, CURLOPT_READFUNCTION, function($curl, $archivo, $largo) use ($mensaje, &$posicion){
            $trozo = substr($mensaje, $posicion, $largo);
            $posicion += strlen($trozo);
            return $trozo;
        });
        curl_setopt($curl, CURLOPT_TIMEOUT, 15);
        curl_setopt($curl, CURLOPT_SSL_OPTIONS, CURLSSLOPT_NATIVE_CA);
        if($dsn["scheme"] === "smtp" && $puerto !== 25){
            curl_setopt($curl, CURLOPT_USE_SSL, CURLUSESSL_ALL);   
        }
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

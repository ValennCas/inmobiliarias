<?php
    // Lee la configuracion del entorno

    function leerArchivoEnv($ruta){
        $valores = [];
        if(!is_readable($ruta)){
            return $valores;
        }
        foreach(file($ruta, FILE_IGNORE_NEW_LINES) as $linea){
            $linea = trim(ltrim($linea, "\xEF\xBB\xBF"));   // el ltrim saca el BOM que agregan algunos editores de Windows
            if($linea === "" || $linea[0] === "#" || strpos($linea, "=") === false){
                continue;
            }
            [$clave, $valor] = explode("=", $linea, 2);
            $valor = trim($valor);
            if(strlen($valor) >= 2 && ($valor[0] === '"' || $valor[0] === "'") && substr($valor, -1) === $valor[0]){
                $valor = substr($valor, 1, -1);   // saca las comillas que rodean el valor
            }
            $valores[trim($clave)] = $valor;
        }
        return $valores;
    }

    function entorno($clave, $defecto = ""){
        static $archivos = null;
        if($archivos === null){
            $archivos = array_merge(leerArchivoEnv(__DIR__ . '/../.env'), leerArchivoEnv(__DIR__ . '/../.env.local'));
        }

        $real = getenv($clave);
        if($real !== false && $real !== ""){
            return $real;
        }
        $valor = $archivos[$clave] ?? "";
        return $valor !== "" ? $valor : $defecto;
    }
?>

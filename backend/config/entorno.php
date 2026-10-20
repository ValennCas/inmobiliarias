<?php
    // Lee la configuracion del entorno

    // Lee un archivo de configuracion y devuelve sus claves y valores en un arreglo
    function leerArchivoEnv($ruta){
        $valores = [];
        // Si el archivo no se puede leer, devuelve un arreglo vacio
        if(!is_readable($ruta)){
            return $valores;
        }
        // Recorre el archivo linea por linea sin conservar los saltos de linea
        foreach(file($ruta, FILE_IGNORE_NEW_LINES) as $linea){
            $linea = trim(ltrim($linea, "\xEF\xBB\xBF"));   // el ltrim saca el BOM que agregan algunos editores de Windows
            // Salta las lineas vacias, los comentarios y las que no tienen un signo igual
            if($linea === "" || $linea[0] === "#" || strpos($linea, "=") === false){
                continue;
            }
            // Separa la clave del valor usando solo el primer signo igual
            [$clave, $valor] = explode("=", $linea, 2);
            $valor = trim($valor);
            if(strlen($valor) >= 2 && ($valor[0] === '"' || $valor[0] === "'") && substr($valor, -1) === $valor[0]){
                $valor = substr($valor, 1, -1);   // saca las comillas que rodean el valor
            }
            // Guarda el valor usando la clave sin espacios en los extremos
            $valores[trim($clave)] = $valor;
        }
        return $valores;
    }

    // Busca la configuracion primero en el entorno del sistema, despues en los archivos y por ultimo usa el valor por defecto
    function entorno($clave, $defecto = ""){
        // Conserva los valores cargados entre llamadas para no leer los archivos cada vez
        static $archivos = null;
        // Carga los archivos una sola vez; los valores de .env.local reemplazan a los de .env
        if($archivos === null){
            $archivos = array_merge(leerArchivoEnv(__DIR__ . '/../.env'), leerArchivoEnv(__DIR__ . '/../.env.local'));
        }

        // Consulta si la clave tiene un valor definido en el entorno del sistema
        $real = getenv($clave);
        // Da prioridad al valor del sistema cuando existe y no esta vacio
        if($real !== false && $real !== ""){
            return $real;
        }
        // Si no hay un valor del sistema, busca la clave en los archivos cargados
        $valor = $archivos[$clave] ?? "";
        // Usa el valor por defecto cuando la clave falta o tiene un texto vacio
        return $valor !== "" ? $valor : $defecto;
    }
?>

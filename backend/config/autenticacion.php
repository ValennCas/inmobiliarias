<?php
    // Carga las clases necesarias para consultar y validar los tokens
    require_once __DIR__ . '/../api/seguridad.php';
    require_once __DIR__ . '/../Servicio/seguridad.php';

    // Valida el header "Authorization: Bearer <token>" y devuelve el id del usuario que le pertenece el token
    // Se agrega un parametro opcional ($id_usuario_esperado) para los controller que no necesitan controlar el $id_usuario
    // Lanza Exception 401 si el token no se envia, es invalido, esta vencido o no corresponde al usuario
    // Comprueba el token de la peticion y, si se indica un usuario esperado, verifica que le pertenezca
    function obtenerIdUsuarioAutenticado(SeguridadServicio $seguridad, $id_usuario_esperado = null){
        // Pasa los nombres de los encabezados a minusculas para buscar Authorization sin depender de como este escrito
        $headers = array_change_key_case(getallheaders(), CASE_LOWER);
        $authorization = $headers["authorization"] ?? null;

        // Rechaza la peticion si falta el encabezado o no empieza con Bearer seguido de un espacio
        if($authorization === null || !str_starts_with($authorization, "Bearer ")){
            throw new Exception("Token no enviado", 401);
        }

        // Quita el prefijo Bearer para obtener el token enviado por el cliente
        $token = substr($authorization, 7);
        // Calcula el hash del token y lo valida para obtener el usuario asociado
        $id_usuario_token = $seguridad->verificarToken(hash("sha256", $token));

        // Si se esperaba un usuario concreto, impide usar un token que pertenezca a otro
        if($id_usuario_esperado !== null && (int) $id_usuario_token !== (int) $id_usuario_esperado){
            throw new Exception("El token no corresponde al usuario", 401);
        }

        return $id_usuario_token;
    }
?>

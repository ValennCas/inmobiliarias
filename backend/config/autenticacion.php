<?php
    require_once __DIR__ . '/../api/seguridad.php';
    require_once __DIR__ . '/../Servicio/seguridad.php';

    // Valida el header "Authorization: Bearer <token>" y devuelve el id del usuario que le pertenece el token
    // Se agrega un parametro opcional ($id_usuario_esperado) para los controller que no necesitan controlar el $id_usuario
    // Lanza Exception 401 si el token no se envia, es invalido, esta vencido o no corresponde al usuario
    function obtenerIdUsuarioAutenticado(SeguridadServicio $seguridad, $id_usuario_esperado = null){
        $headers = array_change_key_case(getallheaders(), CASE_LOWER);
        $authorization = $headers["authorization"] ?? null;

        if($authorization === null || !str_starts_with($authorization, "Bearer ")){
            throw new Exception("Token no enviado", 401);
        }

        $token = substr($authorization, 7);
        $id_usuario_token = $seguridad->verificarToken(hash("sha256", $token));

        if($id_usuario_esperado !== null && (int) $id_usuario_token !== (int) $id_usuario_esperado){
            throw new Exception("El token no corresponde al usuario", 401);
        }

        return $id_usuario_token;
    }
?>

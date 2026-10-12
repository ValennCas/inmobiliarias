<?php
    require_once "config/cors.php";
    require_once "Servicio/servicioRegistrar.php";
    require_once "Servicio/loginUsuario.php";
    require_once "Servicio/servicioInmobiliaria.php";
    require_once "Servicio/seguridad.php";
    require_once "Servicio/logout.php";
    require_once "api/conexion.php";

    #header('Content-Type: application/json; charset=utf-8');

    $recurso = $_GET["recurso"] ?? "";
    $metodo = $_SERVER["REQUEST_METHOD"];

    switch ($recurso) {
        case 'registro':
            if($metodo !== "POST"){
                http_response_code(405);
                echo json_encode(["error" => "Metodo no permitido"]);
                exit();
            }

            $servicioRegistrar = new ServicioRegistrar($conexion);
            $servicioRegistrar->registrarUsuario();

            break;
        case 'login':
            if($metodo !== "POST"){
                http_response_code(405);
                echo json_encode(["error" => "Metodo no permitido"]);
                exit();
            }

            $loginUsuario = new LoginUsuario($conexion);
            $loginUsuario->loguearUsuario();
            
            break;
        case 'inmobiliaria':
            $servicioInmobiliaria = new ServicioInmobiliaria($conexion);

            if($metodo === "POST"){
                $servicioInmobiliaria->crearInmobiliaria();
            }
            elseif($metodo === "GET"){
                $servicioInmobiliaria->buscarInmobiliaria();
            }
            elseif($metodo === "PUT"){
                $servicioInmobiliaria->actualizarInmobiliaria();
            }
            else{
                http_response_code(405);
                echo json_encode(["error" => "Metodo no permitido"]);
                exit();
            }

            break;
        case 'logout':
            $logout = new Logout($conexion);

            if($metodo !== "DELETE"){
                http_response_code(405);
                echo json_encode(["error" => "Metodo no permitido"]);
                exit();
            }
            $logout->cerrarSesion();

            break;
        default:
            http_response_code(404);
            echo json_encode(["error" => "No se pudo realizar la accion"]);
            exit();

            break;
    }
?>
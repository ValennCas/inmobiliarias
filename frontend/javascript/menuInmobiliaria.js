(function(){
    const API_INMOBILIARIA = BASE_BACKEND + "controller/inmobiliariaController.php";
    const API_USUARIO = BASE_BACKEND + "controller/usuarioController.php";

    /*DATOS SESION*/
    const token = localStorage.getItem("token");
    const id_usuario = localStorage.getItem("id_usuario");
    const id_inmobiliaria = localStorage.getItem("id_inmobiliaria");

    /*VALIDAR SESION*/

    if(!token || !id_usuario || !id_inmobiliaria){
        window.location.replace("index.html");
        return;
    }

    /*ELEMENTOS DATOS INMOBILIARIA*/
    const nombreInmobiliaria = document.querySelector("#nombreInmobiliaria");
    const emailInmobiliaria = document.querySelector("#emailInmobiliaria");

    /*MENU ESCRITORIO*/
    const btnPropiedades = document.querySelector("#btnPropiedades");
    const btnAlquileres = document.querySelector("#btnAlquileres");
    const btnInquilinos = document.querySelector("#btnInquilinos");
    const btnMisDatos = document.querySelector("#btnMisDatos");
    const btnCambiarContrasenia = document.querySelector("#btnCambiarContrasenia");
    const btnVolverInmobiliarias = document.querySelector("#btnVolverInmobiliarias");
    const btnCerrarSesion = document.querySelector("#btnCerrarSesion");
    const btnEditarInmobiliaria = document.querySelector("#btnEditarInmobiliaria");
    const btnVerInmobiliaria = document.querySelector("#btnVerInmobiliaria");

    /*MENU CELULAR*/
    const btnPropiedadesMobile = document.querySelector("#btnPropiedadesMobile");
    const btnAlquileresMobile = document.querySelector("#btnAlquileresMobile");
    const btnInquilinosMobile = document.querySelector("#btnInquilinosMobile");
    const btnVolverMobile = document.querySelector("#btnVolverMobile");
    const btnMenuMobile = document.querySelector("#btnMenuMobile");
    const submenuMobile = document.querySelector("#submenuMobile");
    const btnEditarInmobiliariaMobile = document.querySelector("#btnEditarInmobiliariaMobile");
    const btnMisDatosMobile = document.querySelector("#btnMisDatosMobile");
    const btnCambiarContraseniaMobile = document.querySelector("#btnCambiarContraseniaMobile");
    const btnCerrarSesionMobile = document.querySelector("#btnCerrarSesionMobile");

    /*VER INMOBILIARIA*/
    const ventanaVerInmobiliaria = document.querySelector("#ventanaVerInmobiliaria");
    const btnCerrarVerInmobiliaria = document.querySelector("#btnCerrarVerInmobiliaria");
    const verNombreInmobiliaria = document.querySelector("#verNombreInmobiliaria");
    const verEmailInmobiliaria = document.querySelector("#verEmailInmobiliaria");
    const verAreaInmobiliaria = document.querySelector("#verAreaInmobiliaria");
    const verTelefonoInmobiliaria = document.querySelector("#verTelefonoInmobiliaria");
    const verProvinciaInmobiliaria = document.querySelector("#verProvinciaInmobiliaria");
    const verCiudadInmobiliaria = document.querySelector("#verCiudadInmobiliaria");
    const verCalleInmobiliaria = document.querySelector("#verCalleInmobiliaria");
    const verAlturaInmobiliaria = document.querySelector("#verAlturaInmobiliaria");

    /*EDITAR INMOBILIARIA*/
    const ventanaEditarInmobiliaria = document.querySelector("#ventanaEditarInmobiliaria");
    const btnCerrarEditarInmobiliaria = document.querySelector("#btnCerrarEditarInmobiliaria");
    const btnCancelarEditarInmobiliaria = document.querySelector("#btnCancelarEditarInmobiliaria");
    const formEditarInmobiliaria = document.querySelector("#formEditarInmobiliaria");
    const editarNombreInmobiliaria = document.querySelector("#editarNombreInmobiliaria");
    const editarEmailInmobiliaria = document.querySelector("#editarEmailInmobiliaria");
    const editarAreaInmobiliaria = document.querySelector("#editarAreaInmobiliaria");
    const editarTelefonoInmobiliaria = document.querySelector("#editarTelefonoInmobiliaria");
    const editarProvinciaInmobiliaria = document.querySelector("#editarProvinciaInmobiliaria");
    const editarCiudadInmobiliaria = document.querySelector("#editarCiudadInmobiliaria");
    const editarCalleInmobiliaria = document.querySelector("#editarCalleInmobiliaria");
    const editarAlturaInmobiliaria = document.querySelector("#editarAlturaInmobiliaria");
    const mensajeEditarInmobiliaria = document.querySelector("#mensajeEditarInmobiliaria");

    /*MIS DATOS*/
    const ventanaMisDatos = document.querySelector("#ventanaMisDatos");
    const btnCerrarMisDatos = document.querySelector("#btnCerrarMisDatos");
    const datoNombreUsuario = document.querySelector("#datoNombreUsuario");
    const datoApellidoUsuario = document.querySelector("#datoApellidoUsuario");
    const datoEmailUsuario = document.querySelector("#datoEmailUsuario");

    /*EDITAR MIS DATOS*/
    const btnEditarMisDatos = document.querySelector("#btnEditarMisDatos");
    const ventanaEditarMisDatos = document.querySelector("#ventanaEditarMisDatos");
    const btnCerrarEditarMisDatos = document.querySelector("#btnCerrarEditarMisDatos");
    const btnCancelarEditarUsuario = document.querySelector("#btnCancelarEditarUsuario");
    const formEditarMisDatos = document.querySelector("#formEditarMisDatos");
    const editarNombreUsuario = document.querySelector("#editarNombreUsuario");
    const editarApellidoUsuario = document.querySelector("#editarApellidoUsuario");
    const editarEmailUsuario = document.querySelector("#editarEmailUsuario");
    const mensajeEditarUsuario = document.querySelector("#mensajeEditarUsuario");

    /*VALIDAR RESPUESTA DE SESION*/
    function validarSesionMenu(res){
        if(res.status === 401){
            localStorage.clear();
            window.location.replace("index.html");
            return false;
        }

        return true;
    }

    /*CARGAR DATOS DEL MENU*/
    async function cargarDatosInmobiliariaMenu(){
        try{
            const res = await fetch(
                `${API_INMOBILIARIA}?id_usuario=${id_usuario}&id_inmobiliaria=${id_inmobiliaria}`,
                {
                    method: "GET",
                    headers: {
                        "Authorization": `Bearer ${token}`
                    }
                }
            );

            if(!validarSesionMenu(res)){
                return;
            }

            const datos = await res.json();

            if(!res.ok){
                if(nombreInmobiliaria){
                    nombreInmobiliaria.textContent = "Inmobiliaria";
                }

                if(emailInmobiliaria){
                    emailInmobiliaria.textContent = "No se pudieron cargar los datos";
                }

                return;
            }

            const inmobiliaria = datos.inmobiliaria || datos;

            if(nombreInmobiliaria){
                nombreInmobiliaria.textContent = inmobiliaria.nombre || "Inmobiliaria";
            }

            if(emailInmobiliaria){
                emailInmobiliaria.textContent = inmobiliaria.email || "";
            }
        }catch(error){
            console.error(error);

            if(nombreInmobiliaria){
                nombreInmobiliaria.textContent = "Inmobiliaria";
            }

            if(emailInmobiliaria){
                emailInmobiliaria.textContent = "No se pudieron cargar los datos";
            }
        }
    }

    /*VER DATOS INMOBILIARIA*/
    async function abrirVerInmobiliaria(){
        try{
            mostrarCarga();

            const res = await fetch(
                `${API_INMOBILIARIA}?id_usuario=${id_usuario}&id_inmobiliaria=${id_inmobiliaria}`,
                {
                    method: "GET",
                    headers: {
                        "Authorization": `Bearer ${token}`
                    }
                }
            );

            if(!validarSesionMenu(res)){
                return;
            }

            const datos = await res.json();

            if(!res.ok){
                mostrarError(datos.error || datos.mensaje || "No se pudieron cargar los datos de la inmobiliaria");
                return;
            }

            const inmobiliaria = datos.inmobiliaria || datos;

            verNombreInmobiliaria.textContent = inmobiliaria.nombre || "";
            verEmailInmobiliaria.textContent = inmobiliaria.email || "";
            verAreaInmobiliaria.textContent = inmobiliaria.area_telefono || "";
            verTelefonoInmobiliaria.textContent = inmobiliaria.telefono || "";
            verProvinciaInmobiliaria.textContent = inmobiliaria.provincia || "";
            verCiudadInmobiliaria.textContent = inmobiliaria.ciudad || "";
            verCalleInmobiliaria.textContent = inmobiliaria.calle || "";
            verAlturaInmobiliaria.textContent = inmobiliaria.altura || "";

            ventanaVerInmobiliaria.classList.remove("ocultarVentana");
        }catch(error){
            console.error(error);
            mostrarError("No se pudo conectar con el servidor");
        }finally{
            ocultarCarga();
        }
    }

    /*CERRAR VER INMOBILIARIA*/
    function cerrarVerInmobiliaria(){
        ventanaVerInmobiliaria.classList.add("ocultarVentana");
    }

    /*ABRIR EDITAR INMOBILIARIA*/
    async function abrirEditarInmobiliaria(){
        try{
            mostrarCarga();

            const res = await fetch(
                `${API_INMOBILIARIA}?id_usuario=${id_usuario}&id_inmobiliaria=${id_inmobiliaria}`,
                {
                    method: "GET",
                    headers: {
                        "Authorization": `Bearer ${token}`
                    }
                }
            );

            if(!validarSesionMenu(res)){
                return;
            }

            const datos = await res.json();

            if(!res.ok){
                mostrarError(datos.error || datos.mensaje || "No se pudieron cargar los datos de la inmobiliaria");
                return;
            }

            const inmobiliaria = datos.inmobiliaria || datos;

            editarNombreInmobiliaria.value = inmobiliaria.nombre || "";
            editarEmailInmobiliaria.value = inmobiliaria.email || "";
            editarAreaInmobiliaria.value = inmobiliaria.area_telefono || "";
            editarTelefonoInmobiliaria.value = inmobiliaria.telefono || "";
            editarCalleInmobiliaria.value = inmobiliaria.calle || "";
            editarAlturaInmobiliaria.value = inmobiliaria.altura || "";

            if(inmobiliaria.provincia){
                editarProvinciaInmobiliaria.value = inmobiliaria.provincia;

                await cargarCiudades(
                    editarProvinciaInmobiliaria.value,
                    editarCiudadInmobiliaria
                );

                editarCiudadInmobiliaria.value = inmobiliaria.ciudad || "";
            }

            mensajeEditarInmobiliaria.textContent = "";
            ventanaEditarInmobiliaria.classList.remove("ocultarVentana");
        }catch(error){
            console.error(error);
            mostrarError("No se pudo conectar con el servidor");
        }finally{
            ocultarCarga();
        }
    }

    /*CERRAR EDITAR INMOBILIARIA*/
    function cerrarEditarInmobiliaria(){
        mensajeEditarInmobiliaria.textContent = "";
        ventanaEditarInmobiliaria.classList.add("ocultarVentana");
    }

    /*GUARDAR EDICION INMOBILIARIA*/
    if(formEditarInmobiliaria){
        formEditarInmobiliaria.addEventListener("submit", async function(evento){
            evento.preventDefault();

            if(
                editarNombreInmobiliaria.value.trim() === "" ||
                editarEmailInmobiliaria.value.trim() === "" ||
                editarAreaInmobiliaria.value.trim() === "" ||
                editarTelefonoInmobiliaria.value.trim() === "" ||
                editarProvinciaInmobiliaria.value === "" ||
                editarCiudadInmobiliaria.value === "" ||
                editarCalleInmobiliaria.value.trim() === "" ||
                editarAlturaInmobiliaria.value.trim() === ""
            ){
                mensajeEditarInmobiliaria.textContent = "Faltan completar campos";
                mensajeEditarInmobiliaria.className = "mensajeError";
                return;
            }

            const datos = {
                nombre: editarNombreInmobiliaria.value.trim(),
                email: editarEmailInmobiliaria.value.trim(),
                area_telefono: editarAreaInmobiliaria.value.trim(),
                telefono: editarTelefonoInmobiliaria.value.trim(),
                provincia: editarProvinciaInmobiliaria.options[editarProvinciaInmobiliaria.selectedIndex].text,
                ciudad: editarCiudadInmobiliaria.value,
                calle: editarCalleInmobiliaria.value.trim(),
                altura: editarAlturaInmobiliaria.value.trim()
            };

            try{
                mostrarCarga();

                const res = await fetch(
                    `${API_INMOBILIARIA}?id_usuario=${id_usuario}&id_inmobiliaria=${id_inmobiliaria}`,
                    {
                        method: "PUT",
                        headers: {
                            "Content-Type": "application/json",
                            "Authorization": `Bearer ${token}`
                        },
                        body: JSON.stringify(datos)
                    }
                );

                if(!validarSesionMenu(res)){
                    return;
                }

                const respuesta = await res.json();

                if(!res.ok){
                    mensajeEditarInmobiliaria.textContent = respuesta.error || respuesta.mensaje || "No se pudieron editar los datos";
                    mensajeEditarInmobiliaria.className = "mensajeError";
                    return;
                }

                cerrarEditarInmobiliaria();
                await cargarDatosInmobiliariaMenu();

                mostrarExito("Datos de la inmobiliaria actualizados correctamente");
            }catch(error){
                console.error(error);
                mensajeEditarInmobiliaria.textContent = "No se pudo conectar con el servidor";
                mensajeEditarInmobiliaria.className = "mensajeError";
            }finally{
                ocultarCarga();
            }
        });
    }

    /*ABRIR MIS DATOS*/
    async function abrirMisDatos(){
        try{
            mostrarCarga();

            const res = await fetch(
                `${API_USUARIO}?id_usuario=${id_usuario}`,
                {
                    method: "GET",
                    headers: {
                        "Authorization": `Bearer ${token}`
                    }
                }
            );

            if(!validarSesionMenu(res)){
                return;
            }

            const datos = await res.json();

            if(!res.ok){
                mostrarError(datos.error || datos.mensaje || "No se pudieron cargar los datos de la cuenta");
                return;
            }

            datoNombreUsuario.textContent = datos.nombre_usuario || "";
            datoApellidoUsuario.textContent = datos.apellido_usuario || "";
            datoEmailUsuario.textContent = datos.email || "";

            ventanaMisDatos.classList.remove("ocultarVentana");
        }catch(error){
            console.error(error);
            mostrarError("No se pudo conectar con el servidor");
        }finally{
            ocultarCarga();
        }
    }

    /*CERRAR MIS DATOS*/
    function cerrarMisDatos(){
        ventanaMisDatos.classList.add("ocultarVentana");
    }

    /*ABRIR EDITAR MIS DATOS*/
    function abrirEditarMisDatos(){
        editarNombreUsuario.value = datoNombreUsuario.textContent;
        editarApellidoUsuario.value = datoApellidoUsuario.textContent;
        editarEmailUsuario.value = datoEmailUsuario.textContent;

        ventanaMisDatos.classList.add("ocultarVentana");
        mensajeEditarUsuario.textContent = "";
        ventanaEditarMisDatos.classList.remove("ocultarVentana");
    }

    /*CERRAR EDITAR MIS DATOS*/
    function cerrarEditarMisDatos(){
        mensajeEditarUsuario.textContent = "";
        ventanaEditarMisDatos.classList.add("ocultarVentana");
    }

    /*GUARDAR MIS DATOS*/
    if(formEditarMisDatos){
        formEditarMisDatos.addEventListener("submit", async function(evento){
            evento.preventDefault();

            if(
                editarNombreUsuario.value.trim() === "" ||
                editarApellidoUsuario.value.trim() === "" ||
                editarEmailUsuario.value.trim() === ""
            ){
                mensajeEditarUsuario.textContent = "Faltan completar campos";
                mensajeEditarUsuario.className = "mensajeError";
                return;
            }

            const datosUsuario = {
                nombre_usuario: editarNombreUsuario.value.trim(),
                apellido_usuario: editarApellidoUsuario.value.trim(),
                email: editarEmailUsuario.value.trim()
            };

            try{
                mostrarCarga();

                const res = await fetch(
                    `${API_USUARIO}?id_usuario=${id_usuario}`,
                    {
                        method: "PUT",
                        headers: {
                            "Content-Type": "application/json",
                            "Authorization": `Bearer ${token}`
                        },
                        body: JSON.stringify(datosUsuario)
                    }
                );

                if(!validarSesionMenu(res)){
                    return;
                }

                const datos = await res.json();
                if(!res.ok){
                    mensajeEditarUsuario.textContent = datos.error || datos.mensaje || "No se pudieron editar los datos";
                    mensajeEditarUsuario.className = "mensajeError";
                    return;
                }

                cerrarEditarMisDatos();
                await abrirMisDatos();

                mostrarExito("Datos de la cuenta actualizados correctamente");
            }catch(error){
                console.error(error);
                mensajeEditarUsuario.textContent = "No se pudo conectar con el servidor";
                mensajeEditarUsuario.className = "mensajeError";
            }finally{
                ocultarCarga();
            }
        });
    }

    /*NAVEGACION*/
    if(btnPropiedades){
        btnPropiedades.addEventListener("click", function(){
            window.location.href = "inmobiliaria.html";
        });
    }

    if(btnAlquileres){
        btnAlquileres.addEventListener("click", function(){
            window.location.href = "alquileres.html";
        });
    }

    if(btnInquilinos){
        btnInquilinos.addEventListener("click", function(){
            window.location.href = "inquilino.html";
        });
    }

    if(btnVolverInmobiliarias){
        btnVolverInmobiliarias.addEventListener("click", function(){
            window.location.href = "inicioInmobiliaria.html";
        });
    }

    if(btnCambiarContrasenia){
        btnCambiarContrasenia.addEventListener("click", function(){
            window.location.href = "cambiarContrasenia.html";
        });
    }

    /*NAVEGACION MOBILE*/
    if(btnPropiedadesMobile){
        btnPropiedadesMobile.addEventListener("click", function(){
            window.location.href = "inmobiliaria.html";
        });
    }

    if(btnAlquileresMobile){
        btnAlquileresMobile.addEventListener("click", function(){
            window.location.href = "alquileres.html";
        });
    }

    if(btnInquilinosMobile){
        btnInquilinosMobile.addEventListener("click", function(){
            window.location.href = "inquilino.html";
        });
    }

    if(btnVolverMobile){
        btnVolverMobile.addEventListener("click", function(){
            window.location.href = "inicioInmobiliaria.html";
        });
    }

    if(btnCambiarContraseniaMobile){
        btnCambiarContraseniaMobile.addEventListener("click", function(){
            window.location.href = "cambiarContrasenia.html";
        });
    }

    /*MENU MOBILE*/
    if(btnMenuMobile){
        btnMenuMobile.addEventListener("click", function(){
            submenuMobile.classList.toggle("oculto");
        });
    }

    /*CERRAR SESION*/
    function cerrarSesion(){
        localStorage.clear();
        window.location.replace("index.html");
    }

    if(btnCerrarSesion){
        btnCerrarSesion.addEventListener("click", cerrarSesion);
    }

    if(btnCerrarSesionMobile){
        btnCerrarSesionMobile.addEventListener("click", cerrarSesion);
    }

    /*EVENTOS VER INMOBILIARIA*/
    if(btnVerInmobiliaria){
        btnVerInmobiliaria.addEventListener("click", abrirVerInmobiliaria);
    }

    if(btnCerrarVerInmobiliaria){
        btnCerrarVerInmobiliaria.addEventListener("click", cerrarVerInmobiliaria);
    }

    /*EVENTOS EDITAR INMOBILIARIA*/
    if(btnEditarInmobiliaria){
        btnEditarInmobiliaria.addEventListener("click", abrirEditarInmobiliaria);
    }

    if(btnEditarInmobiliariaMobile){
        btnEditarInmobiliariaMobile.addEventListener("click", function(){
            if(submenuMobile){
                submenuMobile.classList.add("oculto");
            }

            abrirEditarInmobiliaria();
        });
    }

    if(btnCerrarEditarInmobiliaria){
        btnCerrarEditarInmobiliaria.addEventListener("click", cerrarEditarInmobiliaria);
    }
    if(btnCancelarEditarInmobiliaria){
        btnCancelarEditarInmobiliaria.addEventListener("click", cerrarEditarInmobiliaria);
    }

    /*EVENTOS MIS DATOS*/
    if(btnMisDatos){
        btnMisDatos.addEventListener("click", abrirMisDatos);
    }

    if(btnMisDatosMobile){
        btnMisDatosMobile.addEventListener("click", function(){
            if(submenuMobile){
                submenuMobile.classList.add("oculto");
            }
            abrirMisDatos();
        });
    }

    if(btnCerrarMisDatos){
        btnCerrarMisDatos.addEventListener("click", cerrarMisDatos);
    }
    if(btnEditarMisDatos){
        btnEditarMisDatos.addEventListener("click", abrirEditarMisDatos);
    }
    if(btnCerrarEditarMisDatos){
        btnCerrarEditarMisDatos.addEventListener("click", cerrarEditarMisDatos);
    }
    if(btnCancelarEditarUsuario){
        btnCancelarEditarUsuario.addEventListener("click", cerrarEditarMisDatos);
    }
    /*CERRAR VENTANAS TOCANDO AFUERA*/
    if(ventanaVerInmobiliaria){
        ventanaVerInmobiliaria.addEventListener("click", function(evento){
            if(evento.target === ventanaVerInmobiliaria){
                cerrarVerInmobiliaria();
            }
        });
    }

    if(ventanaEditarInmobiliaria){
        ventanaEditarInmobiliaria.addEventListener("click", function(evento){
            if(evento.target === ventanaEditarInmobiliaria){
                cerrarEditarInmobiliaria();
            }
        });
    }

    if(ventanaMisDatos){
        ventanaMisDatos.addEventListener("click", function(evento){
            if(evento.target === ventanaMisDatos){
                cerrarMisDatos();
            }
        });
    }
    if(ventanaEditarMisDatos){
        ventanaEditarMisDatos.addEventListener("click", function(evento){
            if(evento.target === ventanaEditarMisDatos){
                cerrarEditarMisDatos();
            }
        });
    }
    /*CARGAR PROVINCIAS EDICION INMOBILIARIA*/
    if(editarProvinciaInmobiliaria && editarCiudadInmobiliaria){
        cargarProvincias(editarProvinciaInmobiliaria, editarCiudadInmobiliaria);

        editarProvinciaInmobiliaria.addEventListener("change", function(){
            cargarCiudades(editarProvinciaInmobiliaria.value, editarCiudadInmobiliaria);
        });
    }
    /*INICIAR*/
    cargarDatosInmobiliariaMenu();

})();
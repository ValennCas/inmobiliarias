// Agrupa las funciones y eventos del menu
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

    /*CAMBIAR CONTRASENIA*/
    const ventanaCambiarContrasenia = document.createElement("section");
    ventanaCambiarContrasenia.id = "ventanaCambiarContrasenia";
    ventanaCambiarContrasenia.className = "ventanaFlotante ocultarVentana";

    ventanaCambiarContrasenia.innerHTML = `
        <div class="contenidoVentana">
            <button type="button" class="cerrarVentana" id="btnCerrarCambiarContrasenia">×</button>

            <h2>Cambiar contraseña</h2>

            <form id="formCambiarContrasenia">
                <label for="passwordActual">Contraseña actual</label>
                <div class="campoPassword">
                    <input type="password" id="passwordActual" maxlength="100" required>
                    <button type="button" class="btnMostrarPassword">🔒</button>
                </div>

                <label for="passwordNueva">Nueva contraseña</label>
                <div class="campoPassword">
                    <input type="password" id="passwordNueva" minlength="6" maxlength="100" required>
                    <button type="button" class="btnMostrarPassword">🔒</button>
                </div>

                <label for="repetirPasswordNueva">Repetir nueva contraseña</label>
                <div class="campoPassword">
                    <input type="password" id="repetirPasswordNueva" minlength="6" maxlength="100" required>
                    <button type="button" class="btnMostrarPassword">🔒</button>
                </div>

                <p id="mensajeCambiarContrasenia"></p>

                <div class="botonesFormulario">
                    <button type="submit" id="btnGuardarNuevaContrasenia">Cambiar contraseña</button>
                    <button type="button" id="btnCancelarCambiarContrasenia">Cancelar</button>
                </div>
            </form>
        </div>
    `;

    document.body.appendChild(ventanaCambiarContrasenia);

    /*VARIABLES */ 
    const btnCerrarCambiarContrasenia = document.querySelector("#btnCerrarCambiarContrasenia");
    const btnCancelarCambiarContrasenia = document.querySelector("#btnCancelarCambiarContrasenia");
    const formCambiarContrasenia = document.querySelector("#formCambiarContrasenia");
    const passwordActual = document.querySelector("#passwordActual");
    const passwordNueva = document.querySelector("#passwordNueva");
    const repetirPasswordNueva = document.querySelector("#repetirPasswordNueva");
    const mensajeCambiarContrasenia = document.querySelector("#mensajeCambiarContrasenia");
    const btnGuardarNuevaContrasenia = document.querySelector("#btnGuardarNuevaContrasenia");

    /*ABRIR CAMBIAR CONTRASENIA*/
    function abrirCambiarContrasenia(){
        formCambiarContrasenia.reset();
        mensajeCambiarContrasenia.textContent = "";
        ventanaCambiarContrasenia.classList.remove("ocultarVentana");
    }

    // Recorre los elementos para agregar su comportamiento
    ventanaCambiarContrasenia.querySelectorAll(".btnMostrarPassword").forEach(function(boton){
        // Maneja el click de boton
        boton.addEventListener("click", function(){
            const input = boton.previousElementSibling;

            if(input.type === "password"){
                input.type = "text";
                boton.textContent = "🔓";
            }else{
                input.type = "password";
                boton.textContent = "🔒";
            }
        });
    });

    /*CAMBIAR CONTRASENIA*/
    if(formCambiarContrasenia){
        // Maneja el envio de form cambiar contrasenia
        formCambiarContrasenia.addEventListener("submit", async function(evento){
            evento.preventDefault();

            if(passwordActual.value.trim() === "" || passwordNueva.value.trim() === "" || repetirPasswordNueva.value.trim() === ""){
                mensajeCambiarContrasenia.textContent = "Faltan completar campos";
                mensajeCambiarContrasenia.className = "mensajeError";
                return;
            }

            const datos = {
                passwordActual: passwordActual.value,
                passwordNueva: passwordNueva.value,
                repetirPasswordNueva: repetirPasswordNueva.value
            };

            try{
                // Muestra la pantalla de carga
                mostrarCarga();
                btnGuardarNuevaContrasenia.disabled = true;

                const res = await fetch(API_CAMBIAR_CONTRA_LOGGEADO, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Authorization": `Bearer ${token}`
                    },
                    body: JSON.stringify(datos)
                });

                if(!validarSesionMenu(res)){
                    return;
                }

                const respuesta = await res.json();

                if(!res.ok){
                    mensajeCambiarContrasenia.textContent = respuesta.error || respuesta.mensaje || "No se pudo cambiar la contraseña";
                    mensajeCambiarContrasenia.className = "mensajeError";
                    return;
                }

                cerrarCambiarContrasenia();
                // Muestra el mensaje de operacion exitosa
                mostrarExito("Contraseña actualizada correctamente");
            }catch(error){
                console.error(error);
                mensajeCambiarContrasenia.textContent = "No se pudo conectar con el servidor";
                mensajeCambiarContrasenia.className = "mensajeError";
            }finally{
                btnGuardarNuevaContrasenia.disabled = false;
                ocultarCarga();
            }
        });
    }
    /*CERRAR CAMBIAR CONTRASENIA*/
    function cerrarCambiarContrasenia(){
        formCambiarContrasenia.reset();
        mensajeCambiarContrasenia.textContent = "";
        ventanaCambiarContrasenia.classList.add("ocultarVentana");
    }

    if(btnCancelarCambiarContrasenia){
        // Maneja el click de btn cancelar cambiar contrasenia
        btnCancelarCambiarContrasenia.addEventListener("click", cerrarCambiarContrasenia);
    }

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
            // Muestra la pantalla de carga
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
                // Muestra un mensaje de error
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
            // Muestra un mensaje de error
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
            // Muestra la pantalla de carga
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
                // Muestra un mensaje de error
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
            // Muestra un mensaje de error
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
        // Maneja el envio de form editar inmobiliaria
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
                // Muestra la pantalla de carga
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

                // Muestra el mensaje de operacion exitosa
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
            // Muestra la pantalla de carga
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
                // Muestra un mensaje de error
                mostrarError(datos.error || datos.mensaje || "No se pudieron cargar los datos de la cuenta");
                return;
            }

            datoNombreUsuario.textContent = datos.nombre_usuario || "";
            datoApellidoUsuario.textContent = datos.apellido_usuario || "";
            datoEmailUsuario.textContent = datos.email || "";

            ventanaMisDatos.classList.remove("ocultarVentana");
        }catch(error){
            console.error(error);
            // Muestra un mensaje de error
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
        // Maneja el envio de form editar mis datos
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
                // Muestra la pantalla de carga
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

                // Muestra el mensaje de operacion exitosa
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
        // Maneja el click de btn propiedades
        btnPropiedades.addEventListener("click", function(){
            window.location.href = "inmobiliaria.html";
        });
    }

    if(btnAlquileres){
        // Maneja el click de btn alquileres
        btnAlquileres.addEventListener("click", function(){
            window.location.href = "alquileres.html";
        });
    }

    if(btnInquilinos){
        // Maneja el click de btn inquilinos
        btnInquilinos.addEventListener("click", function(){
            window.location.href = "inquilino.html";
        });
    }

    if(btnVolverInmobiliarias){
        // Maneja el click de btn volver inmobiliarias
        btnVolverInmobiliarias.addEventListener("click", function(){
            window.location.href = "inicioInmobiliaria.html";
        });
    }

    if(btnCambiarContrasenia){
        // Maneja el click de btn cambiar contrasenia
        btnCambiarContrasenia.addEventListener("click", abrirCambiarContrasenia);
    }

    /*NAVEGACION MOBILE*/
    if(btnPropiedadesMobile){
        // Maneja el click de btn propiedades mobile
        btnPropiedadesMobile.addEventListener("click", function(){
            window.location.href = "inmobiliaria.html";
        });
    }

    if(btnAlquileresMobile){
        // Maneja el click de btn alquileres mobile
        btnAlquileresMobile.addEventListener("click", function(){
            window.location.href = "alquileres.html";
        });
    }

    if(btnInquilinosMobile){
        // Maneja el click de btn inquilinos mobile
        btnInquilinosMobile.addEventListener("click", function(){
            window.location.href = "inquilino.html";
        });
    }

    if(btnVolverMobile){
        // Maneja el click de btn volver mobile
        btnVolverMobile.addEventListener("click", function(){
            window.location.href = "inicioInmobiliaria.html";
        });
    }

    if(btnCambiarContraseniaMobile){
        // Maneja el click de btn cambiar contrasenia mobile
        btnCambiarContraseniaMobile.addEventListener("click", function(){
            if(submenuMobile){
                submenuMobile.classList.add("oculto");
            }
            abrirCambiarContrasenia();
        });
    }

    /*MENU MOBILE*/
    if(btnMenuMobile){
        // Maneja el click de btn menu mobile
        btnMenuMobile.addEventListener("click", function(){
            submenuMobile.classList.toggle("oculto");
        });
    }

    /*CERRAR CAMBIAR CONTRASEÑA*/
    if(btnCerrarCambiarContrasenia){
        // Maneja el click de btn cerrar cambiar contrasenia
        btnCerrarCambiarContrasenia.addEventListener("click", cerrarCambiarContrasenia);
    }

    /*CERRAR VENTANA DE CONTRASEÑA TOCANDO AFUERA*/
    if(ventanaCambiarContrasenia){
        // Maneja el click de ventana cambiar contrasenia
        ventanaCambiarContrasenia.addEventListener("click", function(evento){
            if(evento.target === ventanaCambiarContrasenia){
                cerrarCambiarContrasenia();
            }
        });
    }

    /*CERRAR SESION*/
    function cerrarSesion(){
        localStorage.clear();
        window.location.replace("index.html");
    }

    if(btnCerrarSesion){
        // Maneja el click de btn cerrar sesion
        btnCerrarSesion.addEventListener("click", cerrarSesion);
    }

    if(btnCerrarSesionMobile){
        // Maneja el click de btn cerrar sesion mobile
        btnCerrarSesionMobile.addEventListener("click", cerrarSesion);
    }

    /*EVENTOS VER INMOBILIARIA*/
    if(btnVerInmobiliaria){
        // Maneja el click de btn ver inmobiliaria
        btnVerInmobiliaria.addEventListener("click", abrirVerInmobiliaria);
    }

    if(btnCerrarVerInmobiliaria){
        // Maneja el click de btn cerrar ver inmobiliaria
        btnCerrarVerInmobiliaria.addEventListener("click", cerrarVerInmobiliaria);
    }

    /*EVENTOS EDITAR INMOBILIARIA*/
    if(btnEditarInmobiliaria){
        // Maneja el click de btn editar inmobiliaria
        btnEditarInmobiliaria.addEventListener("click", abrirEditarInmobiliaria);
    }

    if(btnEditarInmobiliariaMobile){
        // Maneja el click de btn editar inmobiliaria mobile
        btnEditarInmobiliariaMobile.addEventListener("click", function(){
            if(submenuMobile){
                submenuMobile.classList.add("oculto");
            }

            abrirEditarInmobiliaria();
        });
    }

    if(btnCerrarEditarInmobiliaria){
        // Maneja el click de btn cerrar editar inmobiliaria
        btnCerrarEditarInmobiliaria.addEventListener("click", cerrarEditarInmobiliaria);
    }
    if(btnCancelarEditarInmobiliaria){
        // Maneja el click de btn cancelar editar inmobiliaria
        btnCancelarEditarInmobiliaria.addEventListener("click", cerrarEditarInmobiliaria);
    }

    /*EVENTOS MIS DATOS*/
    if(btnMisDatos){
        // Maneja el click de btn mis datos
        btnMisDatos.addEventListener("click", abrirMisDatos);
    }

    if(btnMisDatosMobile){
        // Maneja el click de btn mis datos mobile
        btnMisDatosMobile.addEventListener("click", function(){
            if(submenuMobile){
                submenuMobile.classList.add("oculto");
            }
            abrirMisDatos();
        });
    }

    if(btnCerrarMisDatos){
        // Maneja el click de btn cerrar mis datos
        btnCerrarMisDatos.addEventListener("click", cerrarMisDatos);
    }
    if(btnEditarMisDatos){
        // Maneja el click de btn editar mis datos
        btnEditarMisDatos.addEventListener("click", abrirEditarMisDatos);
    }
    if(btnCerrarEditarMisDatos){
        // Maneja el click de btn cerrar editar mis datos
        btnCerrarEditarMisDatos.addEventListener("click", cerrarEditarMisDatos);
    }
    if(btnCancelarEditarUsuario){
        // Maneja el click de btn cancelar editar usuario
        btnCancelarEditarUsuario.addEventListener("click", cerrarEditarMisDatos);
    }
    /*CERRAR VENTANAS TOCANDO AFUERA*/
    if(ventanaVerInmobiliaria){
        // Maneja el click de ventana ver inmobiliaria
        ventanaVerInmobiliaria.addEventListener("click", function(evento){
            if(evento.target === ventanaVerInmobiliaria){
                cerrarVerInmobiliaria();
            }
        });
    }

    if(ventanaEditarInmobiliaria){
        // Maneja el click de ventana editar inmobiliaria
        ventanaEditarInmobiliaria.addEventListener("click", function(evento){
            if(evento.target === ventanaEditarInmobiliaria){
                cerrarEditarInmobiliaria();
            }
        });
    }

    if(ventanaMisDatos){
        // Maneja el click de ventana mis datos
        ventanaMisDatos.addEventListener("click", function(evento){
            if(evento.target === ventanaMisDatos){
                cerrarMisDatos();
            }
        });
    }
    if(ventanaEditarMisDatos){
        // Maneja el click de ventana editar mis datos
        ventanaEditarMisDatos.addEventListener("click", function(evento){
            if(evento.target === ventanaEditarMisDatos){
                cerrarEditarMisDatos();
            }
        });
    }
    /*CARGAR PROVINCIAS EDICION INMOBILIARIA*/
    if(editarProvinciaInmobiliaria && editarCiudadInmobiliaria){
        // Carga las provincias disponibles
        cargarProvincias(editarProvinciaInmobiliaria, editarCiudadInmobiliaria);

        // Maneja el cambio de editar provincia inmobiliaria
        editarProvinciaInmobiliaria.addEventListener("change", function(){
            // Carga las ciudades de la provincia seleccionada
            cargarCiudades(editarProvinciaInmobiliaria.value, editarCiudadInmobiliaria);
        });
    }
    
    /*INICIAR*/
    cargarDatosInmobiliariaMenu();

})();
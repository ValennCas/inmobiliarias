const API_PROPIEDAD = "http://localhost/inmobiliarias/backend/controller/propiedadesController.php";
const API_INQUILINO = "http://localhost/inmobiliarias/backend/controller/inquilinosController.php";
const API_ALQUILER = "http://localhost/inmobiliarias/backend/controller/alquileresController.php";

/*
    MENU, DATOS DE INMOBILIARIA, MIS DATOS Y EDITAR INMOBILIARIA
    SE MANEJAN DESDE:
    menuInmobiliaria.js
*/

/*DATOS DE SESION*/
const token = localStorage.getItem("token");
const id_usuario = localStorage.getItem("id_usuario");
const id_inmobiliaria = localStorage.getItem("id_inmobiliaria");

/*VALIDAR SESION*/
if(!token || !id_usuario || !id_inmobiliaria){
    window.location.replace("index.html");
}

/*VALIDAR SI SE VOLVIO CON LAS FLECHAS*/
window.addEventListener("pageshow", function(evento){
    const navegacion = performance.getEntriesByType("navigation")[0];

    if(evento.persisted || navegacion?.type === "back_forward"){
        localStorage.clear();
        window.location.replace("index.html");
        return;
    }

    const tokenActual = localStorage.getItem("token");
    const usuarioActual = localStorage.getItem("id_usuario");

    if(!tokenActual || !usuarioActual){
        window.location.replace("index.html");
    }
});

/*LISTADO DE INQUILINOS*/
const btnAgregarInquilino = document.querySelector("#btnAgregarInquilino");
const listaInquilinos = document.querySelector("#listaInquilinos");
const sinInquilinos = document.querySelector("#sinInquilinos");

/*AGREGAR INQUILINO*/
const ventanaAgregarInquilino = document.querySelector("#ventanaAgregarInquilino");
const btnCerrarAgregarInquilino = document.querySelector("#btnCerrarAgregarInquilino");
const btnCancelarAgregarInquilino = document.querySelector("#btnCancelarAgregarInquilino");
const formAgregarInquilino = document.querySelector("#formAgregarInquilino");
const nombreInquilino = document.querySelector("#nombreInquilino");
const apellidoInquilino = document.querySelector("#apellidoInquilino");
const emailInquilino = document.querySelector("#emailInquilino");
const areaTelefonoInquilino = document.querySelector("#areaTelefonoInquilino");
const telefonoInquilino = document.querySelector("#telefonoInquilino");
const propiedadInquilino = document.querySelector("#propiedadInquilino");
const precioAlquiler = document.querySelector("#precioAlquiler");
const fechaInicioAlquiler = document.querySelector("#fechaInicioAlquiler");
const fechaFinAlquiler = document.querySelector("#fechaFinAlquiler");
const mensajeAgregarInquilino = document.querySelector("#mensajeAgregarInquilino");
const btnGuardarInquilino = document.querySelector("#btnGuardarInquilino");

/*VER INQUILINO*/
const ventanaVerInquilino = document.querySelector("#ventanaVerInquilino");
const btnCerrarVerInquilino = document.querySelector("#btnCerrarVerInquilino");
const verNombreInquilino = document.querySelector("#verNombreInquilino");
const verApellidoInquilino = document.querySelector("#verApellidoInquilino");
const verEmailInquilino = document.querySelector("#verEmailInquilino");
const verTelefonoInquilino = document.querySelector("#verTelefonoInquilino");
const verTituloPropiedadInquilino = document.querySelector("#verTituloPropiedadInquilino");
const verDireccionPropiedadInquilino = document.querySelector("#verDireccionPropiedadInquilino");
const verCiudadPropiedadInquilino = document.querySelector("#verCiudadPropiedadInquilino");
const verProvinciaPropiedadInquilino = document.querySelector("#verProvinciaPropiedadInquilino");
const verPrecioPropiedadInquilino = document.querySelector("#verPrecioPropiedadInquilino");
const verFechaInicioInquilino = document.querySelector("#verFechaInicioInquilino");
const verFechaFinInquilino = document.querySelector("#verFechaFinInquilino");

/*EDITAR INQUILINO*/
const ventanaEditarInquilino = document.querySelector("#ventanaEditarInquilino");
const btnCerrarEditarInquilino = document.querySelector("#btnCerrarEditarInquilino");
const btnCancelarEditarInquilino = document.querySelector("#btnCancelarEditarInquilino");
const formEditarInquilino = document.querySelector("#formEditarInquilino");
const editarIdInquilino = document.querySelector("#editarIdInquilino");
const editarNombreInquilino = document.querySelector("#editarNombreInquilino");
const editarApellidoInquilino = document.querySelector("#editarApellidoInquilino");
const editarEmailInquilino = document.querySelector("#editarEmailInquilino");
const editarAreaTelefonoInquilino = document.querySelector("#editarAreaTelefonoInquilino");
const editarTelefonoInquilino = document.querySelector("#editarTelefonoInquilino");
const mensajeEditarInquilino = document.querySelector("#mensajeEditarInquilino");
const btnGuardarCambiosInquilino = document.querySelector("#btnGuardarCambiosInquilino");

/*ELIMINAR INQUILINO*/
const ventanaEliminarInquilino = document.querySelector("#ventanaEliminarInquilino");
const idInquilinoEliminar = document.querySelector("#idInquilinoEliminar");
const btnConfirmarEliminarInquilino = document.querySelector("#btnConfirmarEliminarInquilino");
const btnCancelarEliminarInquilino = document.querySelector("#btnCancelarEliminarInquilino");

/*VALIDAR RESPUESTA DE SESION*/
function validarSesionRespuesta(res){
    if(res.status === 401){
        localStorage.clear();
        window.location.replace("index.html");
        return false;
    }

    return true;
}

/*FORMATEAR PRECIO*/
function formatearPrecio(precio){
    if(precio === null || precio === undefined || precio === ""){
        return "";
    }

    return Number(precio).toLocaleString("es-AR");
}

/*FORMATEAR FECHA*/
function formatearFecha(fecha){
    if(!fecha){
        return "";
    }

    const partes = fecha.split("-");

    if(partes.length !== 3){
        return fecha;
    }

    return `${partes[2]}/${partes[1]}/${partes[0]}`;
}

/*VALIDAR FECHAS DEL ALQUILER*/
fechaInicioAlquiler.addEventListener("change", function(){
    fechaFinAlquiler.min = fechaInicioAlquiler.value;

    if(fechaFinAlquiler.value !== "" && fechaFinAlquiler.value <= fechaInicioAlquiler.value){
        fechaFinAlquiler.value = "";
        mensajeAgregarInquilino.textContent = "La fecha de finalización debe ser posterior a la fecha de inicio";
        mensajeAgregarInquilino.className = "mensajeError";
    }else{
        mensajeAgregarInquilino.textContent = "";
    }
});

/*MOSTRAR CUANDO NO HAY INQUILINOS*/
function mostrarSinInquilinos(){
    listaInquilinos.innerHTML = "";
    sinInquilinos.classList.remove("oculto");
}

/*MOSTRAR ERROR EN LISTADO*/
function mostrarErrorInquilinos(mensaje){
    listaInquilinos.innerHTML = "";
    sinInquilinos.classList.remove("oculto");
    sinInquilinos.innerHTML = `<p class="mensajeError">${mensaje}</p>`;
}

/*CARGAR INQUILINOS*/
async function cargarInquilinos(){
    try{
        mostrarCarga();

        const res = await fetch(
            `${API_INQUILINO}?id_usuario=${id_usuario}&id_inmobiliaria=${id_inmobiliaria}`,
            {
                method: "GET",
                headers: {
                    "Authorization": `Bearer ${token}`
                }
            }
        );

        if(!validarSesionRespuesta(res)){
            return;
        }

        const datos = await res.json();

        if(res.status === 404){
            mostrarSinInquilinos();
            return;
        }

        if(!res.ok){
            mostrarErrorInquilinos(datos.error || datos.mensaje || "No se pudieron cargar los inquilinos");
            return;
        }

        mostrarInquilinos(datos);
    }catch(error){
        console.error(error);
        mostrarErrorInquilinos("No se pudo conectar con el servidor");
    }finally{
        ocultarCarga();
    }
}

/*MOSTRAR INQUILINOS*/
function mostrarInquilinos(datos){
    listaInquilinos.innerHTML = "";

    let inquilinos = [];

    if(Array.isArray(datos)){
        inquilinos = datos;
    }else if(Array.isArray(datos.inquilinos)){
        inquilinos = datos.inquilinos;
    }else if(datos.inquilino){
        inquilinos = [datos.inquilino];
    }else if(datos.id_inquilino){
        inquilinos = [datos];
    }

    if(inquilinos.length === 0){
        mostrarSinInquilinos();
        return;
    }

    sinInquilinos.classList.add("oculto");

    inquilinos.forEach(function(inquilino){
        const tarjeta = document.createElement("article");
        tarjeta.classList.add("tarjetaInquilino");

        tarjeta.innerHTML = `
            <div class="contenidoInquilino">
                <h3>${inquilino.nombre || ""} ${inquilino.apellido || ""}</h3>
                <p>${inquilino.email || ""}</p>
                <p>${inquilino.area_telefono || ""} ${inquilino.telefono || ""}</p>
                <p>${inquilino.titulo_propiedad ? "Propiedad: " + inquilino.titulo_propiedad : ""}</p>

                <div class="accionesInquilino">
                    <button type="button" class="btnVerInquilino" data-id="${inquilino.id_inquilino}">Visualizar</button>
                    <button type="button" class="btnEditarInquilino" data-id="${inquilino.id_inquilino}">Editar</button>
                    <button type="button" class="btnEliminarInquilino" data-id="${inquilino.id_inquilino}">Eliminar</button>
                </div>
            </div>
        `;

        listaInquilinos.appendChild(tarjeta);
    });

    agregarEventosInquilinos();
}

/*EVENTOS DE TARJETAS*/
function agregarEventosInquilinos(){
    const botonesVer = document.querySelectorAll(".btnVerInquilino");
    const botonesEditar = document.querySelectorAll(".btnEditarInquilino");
    const botonesEliminar = document.querySelectorAll(".btnEliminarInquilino");

    botonesVer.forEach(function(boton){
        boton.addEventListener("click", function(){
            verInquilino(boton.dataset.id);
        });
    });

    botonesEditar.forEach(function(boton){
        boton.addEventListener("click", function(){
            cargarInquilinoEditar(boton.dataset.id);
        });
    });

    botonesEliminar.forEach(function(boton){
        boton.addEventListener("click", function(){
            idInquilinoEliminar.value = boton.dataset.id;
            ventanaEliminarInquilino.classList.remove("ocultarVentana");
        });
    });
}

/*CARGAR PROPIEDADES DISPONIBLES PARA ALQUILER*/
async function cargarPropiedadesDisponibles(){
    propiedadInquilino.innerHTML = `<option value="">Seleccionar propiedad</option>`;

    try{
        mostrarCarga();

        const res = await fetch(
            `${API_PROPIEDAD}?id_usuario=${id_usuario}&id_inmobiliaria=${id_inmobiliaria}`,
            {
                method: "GET",
                headers: {
                    "Authorization": `Bearer ${token}`
                }
            }
        );

        if(!validarSesionRespuesta(res)){
            return;
        }

        const datos = await res.json();

        if(!res.ok){
            mensajeAgregarInquilino.textContent = datos.error || datos.mensaje || "No se pudieron cargar las propiedades";
            mensajeAgregarInquilino.className = "mensajeError";
            return;
        }

        let propiedades = [];

        if(Array.isArray(datos)){
            propiedades = datos;
        }else if(Array.isArray(datos.propiedades)){
            propiedades = datos.propiedades;
        }

        /*SOLO PROPIEDADES EN ALQUILER*/
        propiedades = propiedades.filter(function(propiedad){
            return (propiedad.estado || "").trim().toUpperCase() === "EN ALQUILER";
        });

        propiedades.forEach(function(propiedad){
            const opcion = document.createElement("option");
            opcion.value = propiedad.id_propiedad;
            opcion.textContent = `${propiedad.titulo || "Propiedad"} - ${propiedad.calle || ""} ${propiedad.altura || ""}`;
            propiedadInquilino.appendChild(opcion);
        });

        if(propiedades.length === 0){
            const opcion = document.createElement("option");
            opcion.value = "";
            opcion.textContent = "No hay propiedades disponibles";
            propiedadInquilino.appendChild(opcion);
        }
    }catch(error){
        console.error(error);
        mensajeAgregarInquilino.textContent = "No se pudieron cargar las propiedades";
        mensajeAgregarInquilino.className = "mensajeError";
    }finally{
        ocultarCarga();
    }
}

/*ABRIR CREAR INQUILINO*/
btnAgregarInquilino.addEventListener("click", async function(){
    mensajeAgregarInquilino.textContent = "";
    formAgregarInquilino.reset();
    ventanaAgregarInquilino.classList.remove("ocultarVentana");

    await cargarPropiedadesDisponibles();
});

/*CERRAR CREAR INQUILINO*/
function cerrarAgregarInquilino(){
    formAgregarInquilino.reset();
    propiedadInquilino.innerHTML = `<option value="">Seleccionar propiedad</option>`;
    mensajeAgregarInquilino.textContent = "";
    ventanaAgregarInquilino.classList.add("ocultarVentana");
}

btnCerrarAgregarInquilino.addEventListener("click", cerrarAgregarInquilino);
btnCancelarAgregarInquilino.addEventListener("click", cerrarAgregarInquilino);

/*CREAR INQUILINO Y ALQUILER*/
formAgregarInquilino.addEventListener("submit", async function(evento){
    evento.preventDefault();

    if(nombreInquilino.value.trim() === "" || apellidoInquilino.value.trim() === "" || emailInquilino.value.trim() === "" || areaTelefonoInquilino.value.trim() === "" || telefonoInquilino.value.trim() === "" || propiedadInquilino.value === "" || precioAlquiler.value === "" || fechaInicioAlquiler.value === "" || fechaFinAlquiler.value === ""){
        mensajeAgregarInquilino.textContent = "Faltan completar campos";
        mensajeAgregarInquilino.className = "mensajeError";
        return;
    }

    if(fechaFinAlquiler.value <= fechaInicioAlquiler.value){
        mensajeAgregarInquilino.textContent = "La fecha de finalización debe ser posterior a la fecha de inicio";
        mensajeAgregarInquilino.className = "mensajeError";
        return;
    }

    const datosInquilino = {
        nombre: nombreInquilino.value.trim(),
        apellido: apellidoInquilino.value.trim(),
        email: emailInquilino.value.trim(),
        area_telefono: areaTelefonoInquilino.value.trim(),
        telefono: telefonoInquilino.value.trim()
    };

    try{
        mostrarCarga();
        btnGuardarInquilino.disabled = true;

        /*CREAMOS EL INQUILINO*/
        const resInquilino = await fetch(
            `${API_INQUILINO}?id_usuario=${id_usuario}&id_inmobiliaria=${id_inmobiliaria}`,
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Authorization": `Bearer ${token}`
                },
                body: JSON.stringify(datosInquilino)
            }
        );

        if(!validarSesionRespuesta(resInquilino)){
            return;
        }

        const respuestaInquilino = await resInquilino.json();

        if(!resInquilino.ok){
            mensajeAgregarInquilino.textContent = respuestaInquilino.error || respuestaInquilino.mensaje || "No se pudo registrar el inquilino";
            mensajeAgregarInquilino.className = "mensajeError";
            return;
        }

        const inquilino = respuestaInquilino.inquilino || respuestaInquilino;
        const id_inquilino = inquilino.id_inquilino;

        /*DATOS DEL ALQUILER*/
        const datosAlquiler = {
            id_inmobiliaria: id_inmobiliaria,
            id_propiedad: propiedadInquilino.value,
            id_inquilino: id_inquilino,
            precio: precioAlquiler.value,
            fecha_inicio: fechaInicioAlquiler.value,
            fecha_fin: fechaFinAlquiler.value
        };

        /*CREAMOS EL ALQUILER*/
        const resAlquiler = await fetch(
            `${API_ALQUILER}?id_usuario=${id_usuario}&id_inmobiliaria=${id_inmobiliaria}`,
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Authorization": `Bearer ${token}`
                },
                body: JSON.stringify(datosAlquiler)
            }
        );

        if(!validarSesionRespuesta(resAlquiler)){
            return;
        }

        const respuestaAlquiler = await resAlquiler.json();

        if(!resAlquiler.ok){
            mensajeAgregarInquilino.textContent = respuestaAlquiler.error || respuestaAlquiler.mensaje || "Se creó el inquilino pero no se pudo crear el alquiler";
            mensajeAgregarInquilino.className = "mensajeError";
            return;
        }

        cerrarAgregarInquilino();
        await cargarInquilinos();
        mostrarExito("Inquilino y alquiler registrados correctamente");
    }catch(error){
        console.error(error);
        mensajeAgregarInquilino.textContent = "No se pudo conectar con el servidor";
        mensajeAgregarInquilino.className = "mensajeError";
    }finally{
        btnGuardarInquilino.disabled = false;
        ocultarCarga();
    }
});

/*VER INQUILINO*/
async function verInquilino(id_inquilino){
    try{
        mostrarCarga();

        /*DATOS DEL INQUILINO*/
        const res = await fetch(
            `${API_INQUILINO}?id_usuario=${id_usuario}&id_inmobiliaria=${id_inmobiliaria}&id_inquilino=${id_inquilino}`,
            {
                method: "GET",
                headers: {
                    "Authorization": `Bearer ${token}`
                }
            }
        );

        if(!validarSesionRespuesta(res)){
            return;
        }

        const datos = await res.json();

        if(!res.ok){
            mostrarError(datos.error || datos.mensaje || "No se pudo cargar el inquilino");
            return;
        }

        const inquilino = datos.inquilino || datos;

        /*DATOS DEL ALQUILER Y LA PROPIEDAD*/
        const resAlquiler = await fetch(
            `${API_ALQUILER}?id_usuario=${id_usuario}&id_inquilino=${id_inquilino}&id_inmobiliaria=${id_inmobiliaria}`,
            {
                method: "GET",
                headers: {
                    "Authorization": `Bearer ${token}`
                }
            }
        );

        if(!validarSesionRespuesta(resAlquiler)){
            return;
        }

        const datosAlquiler = await resAlquiler.json();

        if(!resAlquiler.ok){
            mostrarError(datosAlquiler.error || datosAlquiler.mensaje || "No se pudo cargar el alquiler");
            return;
        }

        let alquiler = null;

        if(Array.isArray(datosAlquiler) && datosAlquiler.length > 0){
            alquiler = datosAlquiler[0];
        }else if(datosAlquiler.alquiler){
            alquiler = datosAlquiler.alquiler;
        }else if(datosAlquiler.id_alquiler){
            alquiler = datosAlquiler;
        }

        /*DATOS DEL INQUILINO*/
        verNombreInquilino.textContent = inquilino.nombre || "";
        verApellidoInquilino.textContent = inquilino.apellido || "";
        verEmailInquilino.textContent = inquilino.email || "";
        verTelefonoInquilino.textContent = `${inquilino.area_telefono || ""} ${inquilino.telefono || ""}`.trim();

        /*DATOS DE LA PROPIEDAD Y ALQUILER*/
        if(alquiler){
            verTituloPropiedadInquilino.textContent = alquiler.titulo || "";
            verDireccionPropiedadInquilino.textContent = `${alquiler.calle || ""} ${alquiler.altura || ""}`;
            verCiudadPropiedadInquilino.textContent = alquiler.ciudad || "";
            verProvinciaPropiedadInquilino.textContent = alquiler.provincia || "";
            verPrecioPropiedadInquilino.textContent = formatearPrecio(alquiler.precio);
            verFechaInicioInquilino.textContent = formatearFecha(alquiler.fecha_inicio);
            verFechaFinInquilino.textContent = formatearFecha(alquiler.fecha_fin);
        }else{
            verTituloPropiedadInquilino.textContent = "";
            verDireccionPropiedadInquilino.textContent = "";
            verCiudadPropiedadInquilino.textContent = "";
            verProvinciaPropiedadInquilino.textContent = "";
            verPrecioPropiedadInquilino.textContent = "";
            verFechaInicioInquilino.textContent = "";
            verFechaFinInquilino.textContent = "";
        }

        ventanaVerInquilino.classList.remove("ocultarVentana");
    }catch(error){
        console.error(error);
        mostrarError("No se pudo conectar con el servidor");
    }finally{
        ocultarCarga();
    }
}

/*CERRAR VER INQUILINO*/
btnCerrarVerInquilino.addEventListener("click", function(){
    ventanaVerInquilino.classList.add("ocultarVentana");
});

/*CARGAR INQUILINO PARA EDITAR*/
async function cargarInquilinoEditar(id_inquilino){
    try{
        mostrarCarga();

        const res = await fetch(
            `${API_INQUILINO}?id_usuario=${id_usuario}&id_inmobiliaria=${id_inmobiliaria}&id_inquilino=${id_inquilino}`,
            {
                method: "GET",
                headers: {
                    "Authorization": `Bearer ${token}`
                }
            }
        );

        if(!validarSesionRespuesta(res)){
            return;
        }

        const datos = await res.json();

        if(!res.ok){
            mostrarError(datos.error || datos.mensaje || "No se pudo cargar el inquilino");
            return;
        }

        const inquilino = datos.inquilino || datos;

        editarIdInquilino.value = inquilino.id_inquilino;
        editarNombreInquilino.value = inquilino.nombre || "";
        editarApellidoInquilino.value = inquilino.apellido || "";
        editarEmailInquilino.value = inquilino.email || "";
        editarAreaTelefonoInquilino.value = inquilino.area_telefono || "";
        editarTelefonoInquilino.value = inquilino.telefono || "";
        mensajeEditarInquilino.textContent = "";

        ventanaEditarInquilino.classList.remove("ocultarVentana");
    }catch(error){
        console.error(error);
        mostrarError("No se pudo conectar con el servidor");
    }finally{
        ocultarCarga();
    }
}

/*CERRAR EDITAR INQUILINO*/
function cerrarEditarInquilino(){
    formEditarInquilino.reset();
    editarIdInquilino.value = "";
    mensajeEditarInquilino.textContent = "";
    ventanaEditarInquilino.classList.add("ocultarVentana");
}

btnCerrarEditarInquilino.addEventListener("click", cerrarEditarInquilino);
btnCancelarEditarInquilino.addEventListener("click", cerrarEditarInquilino);

/*EDITAR INQUILINO*/
formEditarInquilino.addEventListener("submit", async function(evento){
    evento.preventDefault();

    if(editarNombreInquilino.value.trim() === "" || editarApellidoInquilino.value.trim() === "" || editarEmailInquilino.value.trim() === "" || editarAreaTelefonoInquilino.value.trim() === "" || editarTelefonoInquilino.value.trim() === ""){
        mensajeEditarInquilino.textContent = "Faltan completar campos";
        mensajeEditarInquilino.className = "mensajeError";
        return;
    }

    const datos = {
        nombre: editarNombreInquilino.value.trim(),
        apellido: editarApellidoInquilino.value.trim(),
        email: editarEmailInquilino.value.trim(),
        area_telefono: editarAreaTelefonoInquilino.value.trim(),
        telefono: editarTelefonoInquilino.value.trim()
    };

    try{
        mostrarCarga();
        btnGuardarCambiosInquilino.disabled = true;

        const res = await fetch(
            `${API_INQUILINO}?id_usuario=${id_usuario}&id_inmobiliaria=${id_inmobiliaria}&id_inquilino=${editarIdInquilino.value}`,
            {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    "Authorization": `Bearer ${token}`
                },
                body: JSON.stringify(datos)
            }
        );

        if(!validarSesionRespuesta(res)){
            return;
        }

        const respuesta = await res.json();

        if(!res.ok){
            mensajeEditarInquilino.textContent = respuesta.error || respuesta.mensaje || "No se pudo editar el inquilino";
            mensajeEditarInquilino.className = "mensajeError";
            return;
        }

        cerrarEditarInquilino();
        await cargarInquilinos();
        mostrarExito("Inquilino actualizado correctamente");
    }catch(error){
        console.error(error);
        mensajeEditarInquilino.textContent = "No se pudo conectar con el servidor";
        mensajeEditarInquilino.className = "mensajeError";
    }finally{
        btnGuardarCambiosInquilino.disabled = false;
        ocultarCarga();
    }
});

/*CANCELAR ELIMINACION*/
btnCancelarEliminarInquilino.addEventListener("click", function(){
    idInquilinoEliminar.value = "";
    ventanaEliminarInquilino.classList.add("ocultarVentana");
});

/*ELIMINAR INQUILINO*/
btnConfirmarEliminarInquilino.addEventListener("click", async function(){
    const id_inquilino = idInquilinoEliminar.value;

    try{
        mostrarCarga();
        btnConfirmarEliminarInquilino.disabled = true;

        const res = await fetch(
            `${API_INQUILINO}?id_usuario=${id_usuario}&id_inmobiliaria=${id_inmobiliaria}&id_inquilino=${id_inquilino}`,
            {
                method: "DELETE",
                headers: {
                    "Authorization": `Bearer ${token}`
                }
            }
        );

        if(!validarSesionRespuesta(res)){
            return;
        }

        if(!res.ok){
            const respuesta = await res.json();
            mostrarError(respuesta.error || respuesta.mensaje || "No se pudo eliminar el inquilino");
            return;
        }

        ventanaEliminarInquilino.classList.add("ocultarVentana");
        idInquilinoEliminar.value = "";

        await cargarInquilinos();

        mostrarExito("Inquilino eliminado y propiedad disponible nuevamente");
    }catch(error){
        console.error(error);
        mostrarError("No se pudo conectar con el servidor");
    }finally{
        btnConfirmarEliminarInquilino.disabled = false;
        ocultarCarga();
    }
});

/*CERRAR VENTANAS TOCANDO AFUERA*/
ventanaAgregarInquilino.addEventListener("click", function(evento){
    if(evento.target === ventanaAgregarInquilino){
        cerrarAgregarInquilino();
    }
});

ventanaVerInquilino.addEventListener("click", function(evento){
    if(evento.target === ventanaVerInquilino){
        ventanaVerInquilino.classList.add("ocultarVentana");
    }
});

ventanaEditarInquilino.addEventListener("click", function(evento){
    if(evento.target === ventanaEditarInquilino){
        cerrarEditarInquilino();
    }
});

/*INICIAR*/
cargarInquilinos();
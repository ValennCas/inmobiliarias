const API_INMOBILIARIA = "http://localhost/inmobiliarias/backend/controller/inmobiliariaController.php";
const API_ALQUILER = "http://localhost/inmobiliarias/backend/controller/alquileresController.php";
const URL_BACKEND = "http://localhost/inmobiliarias/backend/";

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

/*ELEMENTOS DE LA INMOBILIARIA*/
const nombreInmobiliaria = document.querySelector("#nombreInmobiliaria");
const emailInmobiliaria = document.querySelector("#emailInmobiliaria");
const btnEditarInmobiliaria = document.querySelector("#btnEditarInmobiliaria");

/*ELEMENTOS DEL MENU*/
const btnPropiedades = document.querySelector("#btnPropiedades");
const btnAlquileres = document.querySelector("#btnAlquileres");
const btnInquilinos = document.querySelector("#btnInquilinos");
const btnMisDatos = document.querySelector("#btnMisDatos");
const btnCambiarContrasenia = document.querySelector("#btnCambiarContrasenia");
const btnVolverInmobiliarias = document.querySelector("#btnVolverInmobiliarias");
const btnCerrarSesion = document.querySelector("#btnCerrarSesion");

/*ELEMENTOS DEL MENU CELULAR*/
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

/*ELEMENTOS DEL LISTADO DE ALQUILERES*/
const listaAlquileres = document.querySelector("#listaAlquileres");
const sinAlquileres = document.querySelector("#sinAlquileres");

/*ELEMENTOS PARA VISUALIZAR ALQUILER*/
const ventanaVerAlquiler = document.querySelector("#ventanaVerAlquiler");
const btnCerrarVerAlquiler = document.querySelector("#btnCerrarVerAlquiler");
const verTituloAlquiler = document.querySelector("#verTituloAlquiler");
const verDireccionAlquiler = document.querySelector("#verDireccionAlquiler");
const verCiudadAlquiler = document.querySelector("#verCiudadAlquiler");
const verProvinciaAlquiler = document.querySelector("#verProvinciaAlquiler");
const verPrecioAlquiler = document.querySelector("#verPrecioAlquiler");
const verFechaInicio = document.querySelector("#verFechaInicio");
const verFechaFin = document.querySelector("#verFechaFin");
const verImagenAlquiler = document.querySelector("#verImagenAlquiler");

/*ELEMENTOS PARA VISUALIZAR INQUILINO*/
const verNombreInquilino = document.querySelector("#verNombreInquilino");
const verApellidoInquilino = document.querySelector("#verApellidoInquilino");
const verTelefonoInquilino = document.querySelector("#verTelefonoInquilino");
const verEmailInquilino = document.querySelector("#verEmailInquilino");

/*ELEMENTOS PARA CAMBIAR ESTADO*/
const ventanaCambiarEstado = document.querySelector("#ventanaCambiarEstado");
const btnCerrarCambiarEstado = document.querySelector("#btnCerrarCambiarEstado");
const btnCancelarCambiarEstado = document.querySelector("#btnCancelarCambiarEstado");
const formCambiarEstado = document.querySelector("#formCambiarEstado");
const idPropiedadCambiarEstado = document.querySelector("#idPropiedadCambiarEstado");
const nuevoEstadoPropiedad = document.querySelector("#nuevoEstadoPropiedad");
const mensajeCambiarEstado = document.querySelector("#mensajeCambiarEstado");
const btnGuardarEstado = document.querySelector("#btnGuardarEstado");

/*VALIDAR RESPUESTA DE SESION*/
function validarSesionRespuesta(res){
    if(res.status === 401){
        localStorage.clear();
        window.location.replace("index.html");
        return false;
    }
    return true;
}

/*CARGAR DATOS DE LA INMOBILIARIA*/
async function cargarDatosInmobiliaria(){
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

        if(!validarSesionRespuesta(res)){
            return;
        }

        const datos = await res.json();

        if(!res.ok){
            nombreInmobiliaria.textContent = "Inmobiliaria";
            emailInmobiliaria.textContent = "No se pudieron cargar los datos";
            return;
        }

        const inmobiliaria = datos.inmobiliaria || datos;

        nombreInmobiliaria.textContent = inmobiliaria.nombre || "Inmobiliaria";
        emailInmobiliaria.textContent = inmobiliaria.email || "";
    }catch(error){
        console.error(error);
        nombreInmobiliaria.textContent = "Inmobiliaria";
        emailInmobiliaria.textContent = "No se pudieron cargar los datos";
    }
}

/*MENU*/
btnPropiedades.addEventListener("click", function(){
    window.location.href = "inmobiliaria.html";
});

btnAlquileres.addEventListener("click", function(){
    window.location.href = "alquileres.html";
});

btnInquilinos.addEventListener("click", function(){
    window.location.href = "inquilino.html";
});

btnVolverInmobiliarias.addEventListener("click", function(){
    window.location.href = "inicioInmobiliaria.html";
});

btnCerrarSesion.addEventListener("click", function(){
    localStorage.clear();
    window.location.replace("index.html");
});

btnCambiarContrasenia.addEventListener("click", function(){
    window.location.href = "cambiarContrasenia.html";
});

/*MENU CELULAR*/
btnPropiedadesMobile.addEventListener("click", function(){
    window.location.href = "inmobiliaria.html";
});

btnAlquileresMobile.addEventListener("click", function(){
    window.location.href = "alquileres.html";
});

btnInquilinosMobile.addEventListener("click", function(){
    window.location.href = "inquilino.html";
});

btnVolverMobile.addEventListener("click", function(){
    window.location.href = "inicioInmobiliaria.html";
});

btnMenuMobile.addEventListener("click", function(){
    submenuMobile.classList.toggle("oculto");
});

btnCerrarSesionMobile.addEventListener("click", function(){
    localStorage.clear();
    window.location.replace("index.html");
});

btnCambiarContraseniaMobile.addEventListener("click", function(){
    window.location.href = "cambiarContrasenia.html";
});

/*EDITAR INMOBILIARIA*/
btnEditarInmobiliaria.addEventListener("click", function(){
    window.location.href = "inmobiliaria.html";
});

btnEditarInmobiliariaMobile.addEventListener("click", function(){
    window.location.href = "inmobiliaria.html";
});

/*MIS DATOS*/
btnMisDatos.addEventListener("click", function(){
    window.location.href = "inmobiliaria.html";
});

btnMisDatosMobile.addEventListener("click", function(){
    window.location.href = "inmobiliaria.html";
});

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

/*FORMATEAR PRECIO*/
function formatearPrecio(precio){
    if(precio === null || precio === undefined || precio === ""){
        return "";
    }

    return Number(precio).toLocaleString("es-AR");
}

/*MOSTRAR CUANDO NO HAY ALQUILERES*/
function mostrarSinAlquileres(){
    listaAlquileres.innerHTML = "";
    sinAlquileres.classList.remove("oculto");
}

/*MOSTRAR ERROR EN LISTADO*/
function mostrarErrorAlquileres(mensaje){
    listaAlquileres.innerHTML = "";
    sinAlquileres.classList.remove("oculto");
    sinAlquileres.innerHTML = `<p class="mensajeError">${mensaje}</p>`;
}

/*CARGAR ALQUILERES*/
async function cargarAlquileres(){
    try{
        mostrarCarga();

        const res = await fetch(
            `${API_ALQUILER}?id_usuario=${id_usuario}&id_inmobiliaria=${id_inmobiliaria}`,
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
            mostrarSinAlquileres();
            return;
        }

        if(!res.ok){
            mostrarErrorAlquileres(datos.error || datos.mensaje || "No se pudieron cargar los alquileres");
            return;
        }

        mostrarAlquileres(datos);
    }catch(error){
        console.error(error);
        mostrarErrorAlquileres("No se pudo conectar con el servidor");
    }finally{
        ocultarCarga();
    }
}

/*MOSTRAR ALQUILERES*/
function mostrarAlquileres(datos){
    listaAlquileres.innerHTML = "";

    let alquileres = [];

    if(Array.isArray(datos)){
        alquileres = datos;
    }else if(Array.isArray(datos.alquileres)){
        alquileres = datos.alquileres;
    }else if(datos.alquiler){
        alquileres = [datos.alquiler];
    }else if(datos.id_alquiler){
        alquileres = [datos];
    }

    if(alquileres.length === 0){
        mostrarSinAlquileres();
        return;
    }

    sinAlquileres.classList.add("oculto");

    alquileres.forEach(function(alquiler){
        const tarjeta = document.createElement("article");
        tarjeta.classList.add("tarjetaAlquiler");

        let imagen = "";

        if(alquiler.foto){
            imagen = URL_BACKEND + alquiler.foto;
        }

        tarjeta.innerHTML = `
            ${imagen !== ""
                ? `<img src="${imagen}" alt="${alquiler.titulo || "Propiedad"}" class="imagenAlquiler">`
                : `<div class="sinImagenAlquiler">Sin imagen</div>`
            }

            <div class="contenidoAlquiler">
                <div class="cabeceraTarjetaAlquiler">
                    <h3>${alquiler.titulo || "Propiedad"}</h3>
                    <span class="estadoPropiedad noDisponible">Alquilado</span>
                </div>

                <div class="datosAlquiler">
                    <p><strong>Dirección:</strong> ${alquiler.calle || ""} ${alquiler.altura || ""}</p>
                    <p><strong>Ubicación:</strong> ${alquiler.ciudad || ""}, ${alquiler.provincia || ""}</p>
                    <p><strong>Precio:</strong> $${formatearPrecio(alquiler.precio)}</p>
                </div>

                <div class="fechasAlquiler">
                    <p><strong>Inicio:</strong> ${formatearFecha(alquiler.fecha_inicio)}</p>
                    <p><strong>Fin:</strong> ${formatearFecha(alquiler.fecha_fin)}</p>
                </div>

                <div class="accionesAlquiler">
                    <button type="button" class="btnVerAlquiler" data-id="${alquiler.id_alquiler}">Visualizar</button>
                    <button type="button" class="btnCambiarEstadoAlquiler" data-id-propiedad="${alquiler.id_propiedad}">Cambiar estado</button>
                </div>
            </div>
        `;

        listaAlquileres.appendChild(tarjeta);
    });
    agregarEventosAlquileres();
}

/*AGREGAR EVENTOS A LAS TARJETAS*/
function agregarEventosAlquileres(){
    const botonesVer = document.querySelectorAll(".btnVerAlquiler");
    const botonesCambiarEstado = document.querySelectorAll(".btnCambiarEstadoAlquiler");

    botonesVer.forEach(function(boton){
        boton.addEventListener("click", function(){
            verAlquiler(boton.dataset.id);
        });
    });

    botonesCambiarEstado.forEach(function(boton){
        boton.addEventListener("click", function(){
            abrirCambiarEstado(boton.dataset.idPropiedad);
        });
    });
}

/*VISUALIZAR ALQUILER*/
async function verAlquiler(id_alquiler){
    try{
        const res = await fetch(
            `${API_ALQUILER}?id_usuario=${id_usuario}&id_inmobiliaria=${id_inmobiliaria}&id_alquiler=${id_alquiler}`,
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
            mostrarMensaje(datos.error || datos.mensaje || "No se pudo cargar el alquiler", "error");
            return;
        }

        const alquiler = datos.alquiler || datos;
        verTituloAlquiler.textContent = alquiler.titulo || "Alquiler";
        verDireccionAlquiler.textContent = `${alquiler.calle || ""} ${alquiler.altura || ""}`;
        verCiudadAlquiler.textContent = alquiler.ciudad || "";
        verProvinciaAlquiler.textContent = alquiler.provincia || "";
        verPrecioAlquiler.textContent = formatearPrecio(alquiler.precio);
        verFechaInicio.textContent = formatearFecha(alquiler.fecha_inicio);
        verFechaFin.textContent = formatearFecha(alquiler.fecha_fin);

        /*IMAGEN DEL ALQUILER*/
        if(alquiler.fotos && alquiler.fotos.length > 0){
            verImagenAlquiler.src = URL_BACKEND + alquiler.fotos[0].ruta_foto;
            verImagenAlquiler.style.display = "block";
        }else{
            verImagenAlquiler.src = "";
            verImagenAlquiler.style.display = "none";
        }

        verNombreInquilino.textContent = alquiler.nombre_inquilino || "";
        verApellidoInquilino.textContent = alquiler.apellido_inquilino || "";
        verTelefonoInquilino.textContent = `${alquiler.area_telefono_inquilino || ""} ${alquiler.telefono_inquilino || ""}`.trim();
        verEmailInquilino.textContent = alquiler.email_inquilino || "";

ventanaVerAlquiler.classList.remove("ocultarVentana");
    }catch(error){
        console.error(error);
        mostrarMensaje("No se pudo conectar con el servidor", "error");
    }finally{
        ocultarCarga();
    }
}

/*CERRAR VISUALIZAR ALQUILER*/
btnCerrarVerAlquiler.addEventListener("click", function(){
    ventanaVerAlquiler.classList.add("ocultarVentana");
});

/*ABRIR CAMBIAR ESTADO*/
function abrirCambiarEstado(id_propiedad){
    idPropiedadCambiarEstado.value = id_propiedad;
    nuevoEstadoPropiedad.value = "";
    mensajeCambiarEstado.textContent = "";
    ventanaCambiarEstado.classList.remove("ocultarVentana");
}

/*CERRAR CAMBIAR ESTADO*/
function cerrarCambiarEstado(){
    formCambiarEstado.reset();
    idPropiedadCambiarEstado.value = "";
    mensajeCambiarEstado.textContent = "";
    ventanaCambiarEstado.classList.add("ocultarVentana");
}

btnCerrarCambiarEstado.addEventListener("click", cerrarCambiarEstado);
btnCancelarCambiarEstado.addEventListener("click", cerrarCambiarEstado);

/*GUARDAR CAMBIO DE ESTADO*/
formCambiarEstado.addEventListener("submit", async function(evento){
    evento.preventDefault();

    if(nuevoEstadoPropiedad.value !== "1" && nuevoEstadoPropiedad.value !== "2"){
        mensajeCambiarEstado.textContent = "Debe seleccionar En venta o En alquiler";
        mensajeCambiarEstado.className = "mensajeError";
        return;
    }

    const datos = {
        id_propiedad: idPropiedadCambiarEstado.value,
        id_estado_propiedad: nuevoEstadoPropiedad.value
    };

    try{
        mostrarCarga();
        btnGuardarEstado.disabled = true;

        const res = await fetch(
            `${API_ALQUILER}?id_usuario=${id_usuario}&id_inmobiliaria=${id_inmobiliaria}`,
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
            mensajeCambiarEstado.textContent = respuesta.error || respuesta.mensaje || "No se pudo cambiar el estado";
            mensajeCambiarEstado.className = "mensajeError";
            return;
        }

        cerrarCambiarEstado();

        await cargarAlquileres();

        mostrarExito("Estado de la propiedad actualizado correctamente");
    }catch(error){
        console.error(error);
        mensajeCambiarEstado.textContent = "No se pudo conectar con el servidor";
        mensajeCambiarEstado.className = "mensajeError";
    }finally{
        btnGuardarEstado.disabled = false;
        ocultarCarga();
    }
});

/*CERRAR VENTANAS TOCANDO AFUERA*/
ventanaVerAlquiler.addEventListener("click", function(evento){
    if(evento.target === ventanaVerAlquiler){
        ventanaVerAlquiler.classList.add("ocultarVentana");
    }
});

ventanaCambiarEstado.addEventListener("click", function(evento){
    if(evento.target === ventanaCambiarEstado){
        cerrarCambiarEstado();
    }
});

/*INICIAR*/
cargarDatosInmobiliaria();
cargarAlquileres();
const API_ALQUILER = BASE_BACKEND + "controller/alquileresController.php";
const URL_BACKEND = BASE_BACKEND;

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

let idAlquilerCambiarEstado = null;

/*VALIDAR RESPUESTA DE SESION*/
function validarSesionRespuesta(res){
    if(res.status === 401){
        localStorage.clear();
        window.location.replace("index.html");
        return false;
    }

    return true;
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
                    <button type="button" class="btnCambiarEstadoAlquiler" data-id-alquiler="${alquiler.id_alquiler}" data-id-propiedad="${alquiler.id_propiedad}">Cambiar estado</button>
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
            abrirCambiarEstado(boton.dataset.idAlquiler, boton.dataset.idPropiedad);
        });
    });
}

/*VISUALIZAR ALQUILER*/
async function verAlquiler(id_alquiler){
    try{
        mostrarCarga();

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
            mostrarError(datos.error || datos.mensaje || "No se pudo cargar el alquiler");
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
        if(alquiler.foto){
            verImagenAlquiler.src = URL_BACKEND + alquiler.foto;
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
        mostrarError("No se pudo conectar con el servidor");
    }finally{
        ocultarCarga();
    }
}

/*CERRAR VISUALIZAR ALQUILER*/
btnCerrarVerAlquiler.addEventListener("click", function(){
    ventanaVerAlquiler.classList.add("ocultarVentana");
});

/*ABRIR CAMBIAR ESTADO*/
function abrirCambiarEstado(id_alquiler, id_propiedad){
    idAlquilerCambiarEstado = id_alquiler;
    idPropiedadCambiarEstado.value = id_propiedad;
    nuevoEstadoPropiedad.value = "";
    mensajeCambiarEstado.textContent = "";
    ventanaCambiarEstado.classList.remove("ocultarVentana");
}

/*CERRAR CAMBIAR ESTADO*/
function cerrarCambiarEstado(){
    formCambiarEstado.reset();
    idAlquilerCambiarEstado = null;
    idPropiedadCambiarEstado.value = "";
    mensajeCambiarEstado.textContent = "";
    ventanaCambiarEstado.classList.add("ocultarVentana");
}

btnCerrarCambiarEstado.addEventListener("click", cerrarCambiarEstado);
btnCancelarCambiarEstado.addEventListener("click", cerrarCambiarEstado);

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

/*GUARDAR CAMBIO DE ESTADO*/
formCambiarEstado.addEventListener("submit", async function(evento){
    evento.preventDefault();

    if(nuevoEstadoPropiedad.value !== "EN VENTA" && nuevoEstadoPropiedad.value !== "EN ALQUILER"){
        mensajeCambiarEstado.textContent = "Debe seleccionar En venta o En alquiler";
        mensajeCambiarEstado.className = "mensajeError";
        return;
    }

    const datos = {
        estado_nuevo: nuevoEstadoPropiedad.value
    };

    try{
        mostrarCarga();
        btnGuardarEstado.disabled = true;

        const res = await fetch(
            `${API_ALQUILER}?id_usuario=${id_usuario}&id_inmobiliaria=${id_inmobiliaria}&id_alquiler=${idAlquilerCambiarEstado}`,
            {
                method: "DELETE",
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

        if(!res.ok){
            const respuesta = await res.json();

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

/*INICIAR*/
cargarAlquileres();
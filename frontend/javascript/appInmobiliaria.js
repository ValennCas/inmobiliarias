const API_INMOBILIARIA = "http://localhost/inmobiliarias/backend/controller/inmobiliariaController.php";
const API_PROPIEDAD = "http://localhost/inmobiliarias/backend/controller/propiedadesController.php";
const API_USUARIO = "http://localhost/inmobiliarias/backend/controller/usuarioController.php";
const URL_BACKEND = "http://localhost/inmobiliarias/backend/";

/*DATOS SESION*/
const token = localStorage.getItem("token");
const id_usuario = localStorage.getItem("id_usuario");
const id_inmobiliaria = localStorage.getItem("id_inmobiliaria");

/*VALIDAR SESION*/
if(!token || !id_usuario || !id_inmobiliaria){
    window.location.replace("index.html");
}

/*VALIDAR SI SE VOLVIO CON FLECHAS*/
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

/*ELEMENTOS MENU*/
const nombreInmobiliaria = document.querySelector("#nombreInmobiliaria");
const emailInmobiliaria = document.querySelector("#emailInmobiliaria");

const btnEditarInmobiliaria = document.querySelector("#btnEditarInmobiliaria");

const btnPropiedades = document.querySelector("#btnPropiedades");
const btnAlquileres = document.querySelector("#btnAlquileres");
const btnInquilinos = document.querySelector("#btnInquilinos");
const btnMisDatos = document.querySelector("#btnMisDatos");
const btnCambiarContrasenia = document.querySelector("#btnCambiarContrasenia");

const btnVolverInmobiliarias = document.querySelector("#btnVolverInmobiliarias");
const btnCerrarSesion = document.querySelector("#btnCerrarSesion");

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

/*PROPIEDADES*/
const btnAgregarPropiedad = document.querySelector("#btnAgregarPropiedad");
const listaPropiedades = document.querySelector("#listaPropiedades");
const sinPropiedades = document.querySelector("#sinPropiedades");

/*AGREGAR*/
const ventanaAgregarPropiedad = document.querySelector("#ventanaAgregarPropiedad");
const btnCerrarAgregar = document.querySelector("#btnCerrarAgregar");
const btnCancelarAgregar = document.querySelector("#btnCancelarAgregar");

const formAgregarPropiedad = document.querySelector("#formAgregarPropiedad");

const titulo = document.querySelector("#titulo");
const estadoPropiedad = document.querySelector("#id_estado_propiedad");
const precio = document.querySelector("#precio");
const provincia = document.querySelector("#provincia");
const ciudad = document.querySelector("#ciudad");
const calle = document.querySelector("#calle");
const altura = document.querySelector("#altura");
const descripcion = document.querySelector("#descripcion");
const fotos = document.querySelector("#fotos");

const vistaPreviaFotos = document.querySelector("#vistaPreviaFotos");
const mensajeAgregarPropiedad = document.querySelector("#mensajeAgregarPropiedad");
const btnGuardarPropiedad = document.querySelector("#btnGuardarPropiedad");

/*EDITAR PROPIEDAD*/
const ventanaEditarPropiedad = document.querySelector("#ventanaEditarPropiedad");
const btnCerrarEditar = document.querySelector("#btnCerrarEditar");
const btnCancelarEditar = document.querySelector("#btnCancelarEditar");

const formEditarPropiedad = document.querySelector("#formEditarPropiedad");

const editarIdPropiedad = document.querySelector("#editar_id_propiedad");
const editarTitulo = document.querySelector("#editar_titulo");
const editarEstadoPropiedad = document.querySelector("#editar_estado_propiedad");
const editarPrecio = document.querySelector("#editar_precio");
const editarProvincia = document.querySelector("#editar_provincia");
const editarCiudad = document.querySelector("#editar_ciudad");
const editarCalle = document.querySelector("#editar_calle");
const editarAltura = document.querySelector("#editar_altura");
const editarDescripcion = document.querySelector("#editar_descripcion");
const editarFotos = document.querySelector("#editar_fotos");

const fotosActuales = document.querySelector("#fotosActuales");
const mensajeEditarPropiedad = document.querySelector("#mensajeEditarPropiedad");
const btnGuardarCambiosPropiedad = document.querySelector("#btnGuardarCambiosPropiedad");

/*VER*/
const ventanaVerPropiedad = document.querySelector("#ventanaVerPropiedad");
const btnCerrarVer = document.querySelector("#btnCerrarVer");

const verTituloPropiedad = document.querySelector("#verTituloPropiedad");
const fotoPropiedad = document.querySelector("#fotoPropiedad");
const btnFotoAnterior = document.querySelector("#btnFotoAnterior");
const btnFotoSiguiente = document.querySelector("#btnFotoSiguiente");
const contadorFotos = document.querySelector("#contadorFotos");

const verOperacion = document.querySelector("#verOperacion");
const verPrecio = document.querySelector("#verPrecio");
const verDireccion = document.querySelector("#verDireccion");
const verCiudad = document.querySelector("#verCiudad");
const verProvincia = document.querySelector("#verProvincia");
const verDescripcion = document.querySelector("#verDescripcion");

/*ELIMINAR*/
const ventanaEliminarPropiedad = document.querySelector("#ventanaEliminarPropiedad");
const idPropiedadEliminar = document.querySelector("#idPropiedadEliminar");
const btnConfirmarEliminar = document.querySelector("#btnConfirmarEliminar");
const btnCancelarEliminar = document.querySelector("#btnCancelarEliminar");

/*LOADER Y MENSAJE*/
const mensajeFlotante = document.querySelector("#mensajeFlotante");
const textoMensajeFlotante = document.querySelector("#textoMensajeFlotante");

/*ELEMENETOS EDICION INMOBILIARIA*/
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

/*VER DATOS INMOBILIARIA*/
const btnVerInmobiliaria = document.querySelector("#btnVerInmobiliaria");
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

/*ELEMENTOS PARA VER LOS DATOS DE LA CUENTA*/
const ventanaMisDatos = document.querySelector("#ventanaMisDatos");
const btnCerrarMisDatos = document.querySelector("#btnCerrarMisDatos");
const datoNombreUsuario = document.querySelector("#datoNombreUsuario");
const datoApellidoUsuario = document.querySelector("#datoApellidoUsuario");
const datoEmailUsuario = document.querySelector("#datoEmailUsuario");

/*EDITAR DATOS DE LA CUENTA*/
const btnEditarMisDatos = document.querySelector("#btnEditarMisDatos");
const ventanaEditarMisDatos = document.querySelector("#ventanaEditarMisDatos");
const btnCerrarEditarMisDatos = document.querySelector("#btnCerrarEditarMisDatos");
const btnCancelarEditarUsuario = document.querySelector("#btnCancelarEditarUsuario");
const formEditarMisDatos = document.querySelector("#formEditarMisDatos");

const editarNombreUsuario = document.querySelector("#editarNombreUsuario");
const editarApellidoUsuario = document.querySelector("#editarApellidoUsuario");
const editarEmailUsuario = document.querySelector("#editarEmailUsuario");
const mensajeEditarUsuario = document.querySelector("#mensajeEditarUsuario");

/*VARIABLES CARRUSEL*/
let imagenesPropiedad = [];
let indiceImagen = 0;
let fotosEliminar = [];
let cantidadFotosActuales = 0;

/*MENSAJE REUTILIZABLE*/
function mostrarMensaje(texto, tipo){
    textoMensajeFlotante.textContent = texto;
    mensajeFlotante.className = "mensajeFlotante";
    if(tipo === "error"){
        mensajeFlotante.classList.add("error");
    }else{
        mensajeFlotante.classList.add("exito");
    }
    setTimeout(function(){
        mensajeFlotante.classList.add("oculto");
    }, 2500);
}

/*VALIDAR 401*/
function validarSesionRespuesta(res){
    if(res.status === 401){
        localStorage.clear();
        window.location.replace("index.html");
        return false;
    }
    return true;
}

/*CARGAR DATOS INMOBILIARIA*/
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
btnVolverInmobiliarias.addEventListener("click", function(){
    window.location.href = "inicioInmobiliaria.html";
});

btnCerrarSesion.addEventListener("click", function(){
    localStorage.clear();
    window.location.replace("index.html");
});

/*MENU MOBILE*/
btnVolverMobile.addEventListener("click", function(){
    window.location.href = "inicioInmobiliaria.html";
});

btnCerrarSesionMobile.addEventListener("click", function(){
    localStorage.clear();
    window.location.replace("index.html");
});

btnMenuMobile.addEventListener("click", function(){
    submenuMobile.classList.toggle("oculto");
});

/*NAVEGACION*/
btnPropiedades.addEventListener("click", function(){
    window.location.href = "inmobiliaria.html";
});

btnPropiedadesMobile.addEventListener("click", function(){
    window.location.href = "inmobiliaria.html";
});

/*Estas páginas las hacemos después por ahora quedan preparadas*/
btnAlquileres.addEventListener("click", function(){
    window.location.href = "alquileres.html";
});

btnAlquileresMobile.addEventListener("click", function(){
    window.location.href = "alquileres.html";
});

btnInquilinos.addEventListener("click", function(){
    window.location.href = "inquilino.html";
});

btnInquilinosMobile.addEventListener("click", function(){
    window.location.href = "inquilino.html";
});

btnMisDatos.addEventListener("click", function(){
    abrirMisDatos();
});

btnMisDatosMobile.addEventListener("click", function(){
    submenuMobile.classList.add("oculto");
    abrirMisDatos();
});

btnCambiarContrasenia.addEventListener("click", function(){
    window.location.href = "cambiarContrasenia.html";
});

btnCambiarContraseniaMobile.addEventListener("click", function(){
    window.location.href = "cambiarContrasenia.html";
});

/*ABRIR/CERRAR VENTANA DE VER DATOS DE INMOBILIARIA*/
btnVerInmobiliaria.addEventListener("click", abrirVerInmobiliaria);
btnCerrarVerInmobiliaria.addEventListener("click", function(){
    ventanaVerInmobiliaria.classList.add("ocultarVentana");
});

/*ABRIR VENTANA DE EDITAR INMOBILIARIA*/
btnEditarInmobiliaria.addEventListener("click", abrirEditarInmobiliaria);

btnEditarInmobiliariaMobile.addEventListener("click", function(){
    submenuMobile.classList.add("oculto");
    abrirEditarInmobiliaria();
});

/*CERRAR VENTANA EDICION DE DATOS INMOBILIARIA*/
btnCerrarEditarInmobiliaria.addEventListener("click", cerrarEditarInmobiliaria);
btnCancelarEditarInmobiliaria.addEventListener("click", cerrarEditarInmobiliaria);

/*CARGAR PROVINCIAS*/
cargarProvincias(provincia, ciudad);
provincia.addEventListener("change", function(){
    cargarCiudades(provincia.value, ciudad);
});
cargarProvincias(editarProvincia, editarCiudad);
editarProvincia.addEventListener("change", function(){
    cargarCiudades(editarProvincia.value, editarCiudad);
});

/*ABRIR AGREGAR*/
btnAgregarPropiedad.addEventListener("click", function(){
    mensajeAgregarPropiedad.textContent = "";
    ventanaAgregarPropiedad.classList.remove("ocultarVentana");
});

/*CERRAR AGREGAR*/
function cerrarAgregar(){
    formAgregarPropiedad.reset();
    vistaPreviaFotos.innerHTML = "";
    mensajeAgregarPropiedad.textContent = "";
    ventanaAgregarPropiedad.classList.add("ocultarVentana");
}

btnCerrarAgregar.addEventListener("click", cerrarAgregar);
btnCancelarAgregar.addEventListener("click", cerrarAgregar);

/*CERRAR TOCANDO AFUERA DE LA VENTANA*/
ventanaAgregarPropiedad.addEventListener("click", function(evento){
    if(evento.target === ventanaAgregarPropiedad){
        cerrarAgregar();
    }
});

/*PREVISUALIZAR FOTOS*/
fotos.addEventListener("change", function(){
    vistaPreviaFotos.innerHTML = "";
    const archivos = Array.from(fotos.files);
    const limiteFoto = 20 * 1024 * 1024;
    if(archivos.length > 5){
        mensajeAgregarPropiedad.textContent = "Solo puede cargar hasta 5 imágenes";
        mensajeAgregarPropiedad.className = "mensajeError";
        fotos.value = "";
        return;
    }

    for(const archivo of archivos){
        if(archivo.size > limiteFoto){
            mensajeAgregarPropiedad.textContent = "Cada imagen puede pesar como maximo 20 MB";
            mensajeAgregarPropiedad.className = "mensajeError";
            fotos.value = "";
            vistaPreviaFotos.innerHTML = "";
            return;
        }
    }

    mensajeAgregarPropiedad.textContent = "";
    archivos.forEach(function(archivo){
        const imagen = document.createElement("img");
        imagen.src = URL.createObjectURL(archivo);
        vistaPreviaFotos.appendChild(imagen);
    });
});

/*LISTAR PROPIEDADES*/
async function cargarPropiedades(){
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

        const datos = await res.json()
        listaPropiedades.innerHTML = "";
        if(res.status === 404){
            mostrarSinPropiedades();
            return;
        }

        if(!res.ok){
            mostrarErrorPropiedades(datos.error || datos.mensaje ||"No se pudieron cargar las propiedades.");
            return;
        }
        mostrarPropiedades(datos);
    }catch(error){
        console.error(error);
        mostrarErrorPropiedades("No se pudo conectar con el servidor.");
    }finally{
        ocultarCarga();
    }
}

function obtenerDisponibilidad(estado){
    const estadoTexto = (estado || "").trim().toUpperCase();
    if(estadoTexto === "EN VENTA" || estadoTexto === "EN ALQUILER"){
        return {
            texto: "Disponible",
            clase: "disponible"
        };
    }
    return {
        texto: "No Disponible",
        clase: "noDisponible"
    };
}

/*MOSTRAR MSJ SIN PROPIEDADES*/
function mostrarSinPropiedades(){
    sinPropiedades.classList.remove("oculto");
    sinPropiedades.innerHTML = `<p>Aún no hay propiedades agregadas.</p>`;
    listaPropiedades.innerHTML = "";
}

/*MOSTRAR MSJ DE ERROR AL NO PODER TRAER LAS PROPIEDADES*/
function mostrarErrorPropiedades(mensaje){
    sinPropiedades.classList.remove("oculto");
    sinPropiedades.innerHTML = `<p class="mensajeError">${mensaje}</p>`;
    listaPropiedades.innerHTML = "";
}

/*MOSTRAR PROPIEDADES*/
/*MOSTRAR PROPIEDADES*/
function mostrarPropiedades(datos){
    listaPropiedades.innerHTML = "";
    let propiedades = [];

    if(Array.isArray(datos)){
        propiedades = datos;
    }else if(Array.isArray(datos.propiedades)){
        propiedades = datos.propiedades;
    }else if(datos.propiedad){
        propiedades = [datos.propiedad];
    }else if(datos.id_propiedad){
        propiedades = [datos];
    }
    if(propiedades.length === 0){
        mostrarSinPropiedades();
        return;
    }

    sinPropiedades.classList.add("oculto");
    propiedades.forEach(function(propiedad){
        const tarjeta = document.createElement("article");
        tarjeta.classList.add("tarjetaPropiedad");
        const disponibilidad = obtenerDisponibilidad(propiedad.estado);
        const primeraFoto = propiedad.fotos && propiedad.fotos.length > 0
            ? URL_BACKEND + propiedad.fotos[0].ruta_foto
            : "img/sin-imagen.jpg";
        tarjeta.innerHTML = `
            <div class="estadoPropiedad ${disponibilidad.clase}">
                ${disponibilidad.texto}
            </div>
            <img
                src="${primeraFoto}"
                alt="${propiedad.titulo || "Propiedad"}"
                class="imagenPropiedad"
            >
            <div class="contenidoPropiedad">
                <h3>${propiedad.titulo || "Propiedad"}</h3>
                <p>${propiedad.estado || ""}</p>
                <p>${propiedad.precio ? "$" + propiedad.precio : ""}</p>
                <p>${propiedad.calle || ""} ${propiedad.altura || ""}</p>
                <div class="accionesPropiedad">
                    <button type="button" class="btnVerPropiedad" data-id="${propiedad.id_propiedad}">Ver</button>
                    <button type="button" class="btnEditarPropiedad" data-id="${propiedad.id_propiedad}">Editar</button>
                    <button type="button" class="btnEliminarPropiedad" data-id="${propiedad.id_propiedad}">Eliminar</button>
                </div>
            </div>
        `;

        listaPropiedades.appendChild(tarjeta);
    });
    agregarEventosPropiedades();
}

/*EVENTOS TARJETAS*/
function agregarEventosPropiedades(){
    const botonesVer = document.querySelectorAll(".btnVerPropiedad");
    const botonesEditar = document.querySelectorAll(".btnEditarPropiedad");
    const botonesEliminar = document.querySelectorAll(".btnEliminarPropiedad");

    botonesVer.forEach(function(boton){
        boton.addEventListener("click", function(){
            verPropiedad(boton.dataset.id);
        });
    });

    botonesEditar.forEach(function(boton){
        boton.addEventListener("click", function(){
            cargarPropiedadEditar(boton.dataset.id);
        });
    });

    botonesEliminar.forEach(function(boton){
        boton.addEventListener("click", function(){
            idPropiedadEliminar.value = boton.dataset.id;
            ventanaEliminarPropiedad.classList.remove(
                "ocultarVentana"
            );
        });
    });
}

/*CREAR PROPIEDAD*/
formAgregarPropiedad.addEventListener("submit", async function(evento){
    evento.preventDefault();
    evento.stopPropagation();

    console.log("SUBMIT INTERCEPTADO");
    const archivos = Array.from(fotos.files);
    if(archivos.length > 5){
        mensajeAgregarPropiedad.textContent =
            "Solo puede cargar hasta 5 imágenes";
        mensajeAgregarPropiedad.className =
            "mensajeError";
        return;
    }

    if( titulo.value.trim() === "" || estadoPropiedad.value === "" || precio.value.trim() === "" || provincia.value === "" || 
    ciudad.value === "" || calle.value.trim() === "" || altura.value.trim() === "" || descripcion.value.trim() === ""){
        mensajeAgregarPropiedad.textContent =
            "Faltan completar campos";
        mensajeAgregarPropiedad.className =
            "mensajeError";
        return;
    }

    /*COMO HAY IMAGENES USAMOS FORMDATA NO JSON*/
    const datos = new FormData();

    datos.append("id_inmobiliaria", id_inmobiliaria);
    datos.append("titulo", titulo.value.trim());
    datos.append("id_estado_propiedad", estadoPropiedad.value);
    datos.append("precio", precio.value);
    datos.append("provincia", provincia.options[provincia.selectedIndex].text);
    datos.append("ciudad", ciudad.value);
    datos.append("calle", calle.value.trim());
    datos.append("altura", altura.value);
    datos.append("descripcion", descripcion.value.trim());

    archivos.forEach(function(archivo){
        datos.append("fotos[]", archivo);
    });

    try{
        btnGuardarPropiedad.disabled = true;
        const res = await fetch(
            `${API_PROPIEDAD}?id_usuario=${id_usuario}`,
            {
                method: "POST",
                headers: {
                    "Authorization": `Bearer ${token}`
                },
                body: datos
            }
        );

        if(!validarSesionRespuesta(res)){
            return;
        }

        const respuesta = await res.json();
        if(!res.ok){
            ocultarCarga();
            mensajeAgregarPropiedad.textContent = respuesta.error || respuesta.mensaje || "No se pudo registrar la propiedad";
            mensajeAgregarPropiedad.className = "mensajeError";
            return;
        }
        formAgregarPropiedad.reset();
        formAgregarPropiedad.reset();
        vistaPreviaFotos.innerHTML = "";
        ventanaAgregarPropiedad.classList.add("ocultarVentana");
        await cargarPropiedades();
        mostrarExito("Propiedad agregada correctamente");
    }catch(error){
        console.error(error);
        ocultarCarga();
        mensajeAgregarPropiedad.textContent = "Ocurrió un error al registrar la propiedad";
        mensajeAgregarPropiedad.className = "mensajeError";
    }finally{
        btnGuardarPropiedad.disabled = false;
        ocultarCarga();
    }
});

/*VER PROPIEDAD*/
async function verPropiedad(id_propiedad){
    try{
        mostrarCarga();
        const res = await fetch(
            `${API_PROPIEDAD}?id_usuario=${id_usuario}&id_inmobiliaria=${id_inmobiliaria}&id_propiedad=${id_propiedad}`,
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
        console.log(datos);

        if(!res.ok){
            mostrarMensaje("No se pudo cargar la propiedad");
            return;
        }

        const propiedad = datos.propiedad || datos;

        verTituloPropiedad.textContent = propiedad.titulo || "Propiedad";
        verOperacion.textContent = propiedad.estado || "";
        verPrecio.textContent = propiedad.precio ? `$${propiedad.precio}` : "";
        verDireccion.textContent = `${propiedad.calle || ""} ${propiedad.altura || ""}`;
        verCiudad.textContent = propiedad.ciudad || "";
        verProvincia.textContent = propiedad.provincia || "";
        verDescripcion.textContent = propiedad.descripcion || "";

        imagenesPropiedad = propiedad.fotos || datos.fotos || [];
        indiceImagen = 0;
        mostrarImagenCarrusel();
        ventanaVerPropiedad.classList.remove(
            "ocultarVentana"
        );
    }catch(error){
        console.error(error);
        mostrarMensaje("No se pudo conectar con el servidor");
    }finally{
        ocultarCarga();
    }
};

/*CARRUSEL FOTOS*/
function mostrarImagenCarrusel(){
    if(imagenesPropiedad.length === 0){
        fotoPropiedad.removeAttribute("src");
        fotoPropiedad.alt = "La propiedad no posee imagenes";
        contadorFotos.textContent = "Sin imagenes";
        return;
    }

    const imagen = imagenesPropiedad[indiceImagen];
    if(typeof imagen === "string"){
        fotoPropiedad.src = URL_BACKEND + imagen;
    }else{
        fotoPropiedad.src = URL_BACKEND + imagen.ruta_foto;
    }
    contadorFotos.textContent = `${indiceImagen + 1} de ${imagenesPropiedad.length}`;
}

btnFotoAnterior.addEventListener("click", function(){
    if(imagenesPropiedad.length === 0){
        return;
    }
    indiceImagen--;
    if(indiceImagen < 0){
        indiceImagen = imagenesPropiedad.length - 1;
    }
    mostrarImagenCarrusel();
});

btnFotoSiguiente.addEventListener("click", function(){
    if(imagenesPropiedad.length === 0){
        return;
    }
    indiceImagen++;
    if(indiceImagen >= imagenesPropiedad.length){
        indiceImagen = 0;
    }
    mostrarImagenCarrusel();
});

btnCerrarVer.addEventListener("click", function(){
    ventanaVerPropiedad.classList.add(
        "ocultarVentana"
    );
});

/*CARGAR EDITAR*/
async function cargarPropiedadEditar(id_propiedad){
    try{
        mostrarCarga();
        const res = await fetch(
            `${API_PROPIEDAD}?id_usuario=${id_usuario}&id_inmobiliaria=${id_inmobiliaria}&id_propiedad=${id_propiedad}`,
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
            mostrarMensaje("No se pudo cargar la propiedad", "error");
            return;
        }

        const propiedad = datos.propiedad || datos;

        editarIdPropiedad.value = propiedad.id_propiedad;
        editarTitulo.value = propiedad.titulo || "";
        editarEstadoPropiedad.value = propiedad.id_estado_propiedad || "";
        editarPrecio.value = propiedad.precio || "";
        editarCalle.value = propiedad.calle || "";
        editarAltura.value = propiedad.altura || "";
        editarDescripcion.value = propiedad.descripcion || "";

        mostrarFotosActuales(propiedad.fotos || datos.fotos || []);
        ventanaEditarPropiedad.classList.remove(
            "ocultarVentana"
        );
    }catch(error){
        console.error(error);
        mostrarMensaje(
            "No se pudo conectar con el servidor",
            "error"
        );
    }finally{
        ocultarCarga();
    }
};

/*FOTOS ACTUALES EDITAR*/
function mostrarFotosActuales(imagenes){
    fotosActuales.innerHTML = "";
    fotosEliminar = [];
    cantidadFotosActuales = imagenes.length;

    imagenes.forEach(function(imagen){
        const contenedor = document.createElement("div");
        contenedor.classList.add("fotoEditable");

        const img = document.createElement("img");
        img.src = URL_BACKEND + imagen.ruta_foto;

        const botonEliminar = document.createElement("button");
        botonEliminar.type = "button";
        botonEliminar.classList.add("btnEliminarFoto");
        botonEliminar.textContent = "🗑";
        botonEliminar.title = "Eliminar foto";

        botonEliminar.addEventListener("click", function(){
            if(fotosEliminar.includes(imagen.id_foto)){
                fotosEliminar = fotosEliminar.filter(id => id !== imagen.id_foto);
                contenedor.classList.remove("fotoMarcadaEliminar");
                botonEliminar.textContent = "🗑";
            }else{
                fotosEliminar.push(imagen.id_foto);
                contenedor.classList.add("fotoMarcadaEliminar");
                botonEliminar.textContent = "↶";
            }
        });

        contenedor.appendChild(img);
        contenedor.appendChild(botonEliminar);
        fotosActuales.appendChild(contenedor);
    });
}

/*CERRAR EDITAR*/
function cerrarEditar(){
    formEditarPropiedad.reset();
    fotosActuales.innerHTML = "";
    mensajeEditarPropiedad.textContent = "";
    ventanaEditarPropiedad.classList.add(
        "ocultarVentana"
    );
}

btnCerrarEditar.addEventListener(
    "click",
    cerrarEditar
);

btnCancelarEditar.addEventListener(
    "click",
    cerrarEditar
);

/*ELIMINAR*/
btnCancelarEliminar.addEventListener("click", function(){
    idPropiedadEliminar.value = "";
    ventanaEliminarPropiedad.classList.add(
        "ocultarVentana"
    );
});

btnConfirmarEliminar.addEventListener("click", async function(){
    const id_propiedad = idPropiedadEliminar.value;
    try{
        mostrarCarga();
        const res = await fetch(
            `${API_PROPIEDAD}?id_usuario=${id_usuario}&id_inmobiliaria=${id_inmobiliaria}&id_propiedad=${id_propiedad}`,
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
            const datos = await res.json();
            mostrarMensaje(datos.error || datos.mensaje || "No se pudo eliminar la propiedad", "error");
            return;
        }

        ventanaEliminarPropiedad.classList.add("ocultarVentana");
        idPropiedadEliminar.value = "";
        await cargarPropiedades();
        mostrarExito("Propiedad eliminada correctamente");
    }catch(error){
        console.error(error);
        mostrarMensaje("Ocurrió un error al eliminar la propiedad", "error");
    }finally{
        ocultarCarga();
    }
});

/*ABRIMOS FORMULARIO DE EDICION DE INMOBILIARIA*/
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

        if(!validarSesionRespuesta(res)){
            return;
        }

        const datos = await res.json();
        if(!res.ok){
            mostrarMensaje("No se pudieron cargar los datos de la inmobiliaria", "error");
            return;
        }

        const inmobiliaria = datos.inmobiliaria || datos;
        editarNombreInmobiliaria.value = inmobiliaria.nombre;
        editarEmailInmobiliaria.value = inmobiliaria.email;
        editarAreaInmobiliaria.value = inmobiliaria.area_telefono;
        editarTelefonoInmobiliaria.value = inmobiliaria.telefono;
        editarCalleInmobiliaria.value = inmobiliaria.calle;
        editarAlturaInmobiliaria.value = inmobiliaria.altura;
        ventanaEditarInmobiliaria.classList.remove("ocultarVentana");
    }catch(error){
        console.error(error);
        mostrarMensaje("No se pudo conectar con el servidor", "error");
    }finally{
        ocultarCarga();
    }
}

cargarProvincias(editarProvinciaInmobiliaria, editarCiudadInmobiliaria);

editarProvinciaInmobiliaria.addEventListener("change", function(){
    cargarCiudades(
        editarProvinciaInmobiliaria.value,
        editarCiudadInmobiliaria
    );
});

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
        if(!validarSesionRespuesta(res)){
            return;
        }

        const datos = await res.json();
        if(!res.ok){
            mostrarMensaje(
                datos.error ||
                datos.mensaje ||
                "No se pudieron cargar los datos de la inmobiliaria",
                "error"
            );
            return;
        }

        const inmobiliaria = datos.inmobiliaria || datos;
        verNombreInmobiliaria.textContent = inmobiliaria.nombre;
        verEmailInmobiliaria.textContent = inmobiliaria.email;
        verAreaInmobiliaria.textContent = inmobiliaria.area_telefono;
        verTelefonoInmobiliaria.textContent = inmobiliaria.telefono;
        verProvinciaInmobiliaria.textContent = inmobiliaria.provincia;
        verCiudadInmobiliaria.textContent = inmobiliaria.ciudad;
        verCalleInmobiliaria.textContent = inmobiliaria.calle;
        verAlturaInmobiliaria.textContent = inmobiliaria.altura;

        ventanaVerInmobiliaria.classList.remove("ocultarVentana");
    }catch(error){
        console.error(error);
        mostrarMensaje(
            "No se pudo conectar con el servidor",
            "error"
        );
    }finally{
        ocultarCarga();
    }
}

/*ENVIAR FORMULARIO DE EDICION DE DATOS INMOBILIARIA*/
/*EDITAR PROPIEDAD*/
formEditarPropiedad.addEventListener("submit", async function(evento){
    evento.preventDefault();
    const datos = {
        titulo: editarTitulo.value.trim(),
        id_estado_propiedad: editarEstadoPropiedad.value,
        precio: editarPrecio.value,
        provincia: editarProvincia.options[editarProvincia.selectedIndex].text,
        ciudad: editarCiudad.value,
        calle: editarCalle.value.trim(),
        altura: editarAltura.value,
        descripcion: editarDescripcion.value.trim()
    };
    const nuevasFotos = Array.from(editarFotos.files);
    const cantidadFinal = cantidadFotosActuales - fotosEliminar.length + nuevasFotos.length;

    if(cantidadFinal > 5){
        mensajeEditarPropiedad.textContent = "La propiedad no puede tener mas de 5 imagenes";
        mensajeEditarPropiedad.className = "mensajeError";
        return;
    }

    try{
        btnGuardarCambiosPropiedad.disabled = true;
        const res = await fetch(
            `${API_PROPIEDAD}?id_usuario=${id_usuario}&id_inmobiliaria=${id_inmobiliaria}&id_propiedad=${editarIdPropiedad.value}`,
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
            mensajeEditarPropiedad.textContent = respuesta.error || respuesta.mensaje || "No se pudo editar la propiedad";
            mensajeEditarPropiedad.className = "mensajeError";
            return;
        }
        for(const id_foto of fotosEliminar){
            const resFoto = await fetch(
                `${API_PROPIEDAD}?id_usuario=${id_usuario}&id_propiedad=${editarIdPropiedad.value}&id_foto=${id_foto}`,
                {
                    method: "DELETE",
                    headers: {
                        "Authorization": `Bearer ${token}`
                    }
                }
            );

            if(!validarSesionRespuesta(resFoto)){
                return;
            }
            if(!resFoto.ok){
                throw new Error("No se pudo eliminar una de las fotos");
            }
        }

        if(nuevasFotos.length > 0){
            const datosFotos = new FormData();

            nuevasFotos.forEach(function(archivo){
                datosFotos.append("fotos[]", archivo);
            });
            const resFotos = await fetch(
                `${API_PROPIEDAD}?id_usuario=${id_usuario}&id_propiedad=${editarIdPropiedad.value}&accion=fotos`,
                {
                    method: "POST",
                    headers: {
                        "Authorization": `Bearer ${token}`
                    },
                    body: datosFotos
                }
            );
            if(!validarSesionRespuesta(resFotos)){
                return;
            }
            if(!resFotos.ok){
                const respuestaFotos = await resFotos.json();
                throw new Error(respuestaFotos.error || "No se pudieron guardar las nuevas fotos");
            }
        }
        cerrarEditar();
        await cargarPropiedades();
        mostrarExito("Propiedad actualizada correctamente");
    }catch(error){
        console.error(error);
        mensajeEditarPropiedad.textContent = "No se pudo conectar con el servidor";
        mensajeEditarPropiedad.className = "mensajeError";
    }finally{
        btnGuardarCambiosPropiedad.disabled = false;
        ocultarCarga();
    }
});

/*CERRAMOS LA VENTANA DE EDICION*/
function cerrarEditarInmobiliaria(){
    mensajeEditarInmobiliaria.textContent = "";
    ventanaEditarInmobiliaria.classList.add("ocultarVentana");
}

/*MOSTRAR DATOS DE LA CUENTA*/
async function abrirMisDatos(){
    try{
        //mostrarCarga();
        const res = await fetch(
            `${API_USUARIO}?id_usuario=${id_usuario}`,
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
            mostrarMensaje("No se pudieron cargar los datos de la cuenta", "error");
            return;
        }
        datoNombreUsuario.textContent = datos.nombre_usuario;
        datoApellidoUsuario.textContent = datos.apellido_usuario;
        datoEmailUsuario.textContent = datos.email;
        ventanaMisDatos.classList.remove("ocultarVentana");
        
    }catch(error){
        console.error(error);
        mostrarMensaje("No se pudo conectar con el servidor", "error");
    }finally{
        ocultarCarga();
    }
}

btnCerrarMisDatos.addEventListener("click", function(){
    ventanaMisDatos.classList.add("ocultarVentana");
});

/*ABRIR EDITAR MIS DATOS*/
btnEditarMisDatos.addEventListener("click", function(){
    editarNombreUsuario.value = datoNombreUsuario.textContent;
    editarApellidoUsuario.value = datoApellidoUsuario.textContent;
    editarEmailUsuario.value = datoEmailUsuario.textContent;

    ventanaMisDatos.classList.add("ocultarVentana");
    ventanaEditarMisDatos.classList.remove("ocultarVentana");
});

/*CERRAR EDITAR MIS DATOS*/
function cerrarEditarMisDatos(){
    mensajeEditarUsuario.textContent = "";
    ventanaEditarMisDatos.classList.add("ocultarVentana");
}

btnCerrarEditarMisDatos.addEventListener("click", cerrarEditarMisDatos);
btnCancelarEditarUsuario.addEventListener("click", cerrarEditarMisDatos);

/*GUARDAR EDICION DE USUARIO*/
formEditarMisDatos.addEventListener("submit", async function(evento){
    evento.preventDefault();
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
        if(!validarSesionRespuesta(res)){
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

/*INICIAR*/
cargarDatosInmobiliaria();
cargarPropiedades();
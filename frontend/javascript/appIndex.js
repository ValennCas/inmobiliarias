const API_URL = "http://localhost/inmobiliarias/backend/controller/loginController.php";
const API_PROPIEDADES_PUBLICAS = "http://localhost/inmobiliarias/backend/controller/propiedadesPublicasController.php";
const API_INMOBILIARIAS_PUBLICAS = "http://localhost/inmobiliarias/backend/controller/inmobiliariaPublicaController.php";
const API_RECUPERAR_CONTRASENIA = "http://localhost/inmobiliarias/backend/controller/recuperarContraseniaPublica.php";
const URL_BACKEND = "http://localhost/inmobiliarias/backend/";

/*VARIABLES FORMULARIO LOGIN*/
const btnLogin = document.querySelector(".btnLogin");
const loginContenedor = document.querySelector("#contenedorLogin");
const btnCerrarLogin = document.querySelector(".cerrarLogin");
const formLogin = document.querySelector("#formLogin");
const msjLogin = document.querySelector("#mensajeLogin");
const btnEnviarLogin = document.querySelector("#btnEnviarLogin");
const cargandoLogin = document.querySelector("#cargandoLogin");

/*VARIABLES FORMULARIO FILTROS*/
const btnFiltros = document.querySelector(".filtros");
const filtrosContenedor = document.querySelector("#contenedorFiltros");

const formFiltros = document.querySelector("#formFiltros");
const filtroInmobiliaria = document.querySelector("#inmobiliaria");
const filtroProvincia = document.querySelector("#provincia");
const filtroCiudad = document.querySelector("#ciudad");
const filtroEstado = document.querySelector("#estadoPropiedad");
const btnLimpiar = document.querySelector("#btnLimpiar");
const mensajeInmobiliarias = document.querySelector("#mensajeInmobiliarias");
const mensajeUbicaciones = document.querySelector("#mensajeUbicaciones");

/*PROPIEDADES*/
const listaPropiedades = document.querySelector("#listaPropiedades");
const sinPropiedades = document.querySelector("#sinPropiedades");

/*VER PROPIEDAD*/
const ventanaVerPropiedad = document.querySelector("#ventanaVerPropiedad");
const btnCerrarVerPropiedad = document.querySelector("#btnCerrarVerPropiedad");
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
const btnContactarPropiedad = document.querySelector("#btnContactarPropiedad");

/*VARIABLES RECUPERAR CONTRASENIA*/
const btnOlvideContrasenia = document.querySelector("#btnOlvideContrasenia");
const ventanaRecuperarContrasenia = document.querySelector("#ventanaRecuperarContrasenia");
const btnCerrarRecuperar = document.querySelector("#btnCerrarRecuperar");

const pasoBuscarUsuario = document.querySelector("#pasoBuscarUsuario");
const formBuscarUsuario = document.querySelector("#formBuscarUsuario");
const usuarioRecuperar = document.querySelector("#usuarioRecuperar");
const mensajeBuscarUsuario = document.querySelector("#mensajeBuscarUsuario");
const btnBuscarUsuario = document.querySelector("#btnBuscarUsuario");
const btnCancelarRecuperar = document.querySelector("#btnCancelarRecuperar");

const pasoCodigo = document.querySelector("#pasoCodigo");
const formCodigo = document.querySelector("#formCodigo");
const codigoRecuperacion = document.querySelector("#codigoRecuperacion");
const mensajeCodigo = document.querySelector("#mensajeCodigo");
const btnVerificarCodigo = document.querySelector("#btnVerificarCodigo");
const btnCancelarCodigo = document.querySelector("#btnCancelarCodigo");

const pasoNuevaContrasenia = document.querySelector("#pasoNuevaContrasenia");
const formNuevaContrasenia = document.querySelector("#formNuevaContrasenia");
const nuevaContrasenia = document.querySelector("#nuevaContrasenia");
const repetirContrasenia = document.querySelector("#repetirContrasenia");
const mensajeNuevaContrasenia = document.querySelector("#mensajeNuevaContrasenia");
const btnCambiarContrasenia = document.querySelector("#btnCambiarContrasenia");
const btnCancelarNuevaContrasenia = document.querySelector("#btnCancelarNuevaContrasenia");

let usuarioRecuperacion = "";
let tokenRecuperacion = "";

let propiedadesPublicas = [];
let propiedadSeleccionada = null;
let imagenesPropiedad = [];
let indiceImagen = 0;

cargarProvincias(filtroProvincia, filtroCiudad);
filtroProvincia.addEventListener("change", function(){
    cargarCiudades(filtroProvincia.value, filtroCiudad);
});

/*ABRIR LOGIN*/
btnLogin.addEventListener("click", function(){
    loginContenedor.classList.toggle("loginOculto");
});

/*CERRAR LOGIN*/
btnCerrarLogin.addEventListener("click", function(){
    loginContenedor.classList.toggle("loginOculto");
});

/*FILTROS*/
btnFiltros.addEventListener("click", function(){
    filtrosContenedor.classList.toggle("filtrosMostrar");
});

/*LIMPIAR FILTROS*/
btnLimpiar.addEventListener("click", function(){
    filtroInmobiliaria.value = "";
    filtroProvincia.value = "";
    filtroCiudad.value = "";
    mostrarPropiedades(propiedadesPublicas);
});

/*LOGIN*/
formLogin.addEventListener("submit", async function(evento){
    evento.preventDefault();

    const usuario = document.querySelector("#usuario").value.trim();
    const password = document.querySelector("#password").value.trim();

    if(usuario == "" || password == ""){
        msjLogin.textContent = "Faltan completar los campos";
        msjLogin.className = "mensajeError";
        return;
    }

    if(usuario.length > 100){
        msjLogin.textContent = "El usuario es demasiado largo";
        msjLogin.className = "mensajeError";
        return;
    }

    if(password.length > 100){
        msjLogin.textContent = "La contraseña es demasiado larga";
        msjLogin.className = "mensajeError";
        return;
    }

    const datosLogin = {
        usuario: usuario,
        password: password
    };

    try{
        cargandoLogin.classList.remove("oculto");
        btnEnviarLogin.disabled = true;
        msjLogin.textContent = "";

        const res = await fetch(API_URL, {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify(datosLogin)
        });

        const datos = await res.json();

        if(!res.ok){
            msjLogin.textContent = datos.error;
            msjLogin.className = "mensajeError";
            return;
        }

        localStorage.setItem("token", datos.token);
        localStorage.setItem("id_usuario", datos.id_usuario);
        localStorage.setItem("id_inmobiliaria", datos.id_inmobiliaria);

        window.location.replace("inicioInmobiliaria.html");
    }catch(error){
        console.error(error);
        msjLogin.textContent = "Ocurrió un error al iniciar sesión";
        msjLogin.className = "mensajeError";
    }finally{
        cargandoLogin.classList.add("oculto");
        btnEnviarLogin.disabled = false;
    }
});

/*CARGAR PROPIEDADES EN ALQUILER Y EN VENTAS*/
async function cargarPropiedades(){
    try{
        const parametros = new URLSearchParams();
        if(filtroInmobiliaria.value !== ""){
            parametros.append("id_inmobiliaria", filtroInmobiliaria.value);
        }
        if(filtroProvincia.value !== ""){
            parametros.append("provincia", filtroProvincia.options[filtroProvincia.selectedIndex].text);
        }
        if(filtroCiudad.value !== ""){
            parametros.append("ciudad", filtroCiudad.value);
        }
        if(filtroEstado.value !== ""){
            parametros.append("estado", filtroEstado.value);
        }

        let url = API_PROPIEDADES_PUBLICAS;
        if(parametros.toString() !== ""){
            url += `?${parametros.toString()}`;
        }

        const res = await fetch(url);
        const datos = await res.json();

        if(!res.ok){
            mostrarSinPropiedades();
            return;
        }

        let propiedades = [];
        if(Array.isArray(datos)){
            propiedades = datos;
        }else if(Array.isArray(datos.propiedades)){
            propiedades = datos.propiedades;
        }else if(datos.id_propiedad){
            propiedades = [datos];
        }

        propiedadesPublicas = propiedades;
        mostrarPropiedades(propiedadesPublicas);
    }catch(error){
        console.error(error);
        mostrarSinPropiedades();
    }
}

/*MOSTRAR SIN PROPIEDADES*/
function mostrarSinPropiedades(){
    listaPropiedades.innerHTML = "";

    if(sinPropiedades){
        sinPropiedades.classList.remove("oculto");
    }
}

/*MOSTRAR PROPIEDADES*/
function mostrarPropiedades(propiedades){
    listaPropiedades.innerHTML = "";

    if(propiedades.length === 0){
        mostrarSinPropiedades();
        return;
    }

    if(sinPropiedades){
        sinPropiedades.classList.add("oculto");
    }

    propiedades.forEach(function(propiedad){
        const tarjeta = document.createElement("article");
        tarjeta.classList.add("tarjetaPropiedad");

        let imagen = "";

        if(propiedad.fotos && propiedad.fotos.length > 0){
            imagen = URL_BACKEND + propiedad.fotos[0].ruta_foto;
        }else if(propiedad.foto){
            imagen = URL_BACKEND + propiedad.foto;
        }

        tarjeta.innerHTML = `
            <img src="${imagen}" alt="${propiedad.titulo || "Propiedad"}" class="imagenPropiedad">
            <div class="contenidoPropiedad">
                <h3>${propiedad.titulo || "Propiedad"}</h3>
                <p>${propiedad.estado || ""}</p>
                <p> ${propiedad.precio ? "$" + Number(propiedad.precio).toLocaleString("es-AR") : ""}</p>
                <p>${propiedad.calle || ""} ${propiedad.altura || ""}</p>
                <div class="accionesPropiedad">
                    <button type="button" class="btnVerPropiedad" data-id="${propiedad.id_propiedad}">Ver</button>
                    <button type="button" class="btnContactar" data-id="${propiedad.id_propiedad}" data-inmobiliaria="${propiedad.id_inmobiliaria}">Contactar</button>
                </div>
            </div>
        `;
        listaPropiedades.appendChild(tarjeta);
    });

    agregarEventosPropiedades();
}

/*EVENTOS DE LAS TARJETAS*/
function agregarEventosPropiedades(){
    const botonesVer = document.querySelectorAll(".btnVerPropiedad");
    const botonesContactar = document.querySelectorAll(".btnContactar");

    botonesVer.forEach(function(boton){
        boton.addEventListener("click", function(){
            abrirPropiedad(boton.dataset.id);
        });
    });

    botonesContactar.forEach(function(boton){
        boton.addEventListener("click", function(){
            irContacto(boton.dataset.id, boton.dataset.inmobiliaria);
        });
    });
}

/*VER PROPIEDAD*/
function abrirPropiedad(id_propiedad){
    propiedadSeleccionada = propiedadesPublicas.find(function(propiedad){
        return String(propiedad.id_propiedad) === String(id_propiedad);
    });

    if(!propiedadSeleccionada){
        return;
    }

    verTituloPropiedad.textContent = propiedadSeleccionada.titulo || "Propiedad";
    verOperacion.textContent = propiedadSeleccionada.estado || "";
    verPrecio.textContent = propiedadSeleccionada.precio ? "$" + Number(propiedadSeleccionada.precio).toLocaleString("es-AR") : "";
    verDireccion.textContent = `${propiedadSeleccionada.calle || ""} ${propiedadSeleccionada.altura || ""}`;
    verCiudad.textContent = propiedadSeleccionada.ciudad || "";
    verProvincia.textContent = propiedadSeleccionada.provincia || "";
    verDescripcion.textContent = propiedadSeleccionada.descripcion || "";

    imagenesPropiedad = propiedadSeleccionada.fotos || [];
    indiceImagen = 0;

    mostrarImagenCarrusel();
    ventanaVerPropiedad.classList.remove("ocultarVentana");
}

/*CARRUSEL*/
function mostrarImagenCarrusel(){
    if(imagenesPropiedad.length === 0){
        fotoPropiedad.src = "img/sin-imagen.jpg";
        contadorFotos.textContent = "";
        btnFotoAnterior.style.display = "none";
        btnFotoSiguiente.style.display = "none";
        return;
    }

    fotoPropiedad.src = URL_BACKEND + imagenesPropiedad[indiceImagen].ruta_foto;
    contadorFotos.textContent = `${indiceImagen + 1} de ${imagenesPropiedad.length}`;

    if(imagenesPropiedad.length > 1){
        btnFotoAnterior.style.display = "block";
        btnFotoSiguiente.style.display = "block";
    }else{
        btnFotoAnterior.style.display = "none";
        btnFotoSiguiente.style.display = "none";
    }
}

btnFotoAnterior.addEventListener("click", function(){
    indiceImagen--;

    if(indiceImagen < 0){
        indiceImagen = imagenesPropiedad.length - 1;
    }

    mostrarImagenCarrusel();
});

btnFotoSiguiente.addEventListener("click", function(){
    indiceImagen++;

    if(indiceImagen >= imagenesPropiedad.length){
        indiceImagen = 0;
    }

    mostrarImagenCarrusel();
});

/*CERRAR VER PROPIEDAD*/
function cerrarVerPropiedad(){
    propiedadSeleccionada = null;
    imagenesPropiedad = [];
    indiceImagen = 0;
    ventanaVerPropiedad.classList.add("ocultarVentana");
}

btnCerrarVerPropiedad.addEventListener("click", cerrarVerPropiedad);

ventanaVerPropiedad.addEventListener("click", function(evento){
    if(evento.target === ventanaVerPropiedad){
        cerrarVerPropiedad();
    }
});

/*CONTACTAR*/
function irContacto(id_propiedad, id_inmobiliaria){
    window.location.href = `contacto.html?id_propiedad=${id_propiedad}&id_inmobiliaria=${id_inmobiliaria}`;
}

btnContactarPropiedad.addEventListener("click", function(){
    if(!propiedadSeleccionada){
        return;
    }

    irContacto(propiedadSeleccionada.id_propiedad, propiedadSeleccionada.id_inmobiliaria);
});

async function cargarInmobiliarias(){
    try{
        const res = await fetch(API_INMOBILIARIAS_PUBLICAS);
        const datos = await res.json();

        if(!res.ok){
            return;
        }

        let inmobiliarias = [];

        if(Array.isArray(datos)){
            inmobiliarias = datos;
        }else if(datos.id_inmobiliaria){
            inmobiliarias = [datos];
        }

        filtroInmobiliaria.innerHTML = `<option value="">Todas</option>`;
        inmobiliarias.forEach(function(inmobiliaria){
            const opcion = document.createElement("option");
            opcion.value = inmobiliaria.id_inmobiliaria;
            opcion.textContent = inmobiliaria.nombre;
            filtroInmobiliaria.appendChild(opcion);
        });
    }catch(error){
        mensajeInmobiliarias.textContent = "No se pueden consultar las inmobiliarias en este momento";
    }
}

/*CARGAR DATOS DE LOS FILTROS*/
function cargarFiltros(){
    const inmobiliarias = new Map();
    const provincias = new Set();

    filtroInmobiliaria.innerHTML = `<option value="">Todas</option>`;
    filtroProvincia.innerHTML = `<option value="">Todas</option>`;
    filtroCiudad.innerHTML = `<option value="">Todas</option>`;

    propiedadesPublicas.forEach(function(propiedad){
        if(propiedad.id_inmobiliaria && propiedad.nombre_inmobiliaria){
            inmobiliarias.set(propiedad.id_inmobiliaria, propiedad.nombre_inmobiliaria);
        }

        if(propiedad.provincia){
            provincias.add(propiedad.provincia);
        }
    });

    inmobiliarias.forEach(function(nombre, id){
        const opcion = document.createElement("option");
        opcion.value = id;
        opcion.textContent = nombre;
        filtroInmobiliaria.appendChild(opcion);
    });

    provincias.forEach(function(provincia){
        const opcion = document.createElement("option");
        opcion.value = provincia;
        opcion.textContent = provincia;
        filtroProvincia.appendChild(opcion);
    });
}

/*APLICAR FILTROS*/
formFiltros.addEventListener("submit", async function(evento){
    evento.preventDefault();

    await cargarPropiedades();
    filtroInmobiliaria.value = "";
    filtroProvincia.value = "";
    filtroCiudad.innerHTML = `<option value="">Seleccionar ciudad</option>`;
    filtroCiudad.disabled = true;
    filtroEstado.value = "";

    filtrosContenedor.classList.remove("filtrosMostrar");
});

/*ABRIR RECUPERAR CONTRASENIA*/
btnOlvideContrasenia.addEventListener("click", function(){
    loginContenedor.classList.add("loginOculto");
    ventanaRecuperarContrasenia.classList.remove("ocultarVentana");

    pasoBuscarUsuario.classList.remove("oculto");
    pasoCodigo.classList.add("oculto");
    pasoNuevaContrasenia.classList.add("oculto");

    formBuscarUsuario.reset();
    formCodigo.reset();
    formNuevaContrasenia.reset();

    mensajeBuscarUsuario.textContent = "";
    mensajeCodigo.textContent = "";
    mensajeNuevaContrasenia.textContent = "";

    usuarioRecuperacion = "";
    tokenRecuperacion = "";
});

/*CERRAR RECUPERAR CONTRASENIA*/
function cerrarRecuperarContrasenia(){
    ventanaRecuperarContrasenia.classList.add("ocultarVentana");

    pasoBuscarUsuario.classList.remove("oculto");
    pasoCodigo.classList.add("oculto");
    pasoNuevaContrasenia.classList.add("oculto");

    formBuscarUsuario.reset();
    formCodigo.reset();
    formNuevaContrasenia.reset();

    mensajeBuscarUsuario.textContent = "";
    mensajeCodigo.textContent = "";
    mensajeNuevaContrasenia.textContent = "";

    usuarioRecuperacion = "";
    tokenRecuperacion = "";
}

btnCerrarRecuperar.addEventListener("click", cerrarRecuperarContrasenia);
btnCancelarRecuperar.addEventListener("click", cerrarRecuperarContrasenia);
btnCancelarCodigo.addEventListener("click", cerrarRecuperarContrasenia);
btnCancelarNuevaContrasenia.addEventListener("click", cerrarRecuperarContrasenia);

ventanaRecuperarContrasenia.addEventListener("click", function(evento){
    if(evento.target === ventanaRecuperarContrasenia){
        cerrarRecuperarContrasenia();
    }
});

/*SOLICITAR CODIGO*/
formBuscarUsuario.addEventListener("submit", async function(evento){
    evento.preventDefault();

    const usuario = usuarioRecuperar.value.trim();

    if(usuario === ""){
        mensajeBuscarUsuario.textContent = "Ingresá tu correo electrónico o nombre de usuario";
        mensajeBuscarUsuario.className = "mensajeError";
        return;
    }

    if(usuario.length > 100){
        mensajeBuscarUsuario.textContent = "El dato ingresado es demasiado largo";
        mensajeBuscarUsuario.className = "mensajeError";
        return;
    }

    try{
        btnBuscarUsuario.disabled = true;
        mensajeBuscarUsuario.textContent = "";

        const res = await fetch(API_RECUPERAR_CONTRASENIA, {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify({
                accion: "solicitarCodigo",
                usuario: usuario
            })
        });

        const datos = await res.json();

        if(!res.ok){
            mensajeBuscarUsuario.textContent = datos.error || "No se pudo enviar el código";
            mensajeBuscarUsuario.className = "mensajeError";
            return;
        }

        usuarioRecuperacion = usuario;

        pasoBuscarUsuario.classList.add("oculto");
        pasoCodigo.classList.remove("oculto");
    }catch(error){
        console.error(error);
        mensajeBuscarUsuario.textContent = "No se pudo conectar con el servidor";
        mensajeBuscarUsuario.className = "mensajeError";
    }finally{
        btnBuscarUsuario.disabled = false;
    }
});

/*VERIFICAR CODIGO*/
formCodigo.addEventListener("submit", async function(evento){
    evento.preventDefault();

    const codigo = codigoRecuperacion.value.trim();

    if(codigo === ""){
        mensajeCodigo.textContent = "Ingresá el código recibido";
        mensajeCodigo.className = "mensajeError";
        return;
    }

    if(!/^\d{4}$/.test(codigo)){
        mensajeCodigo.textContent = "El código debe tener 4 números";
        mensajeCodigo.className = "mensajeError";
        return;
    }

    try{
        btnVerificarCodigo.disabled = true;
        mensajeCodigo.textContent = "";

        const res = await fetch(API_RECUPERAR_CONTRASENIA, {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify({
                accion: "verificarCodigo",
                usuario: usuarioRecuperacion,
                codigo: codigo
            })
        });

        const datos = await res.json();

        if(!res.ok){
            mensajeCodigo.textContent = datos.error || "El código es incorrecto o venció";
            mensajeCodigo.className = "mensajeError";
            return;
        }

        tokenRecuperacion = datos.token_recuperacion;

        pasoCodigo.classList.add("oculto");
        pasoNuevaContrasenia.classList.remove("oculto");
    }catch(error){
        console.error(error);
        mensajeCodigo.textContent = "No se pudo conectar con el servidor";
        mensajeCodigo.className = "mensajeError";
    }finally{
        btnVerificarCodigo.disabled = false;
    }
});

/*CAMBIAR CONTRASENIA*/
formNuevaContrasenia.addEventListener("submit", async function(evento){
    evento.preventDefault();

    const password = nuevaContrasenia.value.trim();
    const repetirPassword = repetirContrasenia.value.trim();

    if(password === "" || repetirPassword === ""){
        mensajeNuevaContrasenia.textContent = "Faltan completar los campos";
        mensajeNuevaContrasenia.className = "mensajeError";
        return;
    }

    if(password !== repetirPassword){
        mensajeNuevaContrasenia.textContent = "Las contraseñas no coinciden";
        mensajeNuevaContrasenia.className = "mensajeError";
        return;
    }

    if(password.length < 6){
        mensajeNuevaContrasenia.textContent = "La contraseña debe tener al menos 6 caracteres";
        mensajeNuevaContrasenia.className = "mensajeError";
        return;
    }

    if(password.length > 100){
        mensajeNuevaContrasenia.textContent = "La contraseña es demasiado larga";
        mensajeNuevaContrasenia.className = "mensajeError";
        return;
    }

    if(tokenRecuperacion === ""){
        mensajeNuevaContrasenia.textContent = "La recuperación no fue verificada";
        mensajeNuevaContrasenia.className = "mensajeError";
        return;
    }

    try{
        btnCambiarContrasenia.disabled = true;
        mensajeNuevaContrasenia.textContent = "";

        const res = await fetch(API_RECUPERAR_CONTRASENIA, {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify({
                accion: "cambiarContrasenia",
                token_recuperacion: tokenRecuperacion,
                password: password
            })
        });

        const datos = await res.json();

        if(!res.ok){
            mensajeNuevaContrasenia.textContent = datos.error || "No se pudo cambiar la contraseña";
            mensajeNuevaContrasenia.className = "mensajeError";
            return;
        }

        mensajeNuevaContrasenia.textContent = datos.mensaje || "Contraseña actualizada correctamente";
        mensajeNuevaContrasenia.className = "mensajeExito";
        formNuevaContrasenia.reset();

        setTimeout(function(){
            cerrarRecuperarContrasenia();
            loginContenedor.classList.remove("loginOculto");
        }, 1500);
    }catch(error){
        console.error(error);
        mensajeNuevaContrasenia.textContent = "No se pudo conectar con el servidor";
        mensajeNuevaContrasenia.className = "mensajeError";
    }finally{
        btnCambiarContrasenia.disabled = false;
    }
});

/*INICIAR*/
cargarInmobiliarias();
cargarPropiedades();





const API_INMOBILIARIA = BASE_BACKEND + "controller/inmobiliariaController.php";

const token = localStorage.getItem("token");
const id_usuario = localStorage.getItem("id_usuario");

/*VALIDAR SESION*/
if(!token || !id_usuario){
    window.location.replace("index.html");
}

window.addEventListener("pageshow", function(evento){
    const navegacion = performance.getEntriesByType("navigation")[0];
    if(evento.persisted || navegacion?.type === "back_forward"){
        localStorage.clear();
        window.location.replace("index.html");
        return;
    }
    const tokenActual = localStorage.getItem("token");
    const idUsuarioActual = localStorage.getItem("id_usuario");

    if(!tokenActual || !idUsuarioActual){
        window.location.replace("index.html");
    }
});

/*ELEMENTOS*/
const btnCerrarSesion = document.querySelector("#btnCerrarSesion");

const sinInmobiliaria = document.querySelector("#sinInmobiliaria");
const btnCrearInmobiliaria = document.querySelector("#btnCrearInmobiliaria");
const btnAgregarInmobiliaria = document.querySelector("#btnAgregarInmobiliaria");
const inmobiliariaVentana = document.querySelector("#inmobiliariaventana");
const formInmobiliaria = document.querySelector("#formInmobiliaria");
const provinciaSelect = document.querySelector("#provincia");
const ciudadSelect = document.querySelector("#ciudad");
const btnGuardarInmobiliaria = document.querySelector("#btnGuardarInmobiliaria");
const btnCancelarInmobiliaria = document.querySelector("#btnCancelarInmobiliaria");
const mensajeInmobiliaria = document.querySelector("#mensajeInmobiliaria");
const listaInmobiliarias = document.querySelector("#listaInmobiliarias");
const cargandoPantalla = document.querySelector("#cargandoPantalla");

cargarProvincias(provinciaSelect, ciudadSelect);
provinciaSelect.addEventListener("change", function(){
    cargarCiudades(provinciaSelect.value, ciudadSelect);
});

/*CERRAR SESION*/
btnCerrarSesion.addEventListener("click", function(){
    localStorage.clear();
    window.location.replace("index.html");
})

/*MOSTRAR CARGA*/
function mostrarCarga(){
    cargandoPantalla.classList.remove("oculto");
}

/*OCULTAR CARGA*/
function ocultarCarga(){
    cargandoPantalla.classList.add("oculto");
}

/*ABRIR VENTANA*/
function abrirFormularioInmobiliaria(){
    mensajeInmobiliaria.textContent = "";
    inmobiliariaVentana.classList.remove("ocultarVentana");
}

if(btnCrearInmobiliaria){
    btnCrearInmobiliaria.addEventListener("click", abrirFormularioInmobiliaria);
}

if(btnAgregarInmobiliaria){
    btnAgregarInmobiliaria.addEventListener("click", abrirFormularioInmobiliaria);
}

/*CANCELAR FORMULARIO*/
btnCancelarInmobiliaria.addEventListener("click", function(){
    formInmobiliaria.reset();
    mensajeInmobiliaria.textContent = "";
    inmobiliariaVentana.classList.add("ocultarVentana");
});

/*CERRAR VENTANA TOCANDO FUERA*/
inmobiliariaVentana.addEventListener("click", function(evento){
    if(evento.target === inmobiliariaVentana){
        inmobiliariaVentana.classList.add("ocultarVentana");
        mensajeInmobiliaria.textContent = "";
    }
});

/*CARGAR INMOBILIARIAS*/
async function cargarInmobiliarias(){
    try{
        mostrarCarga();
        const res = await fetch(
            `${API_INMOBILIARIA}?id_usuario=${id_usuario}`,
            {
                method: "GET",
                headers: {
                    "Authorization": `Bearer ${token}`
                }
            }
        );

        const datos = await res.json();
        listaInmobiliarias.innerHTML = "";
        if(res.status === 401){
            localStorage.clear();
            window.location.href = "index.html";
            return;
        }

        if(res.status === 404){
            mostrarSinInmobiliarias();
            return;
        }

        if(!res.ok){
            listaInmobiliarias.innerHTML = `
                <p class="mensajeError">
                    No se pudieron cargar las inmobiliarias.
                </p>
            `;
            return;
        }
        mostrarInmobiliarias(datos);
    }catch(error){
        console.error(error);
        listaInmobiliarias.innerHTML = `
            <p class="mensajeError">
                No se pudo conectar con el servidor.
            </p>
        `;
    }finally{
        ocultarCarga();
    }
}

/*MOSTRAR MENSAJE SIN INMOBILIARIAS*/
function mostrarSinInmobiliarias(){
    sinInmobiliaria.classList.remove("oculto");
    listaInmobiliarias.innerHTML = `
        <p class="sinResultados">
            Aún no posee inmobiliarias agregadas.
        </p>
    `;
}

/*MOSTRAR INMOBILIARIAS*/
function mostrarInmobiliarias(datos){
    sinInmobiliaria.classList.add("oculto");
    let inmobiliarias;
    if(Array.isArray(datos)){
        inmobiliarias = datos;
    }else if(datos.inmobiliaria){
        inmobiliarias = [datos.inmobiliaria];
    }else{
        inmobiliarias = [datos];
    }

    if(inmobiliarias.length === 0){
        mostrarSinInmobiliarias();
        return;
    }

    listaInmobiliarias.innerHTML = "";

    inmobiliarias.forEach(function(inmobiliaria){
        const tarjeta = document.createElement("article");
        tarjeta.classList.add("inmobiliaria");
        tarjeta.innerHTML = `
            <div class="datosInmobiliaria">
                <h3>${inmobiliaria.nombre}</h3>
                <p>
                    ${inmobiliaria.ciudad},
                    ${inmobiliaria.provincia}
                </p>
                <p>${inmobiliaria.email}</p>
            </div>
            <button
                type="button"
                class="btnIngresarInmobiliaria"
                data-id="${inmobiliaria.id_inmobiliaria}">
                <span>Ingresar</span>
                <span class="flecha">→</span>
            </button>
        `;
        listaInmobiliarias.appendChild(tarjeta);
    });
    agregarEventosInmobiliarias();
}

/*INGRESAR A UNA INMOBILIARIA*/
function agregarEventosInmobiliarias(){
    const botones = document.querySelectorAll(".btnIngresarInmobiliaria");
    botones.forEach(function(boton){
        boton.addEventListener("click", function(){
            const id_inmobiliaria = boton.dataset.id;
            localStorage.setItem("id_inmobiliaria", id_inmobiliaria);
            window.location.href = "inmobiliaria.html";
        });
    });
}

/*REGISTRAR INMOBILIARIA*/
formInmobiliaria.addEventListener("submit", async function(evento){

    evento.preventDefault();

    const nombre = document.querySelector("#nombre").value.trim();
    const email = document.querySelector("#email").value.trim();
    const area_telefono = document.querySelector("#area_telefono").value.trim();
    const telefono = document.querySelector("#telefono").value.trim();
    const provincia = document.querySelector("#provincia").value.trim();
    const ciudad = document.querySelector("#ciudad").value.trim();
    const calle = document.querySelector("#calle").value.trim();
    const altura = document.querySelector("#altura").value.trim();

    if(nombre === "" || email === "" || area_telefono === "" || telefono === "" || provincia === "" || ciudad === "" || calle === "" || altura === ""){
        mensajeInmobiliaria.textContent = "Faltan completar campos";
        mensajeInmobiliaria.className = "mensajeError";
        return;
    }

    if(area_telefono.length > 10){
        mensajeInmobiliaria.textContent = "El código de área no puede superar los 10 caracteres";
        mensajeInmobiliaria.className = "mensajeError";
        return;
    }

    if(telefono.length > 10){
        mensajeInmobiliaria.textContent = "El teléfono no puede superar los 10 caracteres";
        mensajeInmobiliaria.className = "mensajeError";
        return;
    }

    if(!email.includes("@")){
        mensajeInmobiliaria.textContent = "Ingrese un correo electrónico válido";
        mensajeInmobiliaria.className = "mensajeError";
        return;
    }

    const datosInmobiliaria = {
        nombre: nombre,
        email: email,
        area_telefono: area_telefono,
        telefono: telefono,
        provincia: provincia,
        ciudad: ciudad,
        calle: calle,
        altura: altura
    };

    try{
        mostrarCarga();
        btnGuardarInmobiliaria.disabled = true;
        mensajeInmobiliaria.textContent = "";
        const res = await fetch(
            `${API_INMOBILIARIA}?id_usuario=${id_usuario}`,
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Authorization": `Bearer ${token}`
                },
                body: JSON.stringify(datosInmobiliaria)
            }
        );

        const datos = await res.json();

        if(res.status === 401){
            localStorage.clear();
            window.location.href = "index.html";
            return;
        }

        if(!res.ok){
            mensajeInmobiliaria.textContent =
                datos.error ||
                datos.mensaje ||
                "No se pudo registrar la inmobiliaria";
            mensajeInmobiliaria.className = "mensajeError";
            return;
        }

        /*LIMPIAR SOLO SI SALIO BIEN*/
        formInmobiliaria.reset();
        mensajeInmobiliaria.textContent = "";
        mensajeInmobiliaria.className = "";
        /*CERRAR VENTANA*/
        inmobiliariaVentana.classList.add("ocultarVentana");
        /*ACTUALIZAR LISTADO*/
        await cargarInmobiliarias();
    }catch(error){
        console.error(error);
        mensajeInmobiliaria.textContent =
            "Ocurrió un error al registrar la inmobiliaria";
        mensajeInmobiliaria.className = "mensajeError";
    }finally{
        btnGuardarInmobiliaria.disabled = false;
        ocultarCarga();
    }
});

/*CARGAR AL ENTRAR*/
cargarInmobiliarias();
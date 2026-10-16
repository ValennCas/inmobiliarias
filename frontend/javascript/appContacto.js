const API_CONTACTO = "http://localhost/inmobiliarias/backend/controller/contactoController.php";

/*DATOS DEL LINK (?id_propiedad=3&id_inmobiliaria=1)*/
const parametros = new URLSearchParams(window.location.search);
const id_propiedad = parametros.get("id_propiedad");
const id_inmobiliaria = parametros.get("id_inmobiliaria");

/*ELEMENTOS*/
const resumenPropiedad = document.querySelector("#resumenPropiedad");
const mensajeEstado = document.querySelector("#mensajeEstado");
const formContacto = document.querySelector("#formContacto");
const campoContacto = document.querySelector("#contacto");
const campoDescripcion = document.querySelector("#descripcion");
const contador = document.querySelector("#contador");
const mensajeContacto = document.querySelector("#mensajeContacto");
const btnEnviarContacto = document.querySelector("#btnEnviarContacto");

function mostrarErrorEstado(texto){
    mensajeEstado.textContent = texto;
    mensajeEstado.className = "mensajeEstado mensajeError";
}

/*CARGAR LA PROPIEDAD Y SU INMOBILIARIA*/
async function cargarPropiedad(){
    if(!id_propiedad || !id_inmobiliaria){
        mostrarErrorEstado("El enlace no es válido. Volvé a las propiedades y elegí una.");
        return;
    }

    try{
        const res = await fetch(
            `${API_CONTACTO}?id_inmobiliaria=${encodeURIComponent(id_inmobiliaria)}&id_propiedad=${encodeURIComponent(id_propiedad)}`
        );
        const datos = await res.json();

        if(!res.ok){
            mostrarErrorEstado(datos.error || "No se pudo cargar la propiedad.");
            return;
        }

        document.querySelector("#resumenDireccion").textContent =
            `${datos.calle} ${datos.altura}, ${datos.ciudad}, ${datos.provincia}`;
        document.querySelector("#resumenDescripcion").textContent = datos.descripcion;
        document.querySelector("#resumenPrecio").textContent =
            "$" + Number(datos.precio).toLocaleString("es-AR");
        document.querySelector("#resumenInmobiliaria").textContent = datos.nombre_inmobiliaria;

        mensajeEstado.classList.add("oculto");
        resumenPropiedad.classList.remove("oculto");
        formContacto.classList.remove("oculto");
    }
    catch(error){
        console.error(error);
        mostrarErrorEstado("No se pudo conectar con el servidor. Intentá más tarde.");
    }
}

/*CONTADOR DE CARACTERES*/
campoDescripcion.addEventListener("input", function(){
    contador.textContent = `${campoDescripcion.value.length} / ${campoDescripcion.maxLength}`;
});

/*ENVIAR CONSULTA*/
formContacto.addEventListener("submit", async function(evento){
    evento.preventDefault();

    const contacto = campoContacto.value.trim();
    const descripcion = campoDescripcion.value.trim();

    if(contacto === "" || descripcion === ""){
        mensajeContacto.textContent = "Faltan completar los campos";
        mensajeContacto.className = "mensajeError";
        return;
    }

    try{
        btnEnviarContacto.disabled = true;
        btnEnviarContacto.textContent = "Enviando...";
        mensajeContacto.textContent = "";

        const res = await fetch(API_CONTACTO, {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify({
                id_inmobiliaria: Number(id_inmobiliaria),
                id_propiedad: Number(id_propiedad),
                contacto: contacto,
                descripcion: descripcion
            })
        });
        const datos = await res.json();

        if(!res.ok){
            mensajeContacto.textContent = datos.error || "No se pudo enviar la consulta";
            mensajeContacto.className = "mensajeError";
            btnEnviarContacto.disabled = false;
            btnEnviarContacto.textContent = "Enviar consulta";
            return;
        }

        mensajeContacto.textContent = datos.mensaje;
        mensajeContacto.className = "mensajeExito";
        formContacto.reset();
        contador.textContent = `0 / ${campoDescripcion.maxLength}`;
        btnEnviarContacto.textContent = "Consulta enviada";
    }
    catch(error){
        console.error(error);
        mensajeContacto.textContent = "Ocurrió un error al enviar la consulta";
        mensajeContacto.className = "mensajeError";
        btnEnviarContacto.disabled = false;
        btnEnviarContacto.textContent = "Enviar consulta";
    }
});

cargarPropiedad();

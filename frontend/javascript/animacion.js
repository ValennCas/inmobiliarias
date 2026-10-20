const cargandoPantalla = document.querySelector("#cargandoPantalla");
const ventanaResultado = document.querySelector("#ventanaResultado");
const iconoResultado = document.querySelector("#iconoResultado");
const tituloResultado = document.querySelector("#tituloResultado");
const textoResultado = document.querySelector("#textoResultado");

// Muestra la pantalla de carga
function mostrarCarga(){
    cargandoPantalla.classList.remove("oculto");
}

// Oculta la pantalla de carga
function ocultarCarga(){
    cargandoPantalla.classList.add("oculto");
}

// Muestra el mensaje de operacion exitosa
function mostrarExito(mensaje){
    iconoResultado.textContent = "✓";
    tituloResultado.textContent = "Operación realizada";
    textoResultado.textContent = mensaje;
    ventanaResultado.classList.remove("ocultarVentana");

    setTimeout(function(){
        ventanaResultado.classList.add("ocultarVentana");
    }, 3000);
}

// Muestra un mensaje de error
function mostrarError(mensaje){
    iconoResultado.textContent = "×";
    tituloResultado.textContent = "Ocurrió un error";
    textoResultado.textContent = mensaje;
    ventanaResultado.classList.remove("ocultarVentana");

    setTimeout(function(){
        ventanaResultado.classList.add("ocultarVentana");
    }, 3000);
}


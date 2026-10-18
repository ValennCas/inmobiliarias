const cargandoPantalla = document.querySelector("#cargandoPantalla");
const ventanaResultado = document.querySelector("#ventanaResultado");
const iconoResultado = document.querySelector("#iconoResultado");
const tituloResultado = document.querySelector("#tituloResultado");
const textoResultado = document.querySelector("#textoResultado");

function mostrarCarga(){
    cargandoPantalla.classList.remove("oculto");
}

function ocultarCarga(){
    cargandoPantalla.classList.add("oculto");
}

function mostrarExito(mensaje){
    iconoResultado.textContent = "✓";
    tituloResultado.textContent = "Operación realizada";
    textoResultado.textContent = mensaje;
    ventanaResultado.classList.remove("ocultarVentana");

    setTimeout(function(){
        console.log("CIERRO RESULTADO POR TIMEOUT", Date.now());
        ventanaResultado.classList.add("ocultarVentana");
    }, 3000);
}

function mostrarError(mensaje){
    iconoResultado.textContent = "×";
    tituloResultado.textContent = "Ocurrió un error";
    textoResultado.textContent = mensaje;
    ventanaResultado.classList.remove("ocultarVentana");

    setTimeout(function(){
        ventanaResultado.classList.add("ocultarVentana");
    }, 3000);
}


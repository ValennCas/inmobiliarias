const API_URL = "http://localhost/inmobiliarias/backend/controller/loginController.php";

/*variables formulario login*/
const btnLogin = document.querySelector(".btnLogin");
const loginContenedor = document.querySelector("#contenedorLogin");
const btnCerrarLogin = document.querySelector(".cerrarLogin");
const formLogin = document.querySelector("#formLogin");
const msjLogin = document.querySelector("#mensajeLogin");

/*variables formulario filtros*/
const btnFiltros = document.querySelector(".filtros");
const filtrosContenedor = document.querySelector("#contenedorFiltros");
const btnLimpiar = document.querySelector("#btnLimpiar");

btnLogin.addEventListener("click", function(){
    loginContenedor.classList.toggle("loginOculto");
});

btnCerrarLogin.addEventListener("click", function(){
    loginContenedor.classList.toggle("loginOculto");
});

btnFiltros.addEventListener("click", function(){
    filtrosContenedor.classList.toggle("filtrosMostrar")
});

btnLimpiar.addEventListener("click", function(){
    document.querySelector("#inmobiliaria").value = "";
    document.querySelector("#provincia").value = "";
    document.querySelector("#ciudad").value = "";
});
    
formLogin.addEventListener("submit", async function(evento){
    evento.preventDefault();
    const usuario = document.querySelector("#usuario").value.trim();
    const password = document.querySelector("#password").value.trim();
    
    if(usuario == "" || password == ""){
        msjLogin.textContent = "Faltan completar los campos";
        return;
    }
    if (usuario.length > 100) {
        msjLogin.textContent = "El usuario es demasiado largo";
        return;
    }
    if (password.length > 100) {
        msjLogin.textContent = "La contraseña es demasiado larga";
        return;
    }

    const datosLogin = {
        usuario: usuario,
        password: password
    }

    try{
        const res = await fetch(
        `${API_URL}?recurso=login`, {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify(datosLogin)
        }
    );
    const datos = await res.json()
    console.log(datos)
   
    if(!res.ok){
        msjLogin.textContent = datos.error;
        msjLogin.className = "mensajeError";
        return;
    }

    localStorage.setItem("token", datos.token);
    msjLogin.textContent = "Inicio de sesión correcto";
    msjLogin.className = "mensajeExito";
    }
    catch(error){
        console.error(error);
        mensajeLogin.textContent = "Ocurrió un error al iniciar sesión";
        mensajeLogin.className = "mensajeError";
    }
});





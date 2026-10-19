// Direccion base del backend (siempre termina en "/").
// En tu maquina (XAMPP en el puerto 80 o Live Server en el 5500) apunta a localhost.
// En un servidor en linea (Render) el front y el backend salen del mismo dominio.
const BASE_BACKEND = (["localhost", "127.0.0.1"].includes(window.location.hostname) && ["", "80", "5500"].includes(window.location.port))
    ? "http://localhost/inmobiliarias/backend/"
    : window.location.origin + "/backend/";

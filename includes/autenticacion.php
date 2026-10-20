<?php
// Sesión del usuario: saber quién entró, abrir la sesión y pedir sesión en las páginas privadas.

if (session_status() != PHP_SESSION_ACTIVE) {
    session_start();
}

// Devuelve el usuario que inició sesión, o null si no hay nadie.
function usuarioActual()
{
    if (isset($_SESSION["usuario"])) {
        return $_SESSION["usuario"];
    }
    return null;
}

// Guarda en la sesión los datos del usuario que entró.
function iniciarSesionUsuario($usuario)
{
    $_SESSION["usuario"] = array(
        "id" => (int) $usuario["id"],
        "nombre" => $usuario["nombre"],
        "correo" => $usuario["correo"]
    );
}

// Si no hay sesión, manda al login y recuerda a qué página se quería entrar.
function requerirSesion($aviso)
{
    if (usuarioActual() == null) {
        header("Location: login.php?aviso=" . $aviso . "&volver=" . basename($_SERVER["PHP_SELF"]));
        exit;
    }
}

// Página a la que se vuelve después de iniciar sesión (por ejemplo "lista.html?genero=2").
// Solo se aceptan páginas de esta misma carpeta: si trae "/" (otra carpeta u otra web) se va al inicio.
function paginaDeRegreso()
{
    $volver = texto($_GET, "volver");
    if ($volver == "" || strpos($volver, "/") !== false || strpos($volver, "\\") !== false) {
        return "index.html";
    }
    return $volver;
}

// Agrega ?volver=... a los enlaces entre login y registro, para no perder la página.
function parametroRegreso()
{
    if (isset($_GET["volver"])) {
        return "?volver=" . urlencode(paginaDeRegreso());
    }
    return "";
}

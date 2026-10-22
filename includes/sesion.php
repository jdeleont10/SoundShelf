<?php
// Sesión del usuario. Todas las páginas PHP incluyen este archivo en lugar de llamar a session_start().

// Los errores de PHP no se muestran en la página (quedan guardados en el log de XAMPP).
ini_set("display_errors", "0");
ini_set("log_errors", "1");

// La cookie de la sesión no la puede leer JavaScript ("httponly")
// y no se envía en formularios que vienen de otros sitios ("samesite").
session_set_cookie_params(array("httponly" => true, "samesite" => "Lax"));
session_start();

// La sesión se cierra sola después de 30 minutos sin actividad.
if (isset($_SESSION["usuario_id"]) && isset($_SESSION["ultima_actividad"])) {
    if (time() - $_SESSION["ultima_actividad"] > 30 * 60) {
        $_SESSION = array();
        session_regenerate_id(true);
        $_SESSION["sesion_vencida"] = true;
    }
}
$_SESSION["ultima_actividad"] = time();

// Código secreto (token) que va en cada formulario.
// Sirve para comprobar que el formulario se envió desde SoundShelf y no desde otra página.
if (!isset($_SESSION["token"])) {
    $_SESSION["token"] = bin2hex(random_bytes(16));
}

// Revisa que el formulario trajo el token correcto.
function tokenValido()
{
    return isset($_POST["token"]) && is_string($_POST["token"]) && hash_equals($_SESSION["token"], $_POST["token"]);
}

// Solo acepta direcciones de páginas de SoundShelf, como "lista.html?genero=2".
// Si es otra cosa (por ejemplo otra web), devuelve la página por defecto.
function paginaSegura($pagina, $porDefecto)
{
    if (is_string($pagina) && preg_match('/^[a-z-]+\.(html|php)(\?[a-zA-Z0-9=&%_.-]*)?$/', $pagina)) {
        return $pagina;
    }
    return $porDefecto;
}

// Página a la que se vuelve después de iniciar sesión (viene en ?volver=...).
function paginaDeRegreso()
{
    if (isset($_GET["volver"])) {
        return paginaSegura($_GET["volver"], "index.html");
    }
    return "index.html";
}

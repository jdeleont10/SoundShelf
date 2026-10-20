<?php
// Sesión del usuario: cookie segura, cierre por inactividad, token CSRF y control de acceso.

define("MINUTOS_SESION", 30); // la sesión se cierra sola después de 30 minutos sin actividad

// Iniciar la sesión con una cookie segura.
if (session_status() != PHP_SESSION_ACTIVE) {
    $usaHttps = isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] != "" && $_SERVER["HTTPS"] != "off";
    session_set_cookie_params(array(
        "httponly" => true,     // JavaScript no puede leer la cookie de la sesión
        "secure" => $usaHttps,  // con HTTPS, la cookie solo viaja cifrada
        "samesite" => "Lax",    // otros sitios no pueden enviar la cookie en sus formularios
        "path" => "/"
    ));
    session_start();
}

// Si pasaron más de 30 minutos desde la última página visitada, se cierra la sesión.
if (isset($_SESSION["usuario"]) && isset($_SESSION["ultima_actividad"])) {
    $segundosSinActividad = time() - $_SESSION["ultima_actividad"];
    if ($segundosSinActividad > MINUTOS_SESION * 60) {
        unset($_SESSION["usuario"]);
        session_regenerate_id(true);
        $_SESSION["sesion_expirada"] = true;
    }
}
$_SESSION["ultima_actividad"] = time();

// Devuelve el usuario que inició sesión, o null si no hay nadie.
function usuarioActual()
{
    if (isset($_SESSION["usuario"])) {
        return $_SESSION["usuario"];
    }
    return null;
}

// Código secreto que va en cada formulario para comprobar que se envió desde SoundShelf (protección CSRF).
function tokenCsrf()
{
    if (!isset($_SESSION["csrf"])) {
        $_SESSION["csrf"] = bin2hex(random_bytes(32));
    }
    return $_SESSION["csrf"];
}

// Revisa que el formulario trae el código correcto.
function verificarCsrf()
{
    if (!isset($_POST["csrf"]) || !is_string($_POST["csrf"])) {
        return false;
    }
    return hash_equals(tokenCsrf(), $_POST["csrf"]);
}

// Abre la sesión del usuario. Se cambia el id de sesión para que nadie pueda reutilizar uno viejo.
function iniciarSesionUsuario($usuario)
{
    session_regenerate_id(true);
    $_SESSION["usuario"] = array(
        "id" => (int) $usuario["id"],
        "nombre" => $usuario["nombre"],
        "correo" => $usuario["correo"]
    );
    $_SESSION["ultima_actividad"] = time();
}

// Si no hay sesión, manda al login y recuerda a qué página se quería entrar.
function requerirSesion($aviso = "canciones")
{
    if (usuarioActual() != null) {
        return;
    }
    $volver = basename($_SERVER["SCRIPT_NAME"]);
    if (isset($_SERVER["QUERY_STRING"]) && $_SERVER["QUERY_STRING"] != "") {
        $volver = $volver . "?" . $_SERVER["QUERY_STRING"];
    }
    header("Location: login.php?aviso=" . urlencode($aviso) . "&volver=" . urlencode($volver));
    exit;
}

// Página a la que se vuelve después de iniciar sesión.
// Solo se aceptan páginas del propio sitio (por ejemplo "lista.html?genero=2"), nunca otra web.
function paginaDeRegreso()
{
    $volver = texto($_GET, "volver");
    if (preg_match('/^[a-z0-9-]+\.(php|html)(\?[A-Za-z0-9=&%._+-]*)?$/', $volver)) {
        return $volver;
    }
    return "index.html";
}

// Agrega ?volver=... a los enlaces entre login y registro, para no perder la página.
function parametroRegreso()
{
    if (isset($_GET["volver"])) {
        return "?volver=" . urlencode(paginaDeRegreso());
    }
    return "";
}

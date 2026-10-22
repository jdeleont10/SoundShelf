<?php
// Cerrar sesión: se borra la sesión y se vuelve al inicio.
// Solo funciona con el botón "Cerrar sesión" (un formulario con el token),
// así otra página no puede cerrarte la sesión con un simple enlace.
include "includes/sesion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && tokenValido()) {
    $_SESSION = array();
    session_destroy();
}

header("Location: index.html");
exit;

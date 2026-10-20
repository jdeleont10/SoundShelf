<?php
// Cerrar sesión. Solo se acepta el formulario del botón "Cerrar sesión" (POST con el token CSRF).
require_once __DIR__ . "/includes/autenticacion.php";

if ($_SERVER["REQUEST_METHOD"] != "POST" || !verificarCsrf()) {
    http_response_code(403);
    exit("Solicitud no autorizada.");
}

// Se borran los datos de la sesión y se cambia el id de sesión.
$_SESSION = array();
session_regenerate_id(true);

header("Location: login.php");
exit;

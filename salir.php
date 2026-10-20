<?php
// Cerrar sesión: se borran los datos de la sesión y se vuelve al login.
require_once __DIR__ . "/includes/autenticacion.php";

session_destroy();

header("Location: login.php");
exit;

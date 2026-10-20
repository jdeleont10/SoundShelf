<?php
// Cerrar sesión: se borra la sesión y se vuelve al inicio.
session_start();
session_destroy();

header("Location: index.html");
exit;

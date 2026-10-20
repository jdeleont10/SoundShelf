<?php
// Conexión a MySQL (datos de XAMPP).
// En esta computadora MySQL usa el puerto 3308; en otra normalmente es 3306.
$servidor = "localhost";
$usuario_bd = "root";
$clave_bd = "";
$base_datos = "soundshelf";
$puerto = 3308;

// Para que, si algo falla, se vea nuestro mensaje en lugar de un error de PHP.
mysqli_report(MYSQLI_REPORT_OFF);

$conexion = mysqli_connect($servidor, $usuario_bd, $clave_bd, $base_datos, $puerto);

if (!$conexion) {
    die("No se pudo conectar a MySQL. Enciende MySQL en XAMPP e importa database.sql y usuarios.sql.");
}

// Para que las tildes y la ñ se vean bien.
mysqli_set_charset($conexion, "utf8mb4");

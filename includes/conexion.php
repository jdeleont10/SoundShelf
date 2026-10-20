<?php
// Conexión a MySQL (datos de XAMPP).
// En esta computadora MySQL usa el puerto 3308; en otra normalmente es 3306.
$servidor = "localhost";
$usuario_bd = "root";
$clave_bd = "";
$base_datos = "soundshelf";
$puerto = 3308;

// Así mysqli no detiene la página con un error; lo revisamos con un if en iniciar.php.
mysqli_report(MYSQLI_REPORT_OFF);

$conexion = @mysqli_connect($servidor, $usuario_bd, $clave_bd, $base_datos, $puerto);

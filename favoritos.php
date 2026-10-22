<?php
// Agrega o quita una canción de favoritos.
// Lo llama el botón Favoritos de las tarjetas (un formulario que envía el id de la canción).
session_start();
include "includes/conexion.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php?aviso=1");
    exit;
}

$usuarioId = $_SESSION["usuario_id"];
$cancionId = (int) $_POST["cancion"];

// ¿Ya era favorita?
$consulta = mysqli_prepare($conexion, "SELECT * FROM favoritos WHERE usuario_id = ? AND cancion_id = ?");
mysqli_stmt_bind_param($consulta, "ii", $usuarioId, $cancionId);
mysqli_stmt_execute($consulta);
$resultado = mysqli_stmt_get_result($consulta);

if (mysqli_num_rows($resultado) > 0) {
    // Sí: se quita.
    $consulta = mysqli_prepare($conexion, "DELETE FROM favoritos WHERE usuario_id = ? AND cancion_id = ?");
} else {
    // No: se agrega.
    $consulta = mysqli_prepare($conexion, "INSERT INTO favoritos (usuario_id, cancion_id) VALUES (?, ?)");
}
mysqli_stmt_bind_param($consulta, "ii", $usuarioId, $cancionId);
mysqli_stmt_execute($consulta);

// Se vuelve a la página donde estaba el usuario.
if (isset($_SERVER["HTTP_REFERER"])) {
    header("Location: " . $_SERVER["HTTP_REFERER"]);
} else {
    header("Location: lista.html");
}
exit;

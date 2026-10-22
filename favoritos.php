<?php
// Agrega o quita una canción de favoritos.
// Lo llama el botón Favoritos de las tarjetas (un formulario que envía el id de la canción,
// el token y la página a la que hay que volver).
include "includes/sesion.php";
include "includes/conexion.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php?aviso=1&volver=lista.html");
    exit;
}

// Solo se acepta el formulario del botón (POST con el token correcto).
if ($_SERVER["REQUEST_METHOD"] != "POST" || !tokenValido()) {
    header("Location: lista.html");
    exit;
}

$usuarioId = $_SESSION["usuario_id"];
$cancionId = 0;
if (isset($_POST["cancion"])) {
    $cancionId = (int) $_POST["cancion"];
}

if ($cancionId > 0) {
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
}

// Se vuelve a la página donde estaba el usuario (solo si es una página de SoundShelf).
$volver = "lista.html";
if (isset($_POST["volver"])) {
    $volver = paginaSegura($_POST["volver"], "lista.html");
}
header("Location: " . $volver);
exit;

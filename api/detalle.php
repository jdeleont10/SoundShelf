<?php
// Datos para detalle.html: una canción y otras parecidas.
// Ejemplo: api/detalle.php?id=1
session_start();
include "../includes/conexion.php";
include "../includes/funciones.php";

header("Content-Type: application/json");

// Solo con sesión. Si no hay, JavaScript manda al login.
if (!isset($_SESSION["usuario_id"])) {
    echo json_encode(array("necesitaLogin" => true));
    exit;
}

$respuesta = array();
$respuesta["sesion"] = datosSesion($conexion);

$id = 0;
if (isset($_GET["id"])) {
    $id = (int) $_GET["id"];
}

// Buscar la canción.
$consulta = mysqli_prepare($conexion, "SELECT c.*, g.nombre AS genero
                                       FROM canciones c
                                       INNER JOIN generos g ON c.genero_id = g.id
                                       WHERE c.id = ?");
mysqli_stmt_bind_param($consulta, "i", $id);
mysqli_stmt_execute($consulta);
$resultado = mysqli_stmt_get_result($consulta);
$cancion = mysqli_fetch_assoc($resultado);

// Si no existe, se avisa y se termina.
if ($cancion == null) {
    $respuesta["cancion"] = null;
    echo json_encode($respuesta);
    exit;
}

$respuesta["cancion"] = prepararCancion($cancion);

// Canciones relacionadas: hasta 3 del mismo género.
$respuesta["relacionadas"] = array();
$respuesta["mismoGenero"] = true;

$consulta = mysqli_prepare($conexion, "SELECT c.*, g.nombre AS genero
                                       FROM canciones c
                                       INNER JOIN generos g ON c.genero_id = g.id
                                       WHERE c.genero_id = ? AND c.id <> ?
                                       ORDER BY c.puntuacion DESC
                                       LIMIT 3");
mysqli_stmt_bind_param($consulta, "ii", $cancion["genero_id"], $id);
mysqli_stmt_execute($consulta);
$resultado = mysqli_stmt_get_result($consulta);
while ($fila = mysqli_fetch_assoc($resultado)) {
    $respuesta["relacionadas"][] = prepararCancion($fila);
}

// Si es la única de su género, se mandan las 3 mejor puntuadas.
if (count($respuesta["relacionadas"]) == 0) {
    $respuesta["mismoGenero"] = false;
    $consulta = mysqli_prepare($conexion, "SELECT c.*, g.nombre AS genero
                                           FROM canciones c
                                           INNER JOIN generos g ON c.genero_id = g.id
                                           WHERE c.id <> ?
                                           ORDER BY c.puntuacion DESC
                                           LIMIT 3");
    mysqli_stmt_bind_param($consulta, "i", $id);
    mysqli_stmt_execute($consulta);
    $resultado = mysqli_stmt_get_result($consulta);
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $respuesta["relacionadas"][] = prepararCancion($fila);
    }
}

echo json_encode($respuesta);

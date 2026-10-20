<?php
// Datos para lista.html: canciones del catálogo con búsqueda, filtro por género y orden.
// Ejemplo: api/lista.php?q=montesanto&genero=1&orden=puntuacion
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

// Lo que el usuario eligió en el formulario (o los valores por defecto).
$q = "";
if (isset($_GET["q"])) {
    $q = trim($_GET["q"]);
}

$genero = 0;
if (isset($_GET["genero"])) {
    $genero = (int) $_GET["genero"];
}

$orden = "titulo";
if (isset($_GET["orden"])) {
    $orden = $_GET["orden"];
}

// El orden se elige de una lista fija, así nadie puede meter otra cosa en el SQL.
$ordenSql = "c.titulo";
if ($orden == "puntuacion") {
    $ordenSql = "c.puntuacion DESC";
}
if ($orden == "anio") {
    $ordenSql = "c.anio DESC";
}

// Géneros para el menú desplegable.
$resultado = mysqli_query($conexion, "SELECT id, nombre FROM generos ORDER BY nombre");
$respuesta["generos"] = array();
while ($fila = mysqli_fetch_assoc($resultado)) {
    $respuesta["generos"][] = $fila;
}

// Canciones que coinciden con la búsqueda (en el título o en el artista).
$buscar = "%" . $q . "%";
if ($genero > 0) {
    $consulta = mysqli_prepare($conexion, "SELECT c.*, g.nombre AS genero
                                           FROM canciones c
                                           INNER JOIN generos g ON c.genero_id = g.id
                                           WHERE (c.titulo LIKE ? OR c.artista LIKE ?) AND c.genero_id = ?
                                           ORDER BY " . $ordenSql);
    mysqli_stmt_bind_param($consulta, "ssi", $buscar, $buscar, $genero);
} else {
    $consulta = mysqli_prepare($conexion, "SELECT c.*, g.nombre AS genero
                                           FROM canciones c
                                           INNER JOIN generos g ON c.genero_id = g.id
                                           WHERE c.titulo LIKE ? OR c.artista LIKE ?
                                           ORDER BY " . $ordenSql);
    mysqli_stmt_bind_param($consulta, "ss", $buscar, $buscar);
}
mysqli_stmt_execute($consulta);
$resultado = mysqli_stmt_get_result($consulta);

$respuesta["canciones"] = array();
while ($fila = mysqli_fetch_assoc($resultado)) {
    $respuesta["canciones"][] = prepararCancion($fila);
}

echo json_encode($respuesta);

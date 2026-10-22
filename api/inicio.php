<?php
// Datos para index.html: cifras, canciones destacadas y géneros.
// Responde en JSON (lo lee js/inicio.js con fetch).
include "../includes/sesion.php";
include "../includes/conexion.php";
include "../includes/funciones.php";

$respuesta = array();
$respuesta["sesion"] = datosSesion($conexion);

// Cuántas canciones y cuántos géneros hay.
$resultado = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM canciones");
$fila = mysqli_fetch_assoc($resultado);
$respuesta["totalCanciones"] = $fila["total"];

$resultado = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM generos");
$fila = mysqli_fetch_assoc($resultado);
$respuesta["totalGeneros"] = $fila["total"];

// Las 3 canciones mejor puntuadas.
$resultado = mysqli_query($conexion, "SELECT c.*, g.nombre AS genero
                                      FROM canciones c
                                      INNER JOIN generos g ON c.genero_id = g.id
                                      ORDER BY c.puntuacion DESC
                                      LIMIT 3");
$respuesta["destacadas"] = array();
while ($fila = mysqli_fetch_assoc($resultado)) {
    if (isset($_SESSION["usuario_id"])) {
        $respuesta["destacadas"][] = prepararCancion($fila);
    } else {
        // Sin sesión solo se manda la portada (se muestra borrosa).
        $respuesta["destacadas"][] = array("imagen" => $fila["imagen"], "respaldo" => portadaRespaldo($fila["genero_id"]));
    }
}

// Los géneros con cuántas canciones tiene cada uno.
$resultado = mysqli_query($conexion, "SELECT g.id, g.nombre, g.descripcion, COUNT(c.id) AS total
                                      FROM generos g
                                      LEFT JOIN canciones c ON c.genero_id = g.id
                                      GROUP BY g.id, g.nombre, g.descripcion
                                      ORDER BY g.nombre");
$respuesta["generos"] = array();
while ($fila = mysqli_fetch_assoc($resultado)) {
    $respuesta["generos"][] = $fila;
}

header("Content-Type: application/json");
echo json_encode($respuesta);

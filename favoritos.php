<?php
// Favoritos del usuario. JavaScript (script.js) llama a este archivo con fetch.
//   POST accion=agregar&cancion=4   guarda una canción
//   POST accion=quitar&cancion=4    la quita
// Siempre responde con la lista actualizada, por ejemplo: {"favoritos": [1, 4]}

define("RESPUESTA_JSON", true); // si algo falla, iniciar.php responde en JSON
require_once __DIR__ . "/includes/iniciar.php";

$usuario = usuarioActual();
if ($usuario == null) {
    responder(array("error" => "Inicia sesión para guardar favoritos en tu cuenta."), 401);
}

$accion = texto($_POST, "accion");
$cancionId = numero($_POST, "cancion");

if ($accion == "agregar") {
    // INSERT IGNORE: si ya era favorita, no se repite.
    ejecutar("INSERT IGNORE INTO favoritos (usuario_id, cancion_id) VALUES (?, ?)", array($usuario["id"], $cancionId));
} else if ($accion == "quitar") {
    ejecutar("DELETE FROM favoritos WHERE usuario_id = ? AND cancion_id = ?", array($usuario["id"], $cancionId));
}

responder(array("favoritos" => idsFavoritos($usuario["id"])));

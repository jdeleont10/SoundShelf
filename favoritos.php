<?php
// Favoritos del usuario. JavaScript (script.js) llama a este archivo con fetch.
//   POST accion=agregar&cancion=4          guarda una canción
//   POST accion=quitar&cancion=4           la quita
//   POST accion=sincronizar&canciones=1,2  pasa a la cuenta los favoritos guardados en el navegador
// Siempre responde con la lista actualizada, por ejemplo: {"favoritos": [1, 4]}

define("RESPUESTA_JSON", true); // si algo falla, iniciar.php responde en JSON
require_once __DIR__ . "/includes/iniciar.php";

$usuario = usuarioActual();
if ($usuario == null) {
    responder(array("error" => "Inicia sesión para guardar favoritos en tu cuenta."), 401);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!verificarCsrf()) {
        responder(array("error" => "Solicitud inválida. Recarga la página."), 403);
    }

    $accion = texto($_POST, "accion");
    $cancionId = numero($_POST, "cancion");
    if (($accion == "agregar" || $accion == "quitar") && $cancionId < 1) {
        responder(array("error" => "Canción inválida."), 400);
    }

    if ($accion == "agregar") {
        $existe = buscarUna("SELECT id FROM canciones WHERE id = ?", array($cancionId));
        if ($existe == null) {
            responder(array("error" => "Esa canción no existe."), 404);
        }
        // INSERT IGNORE: si ya era favorita, no se repite.
        ejecutar("INSERT IGNORE INTO favoritos (usuario_id, cancion_id) VALUES (?, ?)", array($usuario["id"], $cancionId));
    } else if ($accion == "quitar") {
        ejecutar("DELETE FROM favoritos WHERE usuario_id = ? AND cancion_id = ?", array($usuario["id"], $cancionId));
    } else if ($accion == "sincronizar") {
        // Llega una lista como "1,4,7" (máximo 200). Solo se guardan las canciones que existen.
        $lista = array_slice(explode(",", texto($_POST, "canciones")), 0, 200);
        foreach ($lista as $valor) {
            $valor = trim($valor);
            if (ctype_digit($valor)) {
                ejecutar("INSERT IGNORE INTO favoritos (usuario_id, cancion_id) SELECT ?, id FROM canciones WHERE id = ?", array($usuario["id"], $valor));
            }
        }
    } else {
        responder(array("error" => "Acción no válida."), 400);
    }
}

responder(array("favoritos" => idsFavoritos($usuario["id"])));

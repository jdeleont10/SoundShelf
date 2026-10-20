<?php
// Funciones de ayuda que usan todas las páginas.

// ---------- Base de datos ----------

// Ejecuta una consulta. Los valores van aparte, en los "?", así se evita la inyección SQL.
// Ejemplo: ejecutar("DELETE FROM favoritos WHERE usuario_id = ?", array($id));
function ejecutar($sql, $valores = array())
{
    global $conexion;
    $sentencia = mysqli_prepare($conexion, $sql);
    if ($sentencia == false || !mysqli_stmt_execute($sentencia, $valores)) {
        registrarError("Error en la consulta: " . mysqli_error($conexion));
        mostrarError("El catálogo todavía no está listo", "Importa database.sql y usuarios.sql en phpMyAdmin.");
    }
    return $sentencia;
}

// Devuelve todas las filas de una consulta SELECT.
function buscar($sql, $valores = array())
{
    $resultado = mysqli_stmt_get_result(ejecutar($sql, $valores));
    return mysqli_fetch_all($resultado, MYSQLI_ASSOC);
}

// Devuelve solo la primera fila (o null si no hay ninguna).
function buscarUna($sql, $valores = array())
{
    $resultado = mysqli_stmt_get_result(ejecutar($sql, $valores));
    return mysqli_fetch_assoc($resultado);
}

// Los id de las canciones favoritas de un usuario, por ejemplo [4, 11].
function idsFavoritos($usuarioId)
{
    $filas = buscar("SELECT cancion_id FROM favoritos WHERE usuario_id = ? ORDER BY creado_en, cancion_id", array($usuarioId));
    $ids = array();
    foreach ($filas as $fila) {
        $ids[] = (int) $fila["cancion_id"];
    }
    return $ids;
}

// ---------- Datos que llegan del usuario ----------

// Lee un texto de $_GET o $_POST. Si no viene, devuelve "".
function texto($datos, $nombre)
{
    if (isset($datos[$nombre]) && is_string($datos[$nombre])) {
        return trim($datos[$nombre]);
    }
    return "";
}

// Lee un número entero positivo de $_GET o $_POST. Si no es válido, devuelve 0.
function numero($datos, $nombre)
{
    $valor = texto($datos, $nombre);
    if (ctype_digit($valor)) {
        return (int) $valor;
    }
    return 0;
}

// ---------- Mostrar datos ----------

// Texto seguro para el HTML (evita que alguien meta código en la página).
function e($valor)
{
    return htmlspecialchars($valor, ENT_QUOTES, "UTF-8");
}

// 223 segundos -> "3:43".
function formatearDuracion($segundos)
{
    $minutos = floor($segundos / 60);
    $resto = $segundos % 60;
    if ($resto < 10) {
        $resto = "0" . $resto;
    }
    return $minutos . ":" . $resto;
}

// Enlaces para escuchar: Apple Music directo; Spotify y YouTube con una búsqueda.
function enlacesEscuchar($cancion)
{
    // Se corta el título donde empieza un paréntesis o corchete, como "(Live)", y se usa solo el primer artista.
    $titulo = $cancion["titulo"];
    $posicion = strpos($titulo, "(");
    if ($posicion !== false) {
        $titulo = substr($titulo, 0, $posicion);
    }
    $posicion = strpos($titulo, "[");
    if ($posicion !== false) {
        $titulo = substr($titulo, 0, $posicion);
    }
    $titulo = trim($titulo);
    $artistas = explode(",", $cancion["artista"]);
    $busqueda = $titulo . " " . trim($artistas[0]);

    $enlaces = array();
    $enlaces["Spotify"] = "https://open.spotify.com/search/" . rawurlencode($busqueda);
    $enlaces["YouTube"] = "https://www.youtube.com/results?search_query=" . urlencode($busqueda);
    if ($cancion["enlace_apple"] != "") {
        $enlaces["Apple Music"] = $cancion["enlace_apple"];
    }
    return $enlaces;
}

// Ilustración de cada género, por si la portada de internet no carga.
function portadaRespaldo($generoId)
{
    $ilustraciones = array(1 => "jazz", 2 => "indie", 3 => "lofi", 4 => "electronica", 5 => "rock");
    if (isset($ilustraciones[$generoId])) {
        return "assets/img/" . $ilustraciones[$generoId] . ".svg";
    }
    return "assets/img/indie.svg";
}

// ---------- Respuestas y errores ----------

// Responde en JSON y termina (lo usan api.php y favoritos.php).
function responder($datos, $codigo = 200)
{
    http_response_code($codigo);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    exit;
}

// Guarda un error en el archivo de errores de PHP (en XAMPP: C:\xampp\apache\logs\error.log).
function registrarError($mensaje)
{
    error_log("[SoundShelf] " . $mensaje);
}

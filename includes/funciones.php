<?php
// Funciones que se usan en varias páginas.

// Convierte los segundos a minutos. Ejemplo: 223 -> "3:43"
function formatearDuracion($segundos)
{
    $minutos = floor($segundos / 60);
    $resto = $segundos % 60;
    if ($resto < 10) {
        $resto = "0" . $resto;
    }
    return $minutos . ":" . $resto;
}

// Texto para buscar la canción en Spotify o YouTube.
// Se quita lo que va entre paréntesis, como "(Live)", y se deja solo el primer artista.
function textoBusqueda($cancion)
{
    $titulo = $cancion["titulo"];
    $posicion = strpos($titulo, "(");
    if ($posicion !== false) {
        $titulo = substr($titulo, 0, $posicion);
    }
    $artistas = explode(",", $cancion["artista"]);
    return trim($titulo) . " " . trim($artistas[0]);
}

function enlaceSpotify($cancion)
{
    return "https://open.spotify.com/search/" . rawurlencode(textoBusqueda($cancion));
}

function enlaceYoutube($cancion)
{
    return "https://www.youtube.com/results?search_query=" . urlencode(textoBusqueda($cancion));
}

// Agrega a una canción los datos que necesita JavaScript para mostrarla:
// la duración "3:43", los enlaces para escuchar y el dibujo de respaldo.
function prepararCancion($cancion)
{
    $cancion["duracion"] = formatearDuracion($cancion["duracion_segundos"]);
    $cancion["spotify"] = enlaceSpotify($cancion);
    $cancion["youtube"] = enlaceYoutube($cancion);
    $cancion["respaldo"] = portadaRespaldo($cancion["genero_id"]);
    return $cancion;
}

// Datos de la sesión para JavaScript: el nombre del usuario y los id de sus favoritos.
// Si no hay sesión, el nombre va vacío.
function datosSesion($conexion)
{
    $sesion = array();
    $sesion["nombre"] = "";
    $sesion["favoritos"] = array();
    $sesion["token"] = $_SESSION["token"]; // para los formularios que arma JavaScript

    if (isset($_SESSION["usuario_id"])) {
        $sesion["nombre"] = $_SESSION["usuario_nombre"];

        $consulta = mysqli_prepare($conexion, "SELECT cancion_id FROM favoritos WHERE usuario_id = ?");
        mysqli_stmt_bind_param($consulta, "i", $_SESSION["usuario_id"]);
        mysqli_stmt_execute($consulta);
        $resultado = mysqli_stmt_get_result($consulta);
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $sesion["favoritos"][] = $fila["cancion_id"];
        }
    }
    return $sesion;
}

// Dibujo de cada género, por si la portada de internet no carga.
function portadaRespaldo($generoId)
{
    if ($generoId == 1) {
        return "assets/img/jazz.svg";
    }
    if ($generoId == 3) {
        return "assets/img/lofi.svg";
    }
    if ($generoId == 4) {
        return "assets/img/electronica.svg";
    }
    if ($generoId == 5) {
        return "assets/img/rock.svg";
    }
    return "assets/img/indie.svg";
}

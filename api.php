<?php
// API: las páginas .html piden aquí los datos con fetch() y reciben JSON.
//   api.php?accion=inicio                      cifras, géneros y canciones destacadas
//   api.php?accion=catalogo&q=...&genero=...   canciones del catálogo (pide sesión)
//   api.php?accion=cancion&id=1                una canción y sus relacionadas (pide sesión)

define("RESPUESTA_JSON", true); // si algo falla, iniciar.php responde en JSON
require_once __DIR__ . "/includes/iniciar.php";

// Parte del SQL que se repite: cada canción con el nombre de su género.
$sqlCanciones = "SELECT c.*, g.nombre AS genero
                 FROM canciones AS c
                 INNER JOIN generos AS g ON c.genero_id = g.id";

// Agrega a cada canción la duración "3:43", los enlaces para escuchar y la ilustración de respaldo.
function preparar($canciones)
{
    foreach ($canciones as $i => $cancion) {
        $canciones[$i]["duracion"] = formatearDuracion($cancion["duracion_segundos"]);
        $canciones[$i]["enlaces"] = enlacesEscuchar($cancion);
        $canciones[$i]["respaldo"] = portadaRespaldo($cancion["genero_id"]);
    }
    return $canciones;
}

// Datos de la sesión: van en todas las respuestas.
$usuario = usuarioActual();
$sesion = array("usuario" => null, "csrf" => tokenCsrf(), "favoritos" => array());
if ($usuario != null) {
    $sesion["usuario"] = array("nombre" => $usuario["nombre"]);
    $sesion["favoritos"] = idsFavoritos($usuario["id"]);
}

$accion = texto($_GET, "accion");

// El catálogo y las fichas son solo para usuarios con sesión.
if (($accion == "catalogo" || $accion == "cancion") && $usuario == null) {
    responder(array("error" => "Inicia sesión para ver y escuchar las canciones.", "sesion" => $sesion), 401);
}

// ---------- Página de inicio ----------
if ($accion == "inicio") {
    $estadisticas = buscarUna("SELECT (SELECT COUNT(*) FROM canciones) AS total_canciones, (SELECT COUNT(*) FROM generos) AS total_generos");
    $destacados = preparar(buscar($sqlCanciones . " ORDER BY c.puntuacion DESC, c.id ASC LIMIT 3"));

    // Sin sesión solo se mandan las portadas (se muestran borrosas).
    if ($usuario == null) {
        foreach ($destacados as $i => $cancion) {
            $destacados[$i] = array("imagen" => $cancion["imagen"], "respaldo" => $cancion["respaldo"]);
        }
    }

    responder(array(
        "sesion" => $sesion,
        "estadisticas" => $estadisticas,
        "generos" => buscarGeneros(),
        "destacados" => $destacados
    ));
}

// ---------- Catálogo (con búsqueda y filtro por género) ----------
if ($accion == "catalogo") {
    $sql = $sqlCanciones . " WHERE 1 = 1";
    $valores = array();

    $busqueda = texto($_GET, "q");
    if ($busqueda != "") {
        $sql = $sql . " AND (c.titulo LIKE ? OR c.artista LIKE ?)";
        $valores[] = "%" . $busqueda . "%";
        $valores[] = "%" . $busqueda . "%";
    }

    $generoId = numero($_GET, "genero");
    if ($generoId > 0) {
        $sql = $sql . " AND c.genero_id = ?";
        $valores[] = $generoId;
    }

    // Se ignoran los signos ¿ y ¡ del inicio, así "¿Sabes?" queda en la S.
    $sql = $sql . " ORDER BY REPLACE(REPLACE(c.titulo, '¿', ''), '¡', ''), c.id";

    responder(array(
        "sesion" => $sesion,
        "generos" => buscarGeneros(),
        "canciones" => preparar(buscar($sql, $valores))
    ));
}

// ---------- Ficha de una canción ----------
if ($accion == "cancion") {
    $id = numero($_GET, "id");
    if ($id < 1) {
        responder(array("error" => "Abre el catálogo y selecciona una ficha. La dirección del detalle necesita un identificador válido.", "sesion" => $sesion), 400);
    }

    $cancion = buscarUna($sqlCanciones . " WHERE c.id = ?", array($id));
    if ($cancion == null) {
        responder(array("error" => "No encontramos una canción con ese identificador. Explora las otras fichas del catálogo.", "sesion" => $sesion), 404);
    }

    // Hasta 3 canciones del mismo género...
    $relacionados = buscar($sqlCanciones . " WHERE c.genero_id = ? AND c.id <> ? ORDER BY c.puntuacion DESC, c.id LIMIT 3", array($cancion["genero_id"], $id));
    $mismoGenero = true;

    // ...y si es la única de su género, las 3 mejor puntuadas del catálogo.
    if (count($relacionados) == 0) {
        $mismoGenero = false;
        $relacionados = buscar($sqlCanciones . " WHERE c.id <> ? ORDER BY c.puntuacion DESC, c.id LIMIT 3", array($id));
    }

    $ficha = preparar(array($cancion));
    responder(array(
        "sesion" => $sesion,
        "cancion" => $ficha[0],
        "relacionados" => preparar($relacionados),
        "mismoGenero" => $mismoGenero
    ));
}

responder(array("error" => "Acción no válida.", "sesion" => $sesion), 400);

// Géneros con la cantidad de canciones de cada uno (LEFT JOIN + COUNT).
function buscarGeneros()
{
    return buscar("SELECT g.id, g.nombre, g.descripcion, COUNT(c.id) AS total
                   FROM generos AS g
                   LEFT JOIN canciones AS c ON c.genero_id = g.id
                   GROUP BY g.id, g.nombre, g.descripcion
                   ORDER BY g.nombre");
}

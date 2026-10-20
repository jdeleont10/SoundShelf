<?php
// Archivo que cargan todas las páginas PHP al empezar:
// conexión a MySQL, funciones, sesión y cabeceras de seguridad.
require_once __DIR__ . "/conexion.php";
require_once __DIR__ . "/funciones.php";
require_once __DIR__ . "/autenticacion.php";

// Cabeceras de seguridad.
header("X-Frame-Options: SAMEORIGIN");      // otros sitios no pueden mostrar SoundShelf dentro de un marco
header("X-Content-Type-Options: nosniff");  // el navegador respeta el tipo de cada archivo
header("Referrer-Policy: same-origin");     // la dirección de la página no se envía a otros sitios

// Muestra un mensaje de error y termina.
// api.php y favoritos.php definen RESPUESTA_JSON: para ellos el error se manda en JSON.
function mostrarError($titulo, $mensaje, $codigo = 503)
{
    if (defined("RESPUESTA_JSON")) {
        responder(array("error" => $mensaje), $codigo);
    }

    http_response_code($codigo);
    $tituloPagina = $titulo;
    $pagina = "";
    require __DIR__ . "/encabezado.php";
    ?>
    <main id="contenido" class="container">
        <section class="error-panel" aria-labelledby="error-titulo">
            <span class="error-symbol" aria-hidden="true">!</span>
            <p class="eyebrow">SoundShelf</p>
            <h1 id="error-titulo"><?= e($titulo) ?></h1>
            <p class="muted"><?= e($mensaje) ?></p>
            <div class="error-actions">
                <a class="button button-primary" href="lista.html">Ir al catálogo</a>
                <a class="button button-secondary" href="index.html">Volver al inicio</a>
            </div>
        </section>
    </main>
    <?php
    require __DIR__ . "/pie.php";
    exit;
}

// Si no se pudo conectar a MySQL, se muestra un mensaje amable.
if (!$conexion) {
    registrarError("No se pudo conectar a MySQL: " . mysqli_connect_error());
    mostrarError("No pudimos conectar con el catálogo", "Inicia MySQL en XAMPP, importa database.sql y revisa los datos de includes/conexion.php. Después, vuelve a cargar esta página.");
}
mysqli_set_charset($conexion, "utf8mb4");

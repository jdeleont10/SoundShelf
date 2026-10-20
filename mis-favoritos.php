<?php
// Página "Mis favoritos": las canciones que el usuario guardó en su cuenta.
require_once __DIR__ . "/includes/iniciar.php";
requerirSesion("favoritos"); // solo con sesión

$usuario = usuarioActual();
$canciones = buscar("SELECT c.*, g.nombre AS genero
                     FROM favoritos AS f
                     INNER JOIN canciones AS c ON f.cancion_id = c.id
                     INNER JOIN generos AS g ON c.genero_id = g.id
                     WHERE f.usuario_id = ?
                     ORDER BY f.creado_en DESC, c.id DESC", array($usuario["id"]));
$total = count($canciones);

$tituloPagina = "Mis favoritos";
$pagina = "favoritos";
require __DIR__ . "/includes/encabezado.php";
?>
<main id="contenido" class="container">
    <nav class="breadcrumb" aria-label="Ruta de navegación"><a href="index.html">Inicio</a><span aria-hidden="true">/</span><span>Mis favoritos</span></nav>
    <section class="page-heading" aria-labelledby="favoritos-titulo">
        <p class="eyebrow">TU COLECCIÓN</p>
        <h1 id="favoritos-titulo" class="page-title">Mis <span class="accent">favoritos.</span></h1>
        <p class="page-description">Las canciones que guardaste con tu cuenta, <?= e($usuario["nombre"]) ?>. Se mantienen aunque cambies de navegador o de computadora.</p>
    </section>

    <section class="section-block" aria-labelledby="lista-favoritos-titulo">
        <div class="section-heading">
            <h2 id="lista-favoritos-titulo" class="section-title">Guardadas por ti</h2>
            <p class="results-summary" id="total-favoritos"><?php if ($total == 1) { echo "1 canción"; } else { echo $total . " canciones"; } ?></p>
        </div>
        <div id="mis-favoritos" class="track-grid row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
            <?php foreach ($canciones as $cancion) { require __DIR__ . "/includes/tarjeta.php"; } ?>
        </div>
        <article id="favoritos-vacio" class="empty-state" <?php if ($total > 0) { echo "hidden"; } ?>>
            <p class="eyebrow">EMPIEZA TU COLECCIÓN</p>
            <h3>Todavía no tienes favoritos</h3>
            <p class="muted">Pulsa ♡ Favoritos en cualquier canción del catálogo y aparecerá aquí.</p>
            <a class="button button-primary btn btn-accent" href="lista.html">Explorar el catálogo</a>
        </article>
    </section>
</main>
<?php require __DIR__ . "/includes/pie.php"; ?>

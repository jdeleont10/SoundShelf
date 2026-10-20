<?php
// Mis favoritos: las canciones que el usuario guardó en su cuenta.
session_start();
include "includes/conexion.php";
include "includes/funciones.php";

// Solo se puede ver con sesión.
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php?aviso=1");
    exit;
}

$consulta = mysqli_prepare($conexion, "SELECT c.*, g.nombre AS genero
                                       FROM favoritos f
                                       INNER JOIN canciones c ON f.cancion_id = c.id
                                       INNER JOIN generos g ON c.genero_id = g.id
                                       WHERE f.usuario_id = ?
                                       ORDER BY f.creado_en DESC");
mysqli_stmt_bind_param($consulta, "i", $_SESSION["usuario_id"]);
mysqli_stmt_execute($consulta);
$resultado = mysqli_stmt_get_result($consulta);

$canciones = array();
while ($fila = mysqli_fetch_assoc($resultado)) {
    $canciones[] = $fila;
}

$titulo = "Mis favoritos";
include "includes/encabezado.php";
?>

<main class="container">
    <p class="migas"><a href="index.html">Inicio</a> / Mis favoritos</p>

    <section class="seccion-chica">
        <p class="subtitulo-chico">TU COLECCIÓN</p>
        <h1>Mis <span class="lila">favoritos.</span></h1>
        <p class="texto-suave">Las canciones que guardaste con tu cuenta, <?php echo htmlspecialchars($_SESSION["usuario_nombre"]); ?>.</p>
    </section>

    <section class="seccion">
        <h2>Guardadas por ti</h2>
        <p class="texto-suave">
            <?php
            if (count($canciones) == 1) {
                echo "1 canción";
            } else {
                echo count($canciones) . " canciones";
            }
            ?>
        </p>

        <?php if (count($canciones) == 0) { ?>
            <div class="caja text-center">
                <p class="subtitulo-chico">EMPIEZA TU COLECCIÓN</p>
                <h3>Todavía no tienes favoritos</h3>
                <p class="texto-suave">Pulsa ♡ Favoritos en cualquier canción del catálogo y aparecerá aquí.</p>
                <a class="btn btn-lila" href="lista.html">Explorar el catálogo</a>
            </div>
        <?php } else { ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                <?php
                foreach ($canciones as $cancion) {
                    include "includes/tarjeta.php";
                }
                ?>
            </div>
        <?php } ?>
    </section>
</main>

<?php include "includes/pie.php"; ?>

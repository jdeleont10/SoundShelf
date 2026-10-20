<?php
require_once __DIR__ . "/autenticacion.php";

// Cada página define su título ($tituloPagina) y qué enlace del menú va marcado ($pagina).
if (!isset($tituloPagina)) {
    $tituloPagina = "SoundShelf";
}
if (!isset($pagina)) {
    $pagina = "";
}

// Favoritos del usuario, para que los botones ♥ salgan marcados desde el principio.
$idsFavoritos = array();
if (usuarioActual() != null && isset($conexion) && $conexion) {
    $idsFavoritos = idsFavoritos(usuarioActual()["id"]);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SoundShelf: descubre un catálogo de canciones, explora sus géneros y consulta cada ficha.">
    <title><?= e($tituloPagina) ?> | SoundShelf</title>
    <link rel="icon" href="assets/favicon.svg" type="image/svg+xml">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="estilos.css?v=<?= filemtime(__DIR__ . '/../estilos.css') ?>">
</head>
<body data-sesion="<?= usuarioActual() ? '1' : '0' ?>" data-favoritos="<?= e(implode(',', $idsFavoritos)) ?>">
<a class="skip-link" href="#contenido">Saltar al contenido</a>
<header class="site-header sticky-top">
    <nav class="navbar navbar-expand-lg navbar-dark" aria-label="Navegación principal">
        <div class="container">
            <a class="navbar-brand brand" href="index.html" aria-label="SoundShelf, inicio">
                <span class="brand-mark" aria-hidden="true">S<span>·</span></span>
                <span><span class="brand-name">SoundShelf</span><span class="brand-caption">Música para descubrir</span></span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal" aria-controls="menuPrincipal" aria-expanded="false" aria-label="Abrir menú"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="menuPrincipal">
                <div class="navbar-nav ms-lg-auto gap-lg-2 align-items-lg-center">
                    <a class="nav-link <?= $pagina === 'inicio' ? 'active' : '' ?>" href="index.html" <?= $pagina === 'inicio' ? 'aria-current="page"' : '' ?>>Inicio</a>
                    <a class="nav-link <?= $pagina === 'catalogo' ? 'active' : '' ?>" href="lista.html" <?= $pagina === 'catalogo' ? 'aria-current="page"' : '' ?>>Catálogo</a>
                    <a class="nav-link" href="index.html#generos">Géneros</a>
                    <?php if (usuarioActual()): ?><a class="nav-link <?= $pagina === 'favoritos' ? 'active' : '' ?>" href="mis-favoritos.php" <?= $pagina === 'favoritos' ? 'aria-current="page"' : '' ?>>Mis favoritos</a><?php endif; ?>
                </div>
                <div class="auth-links d-flex flex-wrap align-items-center gap-2 ms-lg-4 mt-3 mt-lg-0">
                    <?php if (usuarioActual()): ?>
                        <span class="auth-greeting me-2">Hola, <?= e(usuarioActual()['nombre']) ?></span>
                        <a class="btn btn-outline-light btn-sm rounded-pill px-3" href="salir.php">Cerrar sesión</a>
                    <?php else: ?>
                        <a class="btn btn-outline-light btn-sm rounded-pill px-3" href="login.php">Iniciar sesión</a>
                        <a class="btn btn-accent btn-sm rounded-pill px-3" href="registro.php">Crear cuenta</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</header>

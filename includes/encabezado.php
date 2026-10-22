<?php
// Encabezado de todas las páginas. Antes de incluirlo, cada página pone el $titulo.

// Lista con los id de las canciones favoritas del usuario.
// Sirve para el contador del menú y para saber qué botones de favoritos van marcados.
$misFavoritos = array();
if (isset($_SESSION["usuario_id"])) {
    $consulta = mysqli_prepare($conexion, "SELECT cancion_id FROM favoritos WHERE usuario_id = ?");
    mysqli_stmt_bind_param($consulta, "i", $_SESSION["usuario_id"]);
    mysqli_stmt_execute($consulta);
    $resultado = mysqli_stmt_get_result($consulta);
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $misFavoritos[] = $fila["cancion_id"];
    }
}
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?> | SoundShelf</title>
    <link rel="icon" href="assets/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="estilos.css?v=3">
</head>
<body>

<!-- Barra que se llena al bajar por la página (la mueve script.js) -->
<div id="barra-progreso"></div>

<header class="sticky-top">
<nav class="navbar navbar-expand-lg menu">
    <div class="container">
        <a class="navbar-brand marca" href="index.html">SoundShelf<span class="lila">.</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item"><a class="nav-link" href="index.html">Inicio</a></li>
                <li class="nav-item"><a class="nav-link" href="lista.html">Catálogo</a></li>
                <li class="nav-item"><a class="nav-link" href="index.html#generos">Géneros</a></li>
                <?php if (isset($_SESSION["usuario_id"])) { ?>
                    <li class="nav-item"><a class="nav-link" href="mis-favoritos.php">Mis favoritos <span class="badge contador"><?php echo count($misFavoritos); ?></span></a></li>
                    <li class="nav-item"><span class="navbar-text ms-lg-3">Hola, <?php echo htmlspecialchars($_SESSION["usuario_nombre"]); ?></span></li>
                    <li class="nav-item"><a class="btn btn-outline-light btn-sm ms-lg-3" href="salir.php">Cerrar sesión</a></li>
                <?php } else { ?>
                    <li class="nav-item"><a class="btn btn-outline-light btn-sm ms-lg-3" href="login.php">Iniciar sesión</a></li>
                    <li class="nav-item"><a class="btn btn-lila btn-sm ms-lg-2 mt-2 mt-lg-0" href="registro.php">Crear cuenta</a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>
</header>

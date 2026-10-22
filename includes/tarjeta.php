<?php
// Tarjeta de una canción. Antes de incluirla, la página tiene que tener:
//   $cancion       una fila de la tabla canciones (con el nombre del género en "genero")
//   $misFavoritos  los id de las canciones favoritas del usuario
?>
<div class="col">
    <div class="card tarjeta h-100">
        <a href="detalle.html?id=<?php echo $cancion["id"]; ?>">
            <img src="<?php echo $cancion["imagen"]; ?>" class="card-img-top" alt="Portada de <?php echo htmlspecialchars($cancion["album"]); ?>" onerror="this.src='<?php echo portadaRespaldo($cancion["genero_id"]); ?>'">
        </a>
        <div class="card-body">
            <div class="d-flex justify-content-between mb-2">
                <span class="badge etiqueta"><?php echo $cancion["genero"]; ?></span>
                <span class="estrella"><?php echo $cancion["puntuacion"]; ?> / 10</span>
            </div>
            <h5 class="card-title"><a href="detalle.html?id=<?php echo $cancion["id"]; ?>"><?php echo htmlspecialchars($cancion["titulo"]); ?></a></h5>
            <p class="artista mb-1"><?php echo htmlspecialchars($cancion["artista"]); ?></p>
            <p class="texto-suave small"><?php echo htmlspecialchars($cancion["subtitulo"]); ?></p>

            <!-- Botón de favoritos: envía el id de la canción a favoritos.php -->
            <form action="favoritos.php" method="post" class="d-inline">
                <input type="hidden" name="cancion" value="<?php echo $cancion["id"]; ?>">
                <?php if (in_array($cancion["id"], $misFavoritos)) { ?>
                    <button type="submit" class="btn btn-sm boton-favorito marcado">Favorita</button>
                <?php } else { ?>
                    <button type="submit" class="btn btn-sm boton-favorito">Favoritos</button>
                <?php } ?>
            </form>
            <!-- Botón Escuchar: carga la canción en el reproductor de Spotify de abajo (función en script.js) -->
            <button type="button" class="btn btn-sm btn-spotify" onclick="reproducir('<?php echo $cancion["spotify_id"]; ?>')">Escuchar</button>
        </div>
        <div class="card-footer d-flex justify-content-between small">
            <span class="texto-suave"><?php echo formatearDuracion($cancion["duracion_segundos"]); ?> · <?php echo $cancion["anio"]; ?></span>
            <a class="lila" href="detalle.html?id=<?php echo $cancion["id"]; ?>">Explorar</a>
        </div>
    </div>
</div>

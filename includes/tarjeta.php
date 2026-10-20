<?php // La variable $cancion corresponde a una fila devuelta por MySQL. ?>
<article class="track-card" data-id="<?= (int) $cancion['id'] ?>" data-busqueda="<?= e($cancion['titulo'] . ' ' . $cancion['artista']) ?>" data-genero="<?= (int) $cancion['genero_id'] ?>" data-titulo="<?= e($cancion['titulo']) ?>" data-anio="<?= (int) $cancion['anio'] ?>" data-puntuacion="<?= e($cancion['puntuacion']) ?>">
    <a class="track-cover-link" href="detalle.html?id=<?= (int) $cancion['id'] ?>" aria-label="Ver ficha de <?= e($cancion['titulo']) ?>">
        <div class="track-image">
            <img src="<?= e($cancion['imagen']) ?>" alt="Portada de <?= e($cancion['album']) ?>" width="800" height="600" loading="lazy" data-respaldo="<?= e(portadaRespaldo((int) $cancion['genero_id'])) ?>">
        </div>
    </a>
    <div class="track-card-content">
        <div class="track-card-topline">
            <span class="pill pill-soft"><?= e($cancion['genero']) ?></span>
            <span class="rating" aria-label="Puntuación <?= e($cancion['puntuacion']) ?> de 10"><span aria-hidden="true">★</span> <?= e($cancion['puntuacion']) ?></span>
        </div>
        <h3 class="track-title"><a href="detalle.html?id=<?= (int) $cancion['id'] ?>"><?= e($cancion['titulo']) ?></a></h3>
        <p class="track-artist"><?= e($cancion['artista']) ?></p>
        <p class="track-subtitle"><?= e($cancion['subtitulo']) ?></p>
        <button type="button" class="favorite-button" data-favorito="<?= (int) $cancion['id'] ?>" aria-label="Añadir <?= e($cancion['titulo']) ?> a favoritos" aria-pressed="false">♡ Favoritos</button>
        <a class="listen-card-link" href="<?= e(enlacesEscuchar($cancion)['Spotify']) ?>" target="_blank" rel="noopener noreferrer" aria-label="Escuchar <?= e($cancion['titulo']) ?> en Spotify"><span class="listen-icon" aria-hidden="true">▶</span>Escuchar</a>
        <div class="track-card-bottom">
            <span class="track-meta"><?= e(formatearDuracion($cancion['duracion_segundos'])) ?> <span aria-hidden="true">·</span> <?= (int) $cancion['anio'] ?></span>
            <a class="text-link" href="detalle.html?id=<?= (int) $cancion['id'] ?>" aria-label="Ver detalle de <?= e($cancion['titulo']) ?>">Explorar <span aria-hidden="true">↗</span></a>
        </div>
    </div>
</article>

<footer class="pie">
    <div class="container d-md-flex justify-content-between">
        <div>
            <p class="marca mb-1">SoundShelf<span class="lila">.</span></p>
            <p class="texto-suave small">Canciones, historias y nuevos sonidos.</p>
        </div>
        <p class="texto-suave small">Proyecto de Programación II<br>Música cristiana · Uso académico</p>
        <p class="small">
            <a href="index.html">Inicio</a> ·
            <a href="lista.html">Catálogo</a>
            <?php if (isset($_SESSION["usuario_id"])) { ?>
                · <a href="mis-favoritos.php">Mis favoritos</a>
            <?php } ?>
        </p>
    </div>
</footer>

<!-- Reproductor de Spotify de abajo (aparece al tocar Escuchar, lo controla script.js) -->
<div class="reproductor" id="reproductor">
    <iframe id="reproductor-spotify" title="Reproductor de Spotify" allow="autoplay; clipboard-write; encrypted-media"></iframe>
    <button type="button" class="btn btn-sm btn-outline-light" onclick="cerrarReproductor()">Cerrar</button>
</div>

<!-- Botón para volver arriba (aparece al bajar, lo controla script.js) -->
<button type="button" id="boton-arriba" class="btn btn-lila" onclick="irArriba()">Arriba</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="script.js?v=3"></script>
</body>
</html>

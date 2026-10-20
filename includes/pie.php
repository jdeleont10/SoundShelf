<footer class="site-footer">
    <div class="container footer-inner">
        <div>
            <a class="footer-brand" href="index.html">SoundShelf<span class="accent">.</span></a>
            <p class="footer-note">Canciones, historias y nuevos sonidos.</p>
        </div>
        <p class="footer-note">Proyecto de Programación II<br>Música cristiana · Uso académico</p>
        <nav class="footer-links" aria-label="Navegación del pie">
            <a href="index.html">Inicio</a>
            <a href="lista.html">Catálogo</a>
            <?php if (usuarioActual()): ?><a href="mis-favoritos.php">Mis favoritos</a><?php endif; ?>
        </nav>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="script.js?v=<?= filemtime(__DIR__ . '/../script.js') ?>" defer></script>
</body>
</html>

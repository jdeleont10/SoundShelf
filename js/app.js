// SoundShelf: las páginas .html piden los datos a api.php (que consulta MySQL) y los muestran.
// Cuando la página está lista, se llama a activarEfectos() (está en script.js).

// ==================== AYUDAS ====================

// Convierte un texto en texto seguro para meterlo en el HTML.
function e(texto) {
    if (texto == null) {
        texto = "";
    }
    return String(texto)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#39;");
}

// Lee un dato de la dirección. Ejemplo: en "detalle.html?id=4", leerDireccion("id") da "4".
function leerDireccion(nombre) {
    const parametros = new URLSearchParams(window.location.search);
    return parametros.get(nombre) || "";
}

// Manda al login y recuerda la página actual para volver después.
function irAlLogin() {
    const partes = window.location.pathname.split("/");
    const volver = partes[partes.length - 1] + window.location.search;
    window.location.replace("login.php?aviso=canciones&volver=" + encodeURIComponent(volver));
}

// Muestra un mensaje de error en lugar del contenido.
function mostrarError(titulo, mensaje) {
    document.title = titulo + " | SoundShelf";
    let html = "";
    html += '<section class="error-panel" aria-labelledby="error-titulo">';
    html += '<span class="error-symbol" aria-hidden="true">!</span>';
    html += '<p class="eyebrow">SoundShelf</p>';
    html += '<h1 id="error-titulo">' + e(titulo) + '</h1>';
    html += '<p class="muted">' + e(mensaje) + '</p>';
    html += '<div class="error-actions">';
    html += '<a class="button button-primary btn btn-accent" href="lista.html">Ir al catálogo</a>';
    html += '<a class="button button-secondary btn btn-outline-light" href="index.html">Volver al inicio</a>';
    html += '</div>';
    html += '</section>';
    document.getElementById("contenido").innerHTML = html;
}

// ==================== PEDIR DATOS A LA API ====================

// Pide datos a api.php. Si todo sale bien, llama a la función "mostrar" con los datos.
function pedirDatos(parametros, mostrar, tituloError) {
    fetch("api.php?" + parametros)
        .then(function (respuesta) {
            // 401 = no hay sesión: se va al login.
            if (respuesta.status == 401) {
                irAlLogin();
                return null;
            }
            return respuesta.json();
        })
        .then(function (datos) {
            if (datos == null) {
                return;
            }
            if (datos.sesion) {
                aplicarSesion(datos.sesion);
            }
            if (datos.error) {
                mostrarError(tituloError, datos.error);
            } else {
                mostrar(datos);
            }
            activarEfectos();
        })
        .catch(function (error) {
            console.error(error);
            mostrarError(tituloError, "No pudimos conectar con el catálogo. Abre la página desde http://localhost/soundshelf/ con Apache y MySQL encendidos en XAMPP.");
            activarEfectos();
        });
}

// Muestra en el menú si hay sesión y deja los datos que necesita script.js.
function aplicarSesion(sesion) {
    // Datos para script.js (favoritos).
    if (sesion.usuario) {
        document.body.dataset.sesion = "1";
    } else {
        document.body.dataset.sesion = "0";
    }
    document.body.dataset.favoritos = sesion.favoritos.join(",");

    // Token CSRF para los formularios y para favoritos.php.
    let meta = document.querySelector('meta[name="csrf-token"]');
    if (!meta) {
        meta = document.createElement("meta");
        meta.name = "csrf-token";
        document.head.appendChild(meta);
    }
    meta.content = sesion.csrf;

    // Botones del menú.
    let html = "";
    if (sesion.usuario) {
        html += '<span class="auth-greeting me-2">Hola, ' + e(sesion.usuario.nombre) + '</span>';
        html += '<form action="salir.php" method="post" class="m-0">';
        html += '<input type="hidden" name="csrf" value="' + e(sesion.csrf) + '">';
        html += '<button type="submit" class="btn btn-outline-light btn-sm rounded-pill px-3">Cerrar sesión</button>';
        html += '</form>';
    } else {
        html += '<a class="btn btn-outline-light btn-sm rounded-pill px-3" href="login.php">Iniciar sesión</a>';
        html += '<a class="btn btn-accent btn-sm rounded-pill px-3" href="registro.php">Crear cuenta</a>';
    }
    document.getElementById("auth-links").innerHTML = html;

    // El enlace "Mis favoritos" solo se ve con sesión.
    const soloConSesion = document.querySelectorAll("[data-solo-sesion]");
    for (let i = 0; i < soloConSesion.length; i++) {
        soloConSesion[i].hidden = !sesion.usuario;
    }
}

// ==================== PLANTILLAS ====================

// HTML de una tarjeta de canción.
function tarjeta(c) {
    const ficha = "detalle.html?id=" + c.id;
    let html = "";
    html += '<article class="track-card" data-id="' + c.id + '" data-busqueda="' + e(c.titulo + " " + c.artista) + '" data-genero="' + c.genero_id + '" data-titulo="' + e(c.titulo) + '" data-anio="' + c.anio + '" data-puntuacion="' + e(c.puntuacion) + '">';
    html += '<a class="track-cover-link" href="' + ficha + '" aria-label="Ver ficha de ' + e(c.titulo) + '">';
    html += '<div class="track-image"><img src="' + e(c.imagen) + '" alt="Portada de ' + e(c.album) + '" width="800" height="600" loading="lazy" data-respaldo="' + e(c.respaldo) + '"></div>';
    html += '</a>';
    html += '<div class="track-card-content">';
    html += '<div class="track-card-topline">';
    html += '<span class="pill pill-soft">' + e(c.genero) + '</span>';
    html += '<span class="rating" aria-label="Puntuación ' + e(c.puntuacion) + ' de 10"><span aria-hidden="true">★</span> ' + e(c.puntuacion) + '</span>';
    html += '</div>';
    html += '<h3 class="track-title"><a href="' + ficha + '">' + e(c.titulo) + '</a></h3>';
    html += '<p class="track-artist">' + e(c.artista) + '</p>';
    html += '<p class="track-subtitle">' + e(c.subtitulo) + '</p>';
    html += '<button type="button" class="favorite-button" data-favorito="' + c.id + '" aria-label="Añadir ' + e(c.titulo) + ' a favoritos" aria-pressed="false">♡ Favoritos</button>';
    html += '<a class="listen-card-link" href="' + e(c.enlaces["Spotify"]) + '" target="_blank" rel="noopener noreferrer" aria-label="Escuchar ' + e(c.titulo) + ' en Spotify"><span class="listen-icon" aria-hidden="true">▶</span>Escuchar</a>';
    html += '<div class="track-card-bottom">';
    html += '<span class="track-meta">' + e(c.duracion) + ' <span aria-hidden="true">·</span> ' + c.anio + '</span>';
    html += '<a class="text-link" href="' + ficha + '" aria-label="Ver detalle de ' + e(c.titulo) + '">Explorar <span aria-hidden="true">↗</span></a>';
    html += '</div>';
    html += '</div>';
    html += '</article>';
    return html;
}

// Varias tarjetas dentro de una cuadrícula.
function cuadricula(canciones) {
    let html = '<div class="track-grid row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">';
    for (let i = 0; i < canciones.length; i++) {
        html += tarjeta(canciones[i]);
    }
    html += '</div>';
    return html;
}

// Candado y botones para la vista sin sesión.
const CANDADO = '<svg class="bloqueo-icono" viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="11" width="14" height="10" rx="2"></rect><path d="M8 11V7a4 4 0 0 1 8 0v4"></path></svg>';
const BOTONES_ACCESO = '<div class="bloqueo-acciones"><a class="btn btn-accent rounded-pill px-4" href="login.php?volver=lista.html">Iniciar sesión</a><a class="btn btn-outline-light rounded-pill px-4" href="registro.php?volver=lista.html">Crear cuenta</a></div>';

// ==================== index.html ====================

function mostrarInicio(datos) {
    const conSesion = datos.sesion.usuario != null;
    const principal = datos.destacados[0];
    const hero = document.getElementById("hero-destacado");
    const totalCanciones = Number(datos.estadisticas.total_canciones);
    let html = "";

    // Canción destacada (arriba a la derecha).
    if (!principal) {
        hero.className = "empty-state";
        hero.innerHTML = '<h2>Tu colección empieza aquí</h2><p class="muted">Todavía no hay canciones registradas en la base de datos.</p>';
    } else if (!conSesion) {
        // Sin sesión: portada borrosa con un candado.
        hero.className = "hero-feature hero-bloqueado";
        html = "";
        html += '<div class="feature-image">';
        html += '<img src="' + e(principal.imagen) + '" alt="" width="800" height="600" fetchpriority="high" data-respaldo="' + e(principal.respaldo) + '">';
        html += '<div class="bloqueo-overlay">' + CANDADO;
        html += '<h2>Inicia sesión para escuchar</h2>';
        html += '<p>Crea una cuenta gratis y descubre las ' + totalCanciones + ' canciones del catálogo.</p>';
        html += BOTONES_ACCESO;
        html += '</div>';
        html += '</div>';
        hero.innerHTML = html;
    } else {
        hero.className = "hero-feature";
        html = "";
        html += '<a class="feature-image" href="detalle.html?id=' + principal.id + '" aria-label="Descubrir ' + e(principal.titulo) + '">';
        html += '<img src="' + e(principal.imagen) + '" alt="Portada de ' + e(principal.album) + '" width="800" height="600" fetchpriority="high" data-respaldo="' + e(principal.respaldo) + '">';
        html += '<div class="feature-overlay">';
        html += '<div class="feature-topline"><span class="pill">DESTACADO</span><span class="rating" aria-label="Puntuación ' + e(principal.puntuacion) + ' de 10">★ ' + e(principal.puntuacion) + '</span></div>';
        html += '<div class="feature-caption"><p class="feature-note">' + e(principal.genero) + ' / ' + e(principal.duracion) + '</p><h2>' + e(principal.titulo) + ' <span aria-hidden="true">↗</span></h2><p>' + e(principal.artista) + ' · ' + e(principal.album) + '</p></div>';
        html += '</div>';
        html += '</a>';
        hero.innerHTML = html;
    }

    // Cifras.
    document.getElementById("total-canciones").textContent = totalCanciones;
    document.getElementById("total-generos").textContent = Number(datos.estadisticas.total_generos);

    // Canciones destacadas.
    const destacados = document.getElementById("destacados");
    if (datos.destacados.length == 0) {
        destacados.innerHTML = '<p class="empty-state">El catálogo está vacío. Puedes agregar registros en MySQL.</p>';
    } else if (!conSesion) {
        html = "";
        html += '<article class="bloqueo-panel">';
        html += '<div class="bloqueo-portadas" aria-hidden="true">';
        for (let i = 0; i < datos.destacados.length; i++) {
            html += '<img src="' + e(datos.destacados[i].imagen) + '" alt="" width="120" height="120" loading="lazy" data-respaldo="' + e(datos.destacados[i].respaldo) + '">';
        }
        html += '</div>';
        html += '<div>';
        html += '<h3>Las canciones son solo para miembros</h3>';
        html += '<p class="muted">Inicia sesión para ver el catálogo completo, abrir cada ficha, escuchar en Spotify, YouTube o Apple Music y guardar tus favoritos.</p>';
        html += BOTONES_ACCESO;
        html += '</div>';
        html += '</article>';
        destacados.innerHTML = html;
    } else {
        destacados.innerHTML = cuadricula(datos.destacados);
    }

    // Géneros con su cantidad de canciones.
    html = "";
    for (let i = 0; i < datos.generos.length; i++) {
        const g = datos.generos[i];
        const total = Number(g.total);
        let texto = total + " canciones";
        if (total == 1) {
            texto = "1 canción";
        }
        html += '<a class="genre-card" href="lista.html?genero=' + g.id + '">';
        html += '<span class="genre-number">' + texto + ' <span aria-hidden="true">↗</span></span>';
        html += '<h3>' + e(g.nombre) + '</h3><p class="genre-description">' + e(g.descripcion) + '</p>';
        html += '</a>';
    }
    document.getElementById("lista-generos").innerHTML = html;
}

// ==================== lista.html ====================

function mostrarLista(datos) {
    // Se rellena el formulario con lo que viene en la dirección.
    document.getElementById("q").value = leerDireccion("q");
    const generoElegido = Number(leerDireccion("genero"));
    const selector = document.getElementById("genero");
    for (let i = 0; i < datos.generos.length; i++) {
        const g = datos.generos[i];
        selector.add(new Option(g.nombre, g.id, false, Number(g.id) == generoElegido));
    }

    const canciones = datos.canciones;
    const total = canciones.length;
    if (total == 1) {
        document.querySelector(".results-summary").textContent = "1 canción encontrada";
    } else {
        document.querySelector(".results-summary").textContent = total + " canciones encontradas";
    }

    const resultados = document.getElementById("resultados");
    let html = "";

    if (total == 0) {
        html += '<article class="empty-state"><p class="eyebrow">SIGUE EXPLORANDO</p><h3>No encontramos canciones con esos filtros</h3>';
        html += '<p class="muted">Prueba otro título, otro artista o un género diferente.</p>';
        html += '<a class="button button-primary btn btn-accent" href="lista.html">Ver todo el catálogo</a></article>';
        resultados.innerHTML = html;
        return;
    }

    // Tarjetas.
    html += '<div class="sort-row"><label for="orden-canciones">Ordenar por:</label><select id="orden-canciones"><option value="titulo">Título (A-Z)</option><option value="puntuacion">Mejor puntuación</option><option value="anio">Más recientes</option></select></div>';
    html += '<div id="catalogo-canciones" class="track-grid row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">';
    for (let i = 0; i < total; i++) {
        html += tarjeta(canciones[i]);
    }
    html += '</div>';
    html += '<p id="mensaje-sin-resultados-js" class="empty-state" hidden>No hay canciones que coincidan. Prueba otra búsqueda.</p>';
    resultados.innerHTML = html;

    // Filas de la tabla.
    html = "";
    for (let i = 0; i < total; i++) {
        const c = canciones[i];
        html += '<tr data-id="' + c.id + '">';
        html += '<td><span class="table-title">' + e(c.titulo) + '</span><span class="table-subtitle">' + e(c.artista) + '</span></td>';
        html += '<td>' + e(c.genero) + '</td>';
        html += '<td>' + e(c.album) + '</td>';
        html += '<td>' + e(c.duracion) + '</td>';
        html += '<td>' + c.anio + '</td>';
        html += '<td><span class="rating">★ ' + e(c.puntuacion) + '<span class="sr-only"> de 10</span></span></td>';
        html += '<td><a class="button button-secondary button-small" href="detalle.html?id=' + c.id + '" aria-label="Ver ficha de ' + e(c.titulo) + '">Ver ficha <span aria-hidden="true">↗</span></a></td>';
        html += '</tr>';
    }
    document.querySelector("#tabla-canciones tbody").innerHTML = html;
    document.getElementById("seccion-tabla").hidden = false;
}

// ==================== detalle.html ====================

function mostrarDetalle(datos) {
    const c = datos.cancion;
    const enlaceGenero = "lista.html?genero=" + c.genero_id;
    document.title = c.titulo + " | SoundShelf";
    document.getElementById("miga-titulo").textContent = c.titulo;

    // Botones para escuchar (Spotify, YouTube, Apple Music).
    let botones = "";
    for (const plataforma in c.enlaces) {
        const clase = "listen-" + plataforma.toLowerCase().replace(/ /g, "-");
        botones += '<a class="listen-link ' + clase + '" href="' + e(c.enlaces[plataforma]) + '" target="_blank" rel="noopener noreferrer"><span class="listen-icon" aria-hidden="true">▶</span>' + e(plataforma) + '</a>';
    }

    let html = "";
    html += '<section class="detail-hero" aria-labelledby="cancion-titulo">';
    html += '<div class="detail-copy">';
    html += '<p class="eyebrow">DETRÁS DE CADA MELODÍA</p>';
    html += '<div class="detail-tags"><a class="pill pill-soft" href="' + enlaceGenero + '">' + e(c.genero) + '</a><span class="rating" aria-label="Puntuación ' + e(c.puntuacion) + ' de 10">★ ' + e(c.puntuacion) + ' / 10</span></div>';
    html += '<h1 id="cancion-titulo" class="detail-title">' + e(c.titulo) + '</h1>';
    html += '<p class="detail-subtitle">' + e(c.subtitulo) + '</p>';
    html += '<p class="track-meta">' + e(c.artista) + ' <span aria-hidden="true">·</span> ' + c.anio + '</p>';
    html += '<button type="button" class="favorite-button" data-favorito="' + c.id + '" aria-pressed="false">♡ Favoritos</button>';
    html += '<div class="hero-actions"><a class="button button-primary btn btn-accent" href="' + enlaceGenero + '">Más de ' + e(c.genero) + ' <span aria-hidden="true">↗</span></a><a class="button button-secondary btn btn-outline-light" href="lista.html">Volver al catálogo</a></div>';
    html += '<div class="listen-links" aria-label="Escuchar la canción"><span class="listen-label">Escúchala en</span>' + botones + '</div>';
    html += '</div>';
    html += '<div class="detail-art"><img src="' + e(c.imagen) + '" alt="Portada del álbum ' + e(c.album) + '" width="800" height="600" fetchpriority="high" data-respaldo="' + e(c.respaldo) + '"></div>';
    html += '</section>';

    html += '<div class="detail-layout">';
    html += '<section class="detail-description detail-section" aria-labelledby="historia-titulo">';
    html += '<p class="eyebrow">CONOCE SU HISTORIA</p>';
    html += '<h2 id="historia-titulo" class="section-title">Acerca de la canción</h2>';
    html += '<p>' + e(c.descripcion).replace(/\n/g, "<br>") + '</p>';
    html += '<p class="muted">Una mirada a su estilo musical y a la historia detrás de su creación.</p>';
    html += '</section>';
    html += '<section class="detail-facts detail-section" aria-labelledby="ficha-titulo">';
    html += '<h2 id="ficha-titulo">Ficha de la canción</h2>';
    html += '<dl>';
    html += '<div><dt>Género</dt><dd>' + e(c.genero) + '</dd></div>';
    html += '<div><dt>Artista</dt><dd>' + e(c.artista) + '</dd></div>';
    html += '<div><dt>Álbum</dt><dd>' + e(c.album) + '</dd></div>';
    html += '<div><dt>Duración</dt><dd>' + e(c.duracion) + '</dd></div>';
    html += '<div><dt>Año de lanzamiento</dt><dd>' + c.anio + '</dd></div>';
    html += '<div><dt>Puntuación del catálogo</dt><dd class="accent">' + e(c.puntuacion) + ' / 10</dd></div>';
    html += '</dl>';
    html += '</section>';
    html += '</div>';
    document.getElementById("ficha").innerHTML = html;

    // Canciones relacionadas (del mismo género) o recomendadas.
    if (datos.relacionados.length > 0) {
        html = '<div class="section-heading"><div><p class="eyebrow">SIGUE DESCUBRIENDO</p>';
        if (datos.mismoGenero) {
            html += '<h2 id="relacionados-titulo" class="section-title">Más canciones del mismo género</h2>';
            html += '<p class="section-description">Otras canciones de ' + e(c.genero) + ' para explorar.</p></div>';
            html += '<a class="text-link" href="' + enlaceGenero + '">Ver género <span aria-hidden="true">→</span></a>';
        } else {
            html += '<h2 id="relacionados-titulo" class="section-title">Otras canciones que te pueden gustar</h2>';
            html += '<p class="section-description">Las mejor puntuadas del catálogo.</p></div>';
            html += '<a class="text-link" href="lista.html">Ver catálogo <span aria-hidden="true">→</span></a>';
        }
        html += '</div>';
        html += cuadricula(datos.relacionados);

        const seccion = document.getElementById("seccion-relacionados");
        seccion.innerHTML = html;
        seccion.hidden = false;
    }
}

// ==================== ARRANQUE ====================
// Según la página (data-pagina en <body>) se piden unos datos u otros.

const pagina = document.body.dataset.pagina;

if (pagina == "inicio") {
    pedirDatos("accion=inicio", mostrarInicio, "No pudimos cargar el catálogo");
} else if (pagina == "lista") {
    const parametros = "accion=catalogo&q=" + encodeURIComponent(leerDireccion("q")) + "&genero=" + encodeURIComponent(leerDireccion("genero") || "0");
    pedirDatos(parametros, mostrarLista, "No pudimos cargar el catálogo");
} else if (pagina == "detalle") {
    pedirDatos("accion=cancion&id=" + encodeURIComponent(leerDireccion("id")), mostrarDetalle, "Esta canción no está disponible");
}

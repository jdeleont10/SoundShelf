// Funciones que usan index.html, lista.html y detalle.html.

// Token de la sesión: va en los formularios (favoritos y cerrar sesión). Lo pone pintarMenu().
let tokenSesion = "";

// Convierte un texto en texto seguro para meterlo en el HTML (por si trae < > " o ').
function e(texto) {
    return String(texto)
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#39;");
}

// Nombre de la página actual con su búsqueda, por ejemplo "lista.html?genero=2".
function paginaActual() {
    let partes = window.location.pathname.split("/");
    return partes[partes.length - 1] + window.location.search;
}

// Manda al login y recuerda la página actual para volver después.
function irAlLogin() {
    window.location.href = "login.php?aviso=1&volver=" + encodeURIComponent(paginaActual());
}

// Pone en el menú el saludo y "Cerrar sesión", o los botones para entrar.
function pintarMenu(sesion) {
    let menu = document.getElementById("menu-usuario");
    let html = "";
    tokenSesion = sesion.token;

    if (sesion.nombre != "") {
        html += '<li class="nav-item"><a class="nav-link" href="mis-favoritos.php">Mis favoritos <span class="badge contador">' + sesion.favoritos.length + '</span></a></li>';
        html += '<li class="nav-item"><span class="navbar-text ms-lg-3" id="nombre-usuario"></span></li>';
        html += '<li class="nav-item"><form action="salir.php" method="post" class="d-inline">';
        html += '<input type="hidden" name="token" value="' + tokenSesion + '">';
        html += '<button type="submit" class="btn btn-outline-light btn-sm ms-lg-3">Cerrar sesión</button></form></li>';
    } else {
        html += '<li class="nav-item"><a class="btn btn-outline-light btn-sm ms-lg-3" href="login.php">Iniciar sesión</a></li>';
        html += '<li class="nav-item"><a class="btn btn-lila btn-sm ms-lg-2 mt-2 mt-lg-0" href="registro.php">Crear cuenta</a></li>';
    }
    menu.innerHTML = html;

    // El nombre se pone con textContent para que se vea tal cual lo escribió el usuario.
    if (sesion.nombre != "") {
        document.getElementById("nombre-usuario").textContent = "Hola, " + sesion.nombre;
    }
}

// Devuelve el HTML de la tarjeta de una canción.
// "favoritos" es la lista con los id de las canciones favoritas del usuario.
function tarjeta(cancion, favoritos) {
    let ficha = "detalle.html?id=" + cancion.id;

    // ¿Esta canción es favorita?
    let esFavorita = false;
    for (let i = 0; i < favoritos.length; i++) {
        if (favoritos[i] == cancion.id) {
            esFavorita = true;
        }
    }

    let html = "";
    html += '<div class="col">';
    html += '<article class="card tarjeta h-100">';
    html += '<a href="' + ficha + '"><img src="' + e(cancion.imagen) + '" class="card-img-top" alt="Portada de ' + e(cancion.album) + '" onerror="this.src=\'' + e(cancion.respaldo) + '\'"></a>';
    html += '<div class="card-body">';
    html += '<div class="d-flex justify-content-between mb-2">';
    html += '<span class="badge etiqueta">' + e(cancion.genero) + '</span>';
    html += '<span class="estrella">' + cancion.puntuacion + ' / 10</span>';
    html += '</div>';
    html += '<h5 class="card-title"><a href="' + ficha + '">' + e(cancion.titulo) + '</a></h5>';
    html += '<p class="artista mb-1">' + e(cancion.artista) + '</p>';
    html += '<p class="texto-suave small">' + e(cancion.subtitulo) + '</p>';

    // Botón de favoritos: envía el id de la canción a favoritos.php
    html += '<form action="favoritos.php" method="post" class="d-inline">';
    html += '<input type="hidden" name="cancion" value="' + cancion.id + '">';
    html += '<input type="hidden" name="token" value="' + tokenSesion + '">';
    html += '<input type="hidden" name="volver" value="' + e(paginaActual()) + '">';
    if (esFavorita) {
        html += '<button type="submit" class="btn btn-sm boton-favorito marcado">Favorita</button>';
    } else {
        html += '<button type="submit" class="btn btn-sm boton-favorito">Favoritos</button>';
    }
    html += '</form> ';
    // Botón Escuchar: carga la canción en el reproductor de Spotify de abajo (función en script.js)
    html += '<button type="button" class="btn btn-sm btn-spotify" onclick="reproducir(\'' + cancion.spotify_id + '\')">Escuchar</button>';
    html += '</div>';

    html += '<div class="card-footer d-flex justify-content-between small">';
    html += '<span class="texto-suave">' + cancion.duracion + ' · ' + cancion.anio + '</span>';
    html += '<a class="lila" href="' + ficha + '">Explorar</a>';
    html += '</div>';
    html += '</article>';
    html += '</div>';
    return html;
}

// Lee un dato de la dirección. Ejemplo: en "detalle.html?id=4", leerDireccion("id") da "4".
function leerDireccion(nombre) {
    let parametros = new URLSearchParams(window.location.search);
    let valor = parametros.get(nombre);
    if (valor == null) {
        return "";
    }
    return valor;
}

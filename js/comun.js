// Funciones que usan index.html, lista.html y detalle.html.

// Pone en el menú el saludo y "Cerrar sesión", o los botones para entrar.
function pintarMenu(sesion) {
    let menu = document.getElementById("menu-usuario");
    let html = "";

    if (sesion.nombre != "") {
        html += '<li class="nav-item"><a class="nav-link" href="mis-favoritos.php">Mis favoritos <span class="badge contador">' + sesion.favoritos.length + '</span></a></li>';
        html += '<li class="nav-item"><span class="navbar-text ms-lg-3" id="nombre-usuario"></span></li>';
        html += '<li class="nav-item"><a class="btn btn-outline-light btn-sm ms-lg-3" href="salir.php">Cerrar sesión</a></li>';
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
    html += '<div class="card tarjeta h-100">';
    html += '<a href="' + ficha + '"><img src="' + cancion.imagen + '" class="card-img-top" alt="Portada de ' + cancion.album + '" onerror="this.src=\'' + cancion.respaldo + '\'"></a>';
    html += '<div class="card-body">';
    html += '<div class="d-flex justify-content-between mb-2">';
    html += '<span class="badge etiqueta">' + cancion.genero + '</span>';
    html += '<span class="estrella">' + cancion.puntuacion + ' / 10</span>';
    html += '</div>';
    html += '<h5 class="card-title"><a href="' + ficha + '">' + cancion.titulo + '</a></h5>';
    html += '<p class="artista mb-1">' + cancion.artista + '</p>';
    html += '<p class="texto-suave small">' + cancion.subtitulo + '</p>';

    // Botón de favoritos: envía el id de la canción a favoritos.php
    html += '<form action="favoritos.php" method="post" class="d-inline">';
    html += '<input type="hidden" name="cancion" value="' + cancion.id + '">';
    if (esFavorita) {
        html += '<button type="submit" class="btn btn-sm boton-favorito marcado">Favorita</button>';
    } else {
        html += '<button type="submit" class="btn btn-sm boton-favorito">Favoritos</button>';
    }
    html += '</form> ';
    html += '<a class="btn btn-sm btn-spotify" href="' + cancion.spotify + '" target="_blank">Escuchar</a>';
    html += '</div>';

    html += '<div class="card-footer d-flex justify-content-between small">';
    html += '<span class="texto-suave">' + cancion.duracion + ' · ' + cancion.anio + '</span>';
    html += '<a class="lila" href="' + ficha + '">Explorar</a>';
    html += '</div>';
    html += '</div>';
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

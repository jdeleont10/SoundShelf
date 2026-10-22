// detalle.html: pide una canción a api/detalle.php y muestra su ficha.
// El id viene en la dirección, por ejemplo detalle.html?id=1

let id = leerDireccion("id");

fetch("api/detalle.php?id=" + id)
    .then(function (respuesta) {
        return respuesta.json();
    })
    .then(function (datos) {
        // Sin sesión no se pueden ver las fichas.
        if (datos.necesitaLogin) {
            window.location.href = "login.php?aviso=1";
            return;
        }
        pintarMenu(datos.sesion);

        if (datos.cancion == null) {
            mostrarNoEncontrada();
        } else {
            mostrarFicha(datos);
        }
    })
    .catch(function () {
        document.getElementById("contenido").innerHTML = '<div class="alert alert-danger seccion">No se pudo cargar la canción. Revisa que Apache y MySQL estén encendidos en XAMPP.</div>';
    });

// Cuando no existe una canción con ese id.
function mostrarNoEncontrada() {
    document.title = "Canción no encontrada | SoundShelf";

    let html = "";
    html += '<div class="caja seccion">';
    html += '<p class="error-icono">!</p>';
    html += '<h1>Esta canción no está disponible</h1>';
    html += '<p class="texto-suave">No encontramos una canción con ese identificador. Explora las otras fichas del catálogo.</p>';
    html += '<a class="btn btn-lila me-2" href="lista.html">Ir al catálogo</a>';
    html += '<a class="btn btn-outline-light" href="index.html">Volver al inicio</a>';
    html += '</div>';
    document.getElementById("contenido").innerHTML = html;
}

function mostrarFicha(datos) {
    let c = datos.cancion;
    let enlaceGenero = "lista.html?genero=" + c.genero_id;
    document.title = c.titulo + " | SoundShelf";

    // ¿Es favorita?
    let esFavorita = false;
    for (let i = 0; i < datos.sesion.favoritos.length; i++) {
        if (datos.sesion.favoritos[i] == c.id) {
            esFavorita = true;
        }
    }

    let html = "";
    html += '<p class="migas"><a href="index.html">Inicio</a> / <a href="lista.html">Catálogo</a> / ' + c.titulo + '</p>';

    // La ficha de la canción va dentro de un <article>.
    html += '<article>';

    // Parte de arriba: datos principales y portada.
    html += '<section class="row align-items-center seccion-chica">';
    html += '<div class="col-lg-6">';
    html += '<p class="subtitulo-chico">DETRÁS DE CADA MELODÍA</p>';
    html += '<a class="badge etiqueta" href="' + enlaceGenero + '">' + c.genero + '</a>';
    html += '<span class="estrella ms-2">' + c.puntuacion + ' / 10</span>';
    html += '<h1 class="titulo-grande mt-3">' + c.titulo + '</h1>';
    html += '<p class="texto-suave">' + c.subtitulo + '</p>';
    html += '<p class="small">' + c.artista + ' · ' + c.anio + '</p>';

    // Botón de favoritos: envía el id de la canción a favoritos.php
    html += '<form action="favoritos.php" method="post" class="mb-3">';
    html += '<input type="hidden" name="cancion" value="' + c.id + '">';
    if (esFavorita) {
        html += '<button type="submit" class="btn boton-favorito marcado">Favorita</button>';
    } else {
        html += '<button type="submit" class="btn boton-favorito">Favoritos</button>';
    }
    html += '</form>';

    html += '<a class="btn btn-lila mb-2 me-1" href="' + enlaceGenero + '">Más de ' + c.genero + '</a>';
    html += '<a class="btn btn-outline-light mb-2 me-1" href="lista.html">Volver al catálogo</a>';
    html += '<button type="button" id="boton-copiar" class="btn btn-outline-light mb-2" onclick="copiarEnlace()">Copiar enlace</button>';

    html += '<p class="subtitulo-chico mt-4">ESCÚCHALA EN</p>';
    html += '<button type="button" class="btn btn-spotify mb-2 me-1" onclick="reproducir(\'' + c.spotify_id + '\')">Spotify</button>';
    html += '<a class="btn btn-youtube mb-2 me-1" href="' + c.youtube + '" target="_blank">YouTube</a>';
    if (c.enlace_apple != "") {
        html += '<a class="btn btn-apple mb-2" href="' + c.enlace_apple + '" target="_blank">Apple Music</a>';
    }
    html += '</div>';
    html += '<div class="col-lg-6 mt-4 mt-lg-0">';
    html += '<img src="' + c.imagen + '" class="portada-grande" alt="Portada del álbum ' + c.album + '" onerror="this.src=\'' + c.respaldo + '\'">';
    html += '</div>';
    html += '</section>';

    // Historia y ficha técnica.
    html += '<section class="row seccion">';
    html += '<div class="col-lg-7">';
    html += '<p class="subtitulo-chico">CONOCE SU HISTORIA</p>';
    html += '<h2>Acerca de la canción</h2>';
    html += '<p class="texto-suave">' + c.descripcion.replaceAll("\n", "<br>") + '</p>';
    html += '</div>';
    html += '<div class="col-lg-5">';
    html += '<div class="caja">';
    html += '<h4>Ficha de la canción</h4>';
    html += '<table class="table mb-0">';
    html += '<tr><td class="texto-suave">Género</td><td class="text-end">' + c.genero + '</td></tr>';
    html += '<tr><td class="texto-suave">Artista</td><td class="text-end">' + c.artista + '</td></tr>';
    html += '<tr><td class="texto-suave">Álbum</td><td class="text-end">' + c.album + '</td></tr>';
    html += '<tr><td class="texto-suave">Duración</td><td class="text-end">' + c.duracion + '</td></tr>';
    html += '<tr><td class="texto-suave">Año de lanzamiento</td><td class="text-end">' + c.anio + '</td></tr>';
    html += '<tr><td class="texto-suave">Puntuación</td><td class="text-end lila">' + c.puntuacion + ' / 10</td></tr>';
    html += '</table>';
    html += '</div>';
    html += '</div>';
    html += '</section>';
    html += '</article>';

    // Canciones relacionadas.
    html += '<section class="seccion">';
    html += '<p class="subtitulo-chico">SIGUE DESCUBRIENDO</p>';
    if (datos.mismoGenero) {
        html += '<h2>Más canciones del mismo género</h2>';
    } else {
        html += '<h2>Otras canciones que te pueden gustar</h2>';
    }
    html += '<div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 mt-1">';
    for (let i = 0; i < datos.relacionadas.length; i++) {
        html += tarjeta(datos.relacionadas[i], datos.sesion.favoritos);
    }
    html += '</div>';
    html += '</section>';

    document.getElementById("contenido").innerHTML = html;
}

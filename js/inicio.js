// index.html: pide los datos a api/inicio.php y los pone en la página.

fetch("api/inicio.php")
    .then(function (respuesta) {
        return respuesta.json();
    })
    .then(function (datos) {
        pintarMenu(datos.sesion);
        mostrarDestacada(datos);
        mostrarCifras(datos);
        mostrarDestacadas(datos);
        mostrarGeneros(datos);
    })
    .catch(function () {
        document.getElementById("destacadas").innerHTML = '<div class="alert alert-danger">No se pudieron cargar las canciones. Revisa que Apache y MySQL estén encendidos en XAMPP.</div>';
    });

// La canción mejor puntuada (arriba a la derecha).
function mostrarDestacada(datos) {
    if (datos.destacadas.length == 0) {
        return;
    }
    let principal = datos.destacadas[0];
    let html = "";

    if (datos.sesion.nombre != "") {
        // Con sesión: la portada con su título y un enlace a la ficha.
        html += '<a href="detalle.html?id=' + principal.id + '" class="destacada">';
        html += '<img src="' + principal.imagen + '" alt="Portada de ' + principal.album + '" onerror="this.src=\'' + principal.respaldo + '\'">';
        html += '<div class="destacada-texto">';
        html += '<span class="badge etiqueta">DESTACADO</span>';
        html += '<span class="estrella float-end">' + principal.puntuacion + ' / 10</span>';
        html += '<h2 class="mt-3">' + principal.titulo + '</h2>';
        html += '<p>' + principal.artista + ' · ' + principal.album + '</p>';
        html += '</div>';
        html += '</a>';
    } else {
        // Sin sesión: la portada se ve borrosa y encima va el aviso.
        html += '<div class="destacada">';
        html += '<img src="' + principal.imagen + '" alt="" class="borrosa" onerror="this.src=\'' + principal.respaldo + '\'">';
        html += '<div class="destacada-bloqueada">';
        html += '';
        html += '<h2>Inicia sesión para escuchar</h2>';
        html += '<p>Crea una cuenta gratis y descubre las ' + datos.totalCanciones + ' canciones del catálogo.</p>';
        html += '<div><a class="btn btn-lila" href="login.php">Iniciar sesión</a>';
        html += '<a class="btn btn-outline-light" href="registro.php">Crear cuenta</a></div>';
        html += '</div>';
        html += '</div>';
    }
    document.getElementById("destacada").innerHTML = html;
}

function mostrarCifras(datos) {
    document.getElementById("total-canciones").innerHTML = datos.totalCanciones;
    document.getElementById("total-generos").innerHTML = datos.totalGeneros;
}

// Las 3 mejor puntuadas (solo con sesión).
function mostrarDestacadas(datos) {
    let html = "";

    if (datos.sesion.nombre != "") {
        html += '<div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">';
        for (let i = 0; i < datos.destacadas.length; i++) {
            html += tarjeta(datos.destacadas[i], datos.sesion.favoritos);
        }
        html += '</div>';
    } else {
        html += '<div class="caja">';
        html += '<h3>Las canciones son solo para miembros</h3>';
        html += '<p class="texto-suave">Inicia sesión para ver el catálogo completo, abrir cada ficha, escuchar en Spotify, YouTube o Apple Music y guardar tus favoritos.</p>';
        html += '<a class="btn btn-lila me-2" href="login.php">Iniciar sesión</a>';
        html += '<a class="btn btn-outline-light" href="registro.php">Crear cuenta</a>';
        html += '</div>';
    }
    document.getElementById("destacadas").innerHTML = html;
}

// Una tarjeta por género, con cuántas canciones tiene.
function mostrarGeneros(datos) {
    let html = "";
    for (let i = 0; i < datos.generos.length; i++) {
        let genero = datos.generos[i];

        let cantidad = genero.total + " canciones";
        if (genero.total == 1) {
            cantidad = "1 canción";
        }

        html += '<div class="col">';
        html += '<a class="card tarjeta-genero h-100" href="lista.html?genero=' + genero.id + '">';
        html += '<div class="card-body">';
        html += '<p class="lila small">' + cantidad + '</p>';
        html += '<h5>' + genero.nombre + '</h5>';
        html += '<p class="texto-suave small mb-0">' + genero.descripcion + '</p>';
        html += '</div>';
        html += '</a>';
        html += '</div>';
    }
    document.getElementById("lista-generos").innerHTML = html;
}

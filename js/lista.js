// lista.html: pide las canciones a api/lista.php con los filtros de la dirección
// (por ejemplo lista.html?q=montesanto&genero=1&orden=puntuacion) y las muestra.

let q = leerDireccion("q");
let genero = leerDireccion("genero");
let orden = leerDireccion("orden");

// El formulario muestra lo que se buscó.
document.getElementById("q").value = q;
if (orden != "") {
    document.getElementById("orden").value = orden;
}

fetch("api/lista.php?q=" + encodeURIComponent(q) + "&genero=" + genero + "&orden=" + orden)
    .then(function (respuesta) {
        return respuesta.json();
    })
    .then(function (datos) {
        // Sin sesión no se puede ver el catálogo.
        if (datos.necesitaLogin) {
            window.location.href = "login.php?aviso=1";
            return;
        }
        pintarMenu(datos.sesion);
        mostrarGeneros(datos.generos);
        mostrarCanciones(datos.canciones, datos.sesion.favoritos);
        mostrarTabla(datos.canciones);
    })
    .catch(function () {
        document.getElementById("resultados").innerHTML = '<div class="alert alert-danger">No se pudo cargar el catálogo. Revisa que Apache y MySQL estén encendidos en XAMPP.</div>';
    });

// Opciones del menú desplegable de géneros.
function mostrarGeneros(generos) {
    let html = '<option value="0">Todos los géneros</option>';
    for (let i = 0; i < generos.length; i++) {
        if (generos[i].id == genero) {
            html += '<option value="' + generos[i].id + '" selected>' + generos[i].nombre + '</option>';
        } else {
            html += '<option value="' + generos[i].id + '">' + generos[i].nombre + '</option>';
        }
    }
    document.getElementById("genero").innerHTML = html;
}

// Tarjetas de las canciones encontradas.
function mostrarCanciones(canciones, favoritos) {
    if (canciones.length == 1) {
        document.getElementById("cantidad").innerHTML = "1 canción encontrada";
    } else {
        document.getElementById("cantidad").innerHTML = canciones.length + " canciones encontradas";
    }

    let html = "";
    if (canciones.length == 0) {
        html += '<div class="caja text-center">';
        html += '<h3>No encontramos canciones con esos filtros</h3>';
        html += '<p class="texto-suave">Prueba otro título, otro artista o un género diferente.</p>';
        html += '<a class="btn btn-lila" href="lista.html">Ver todo el catálogo</a>';
        html += '</div>';
    } else {
        html += '<div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">';
        for (let i = 0; i < canciones.length; i++) {
            html += tarjeta(canciones[i], favoritos);
        }
        html += '</div>';
    }
    document.getElementById("resultados").innerHTML = html;
}

// Una fila de la tabla por cada canción.
function mostrarTabla(canciones) {
    // Si no hay canciones, la tabla no se muestra.
    if (canciones.length == 0) {
        document.getElementById("seccion-tabla").style.display = "none";
        return;
    }

    let html = "";
    for (let i = 0; i < canciones.length; i++) {
        let c = canciones[i];
        html += '<tr>';
        html += '<td><strong>' + c.titulo + '</strong><br><span class="texto-suave small">' + c.artista + '</span></td>';
        html += '<td>' + c.genero + '</td>';
        html += '<td>' + c.album + '</td>';
        html += '<td>' + c.duracion + '</td>';
        html += '<td>' + c.anio + '</td>';
        html += '<td class="estrella">' + c.puntuacion + ' / 10</td>';
        html += '<td><a class="btn btn-sm btn-outline-light" href="detalle.html?id=' + c.id + '">Ver ficha</a></td>';
        html += '</tr>';
    }
    document.getElementById("filas-tabla").innerHTML = html;
}

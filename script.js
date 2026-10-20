// SoundShelf: efectos y comportamiento dinámico de la página.
// Todo empieza en la función activarEfectos().
// - En las páginas PHP (login, registro, mis favoritos) se llama cuando carga la página.
// - En las páginas .html se llama desde js/app.js, cuando ya llegaron los datos de la base de datos.

function activarEfectos() {
    crearContadorFavoritos();
    activarFavoritos();
    activarFiltros();
    activarAnimaciones();
    activarMostrarContrasena();
    mostrarSaludo();
    activarBarraYBotonArriba();
    activarValidacionRegistro();
    activarCopiarEnlace();
    activarPortadasDeRespaldo();
}

document.addEventListener("DOMContentLoaded", function () {
    // Las páginas .html tienen data-pagina en <body>: a ellas las activa js/app.js.
    if (!document.body.dataset.pagina) {
        activarEfectos();
    }
});

// ==================== AVISOS (mensajes que aparecen abajo a la derecha) ====================

let aviso = null;
let temporizadorAviso = null;

function notificar(texto) {
    if (aviso == null) {
        aviso = document.createElement("div");
        aviso.className = "js-toast";
        aviso.setAttribute("role", "status");
        document.body.appendChild(aviso);
    }
    aviso.textContent = texto;
    aviso.classList.add("visible");

    // El aviso se oculta solo después de 2,3 segundos.
    clearTimeout(temporizadorAviso);
    temporizadorAviso = setTimeout(function () {
        aviso.classList.remove("visible");
    }, 2300);
}

// ==================== CONTADOR DE FAVORITOS EN EL MENÚ ====================

let contador = null;

function crearContadorFavoritos() {
    const menu = document.querySelector(".navbar-nav");
    if (!menu) {
        return;
    }
    // Con sesión el contador es un enlace a "Mis favoritos"; sin sesión es solo texto.
    if (document.body.dataset.sesion == "1") {
        contador = document.createElement("a");
        contador.href = "mis-favoritos.php";
    } else {
        contador = document.createElement("span");
    }
    contador.className = "contador-favoritos";
    menu.appendChild(contador);
}

function actualizarContador(cantidad, animar) {
    if (contador == null) {
        return;
    }
    contador.textContent = "♥ " + cantidad;
    if (cantidad == 1) {
        contador.title = "1 canción favorita";
    } else {
        contador.title = cantidad + " canciones favoritas";
    }
    // Pequeño "latido" cuando cambia: se pone la clase y se quita al terminar la animación.
    if (animar) {
        contador.classList.add("latido");
        setTimeout(function () {
            contador.classList.remove("latido");
        }, 400);
    }
}

// ==================== FAVORITOS ====================
// Se guardan en MySQL a través de favoritos.php (solo con sesión).

let favoritos = [];

// Pinta los botones ♥ según la lista de favoritos.
function actualizarBotonesFavoritos(animar) {
    const botones = document.querySelectorAll("[data-favorito]");
    for (let i = 0; i < botones.length; i++) {
        const boton = botones[i];
        const activo = favoritos.indexOf(boton.dataset.favorito) != -1;
        if (activo) {
            boton.textContent = "♥ Favorita";
            boton.classList.add("es-favorita");
            boton.setAttribute("aria-pressed", "true");
        } else {
            boton.textContent = "♡ Favoritos";
            boton.classList.remove("es-favorita");
            boton.setAttribute("aria-pressed", "false");
        }
    }
    actualizarContador(favoritos.length, animar);
}

function activarFavoritos() {
    // La lista inicial viene en la página, por ejemplo data-favoritos="1,4,7".
    const texto = document.body.dataset.favoritos;
    if (texto) {
        favoritos = texto.split(",");
    }
    actualizarBotonesFavoritos(false);

    const botones = document.querySelectorAll("[data-favorito]");
    for (let i = 0; i < botones.length; i++) {
        botones[i].addEventListener("click", function () {
            cambiarFavorito(this);
        });
    }
}

// Agrega o quita una canción de favoritos en la base de datos.
function cambiarFavorito(boton) {
    if (document.body.dataset.sesion != "1") {
        notificar("Inicia sesión para guardar favoritos.");
        return;
    }

    const id = boton.dataset.favorito;
    const yaEstaba = favoritos.indexOf(id) != -1;
    let accion = "agregar";
    if (yaEstaba) {
        accion = "quitar";
    }

    const datos = new FormData();
    datos.append("accion", accion);
    datos.append("cancion", id);

    boton.disabled = true;
    fetch("favoritos.php", { method: "POST", body: datos })
        .then(function (respuesta) {
            return respuesta.json();
        })
        .then(function (respuesta) {
            boton.disabled = false;
            if (respuesta.error) {
                notificar(respuesta.error);
                return;
            }
            // El servidor manda la lista con números; se pasan a texto para compararlos con los botones.
            favoritos = [];
            for (let i = 0; i < respuesta.favoritos.length; i++) {
                favoritos.push(String(respuesta.favoritos[i]));
            }
            actualizarBotonesFavoritos(true);
            if (yaEstaba) {
                notificar("Canción quitada de tu cuenta");
                quitarTarjetaDeMisFavoritos(id);
            } else {
                notificar("Canción guardada en tu cuenta");
            }
        })
        .catch(function () {
            boton.disabled = false;
            notificar("No se pudo guardar. Intenta de nuevo.");
        });
}

// En "Mis favoritos", la tarjeta desaparece cuando se quita.
function quitarTarjetaDeMisFavoritos(id) {
    const lista = document.getElementById("mis-favoritos");
    if (!lista) {
        return;
    }
    const tarjeta = lista.querySelector('.track-card[data-id="' + id + '"]');
    if (!tarjeta) {
        return;
    }
    tarjeta.classList.add("js-saliendo");
    setTimeout(function () {
        tarjeta.remove();
        const restantes = lista.querySelectorAll(".track-card").length;
        if (restantes == 1) {
            document.getElementById("total-favoritos").textContent = "1 canción";
        } else {
            document.getElementById("total-favoritos").textContent = restantes + " canciones";
        }
        document.getElementById("favoritos-vacio").hidden = restantes != 0;
    }, 300);
}

// ==================== FILTRO EN VIVO Y ORDEN (catálogo) ====================

// Pasa a minúsculas y quita las tildes, para que "cancion" encuentre "Canción".
function normalizar(texto) {
    texto = texto.toLowerCase();
    const conTilde = "áéíóúüñ";
    const sinTilde = "aeiouun";
    let resultado = "";
    for (let i = 0; i < texto.length; i++) {
        const posicion = conTilde.indexOf(texto[i]);
        if (posicion != -1) {
            resultado += sinTilde[posicion];
        } else {
            resultado += texto[i];
        }
    }
    return resultado;
}

// Quita los signos ¿ y ¡, para ordenar "¿Sabes?" en la S.
function sinSignos(titulo) {
    return titulo.replace("¿", "").replace("¡", "");
}

function activarFiltros() {
    const formulario = document.querySelector(".filter-form");
    const cuadricula = document.getElementById("catalogo-canciones");
    if (!formulario || !cuadricula) {
        return;
    }

    const campo = document.getElementById("q");
    const genero = document.getElementById("genero");
    const orden = document.getElementById("orden-canciones");
    const resumen = document.querySelector(".results-summary");
    const mensaje = document.getElementById("mensaje-sin-resultados-js");
    const cuerpoTabla = document.querySelector("#tabla-canciones tbody");
    const filas = document.querySelectorAll("#tabla-canciones tbody tr");

    // Las tarjetas se copian a un arreglo normal para poder ordenarlas con sort().
    const tarjetas = [];
    const encontradas = cuadricula.querySelectorAll(".track-card");
    for (let i = 0; i < encontradas.length; i++) {
        tarjetas.push(encontradas[i]);
    }

    // Muestra solo las canciones que coinciden con lo escrito y con el género elegido.
    function filtrar() {
        const busqueda = normalizar(campo.value.trim());
        const generoElegido = genero.value;
        let cantidad = 0;

        for (let i = 0; i < tarjetas.length; i++) {
            const tarjeta = tarjetas[i];
            const coincideTexto = normalizar(tarjeta.dataset.busqueda).indexOf(busqueda) != -1;
            const coincideGenero = generoElegido == "0" || tarjeta.dataset.genero == generoElegido;
            const visible = coincideTexto && coincideGenero;
            tarjeta.hidden = !visible;
            if (visible) {
                cantidad++;
            }
        }

        // En la tabla se muestran las mismas canciones que en las tarjetas.
        for (let i = 0; i < filas.length; i++) {
            const tarjeta = cuadricula.querySelector('.track-card[data-id="' + filas[i].dataset.id + '"]');
            filas[i].hidden = tarjeta.hidden;
        }

        if (cantidad == 1) {
            resumen.textContent = "1 canción encontrada";
        } else {
            resumen.textContent = cantidad + " canciones encontradas";
        }
        mensaje.hidden = cantidad != 0;
    }

    // Ordena las tarjetas (y las filas de la tabla) por título, puntuación o año.
    function ordenar() {
        const criterio = orden.value;
        tarjetas.sort(function (a, b) {
            if (criterio == "puntuacion") {
                return Number(b.dataset.puntuacion) - Number(a.dataset.puntuacion);
            }
            if (criterio == "anio") {
                return Number(b.dataset.anio) - Number(a.dataset.anio);
            }
            return sinSignos(a.dataset.titulo).localeCompare(sinSignos(b.dataset.titulo), "es");
        });

        // appendChild mueve cada elemento al final, así quedan en el nuevo orden.
        for (let i = 0; i < tarjetas.length; i++) {
            cuadricula.appendChild(tarjetas[i]);
            const fila = cuerpoTabla.querySelector('tr[data-id="' + tarjetas[i].dataset.id + '"]');
            cuerpoTabla.appendChild(fila);
        }
    }

    campo.addEventListener("input", filtrar);
    genero.addEventListener("change", filtrar);
    orden.addEventListener("change", ordenar);
    filtrar();
}

// ==================== ANIMACIÓN DE ENTRADA ====================
// Las tarjetas aparecen suavemente (la animación está en estilos.css, clase js-revelar).

function activarAnimaciones() {
    const elementos = document.querySelectorAll(".track-card, .genre-card, .detail-section");
    for (let i = 0; i < elementos.length; i++) {
        elementos[i].classList.add("js-revelar");
    }
}

// ==================== MOSTRAR U OCULTAR LA CONTRASEÑA ====================

function activarMostrarContrasena() {
    const botones = document.querySelectorAll(".toggle-password");
    for (let i = 0; i < botones.length; i++) {
        botones[i].addEventListener("click", function () {
            const boton = this;
            const campo = document.getElementById(boton.dataset.target);
            if (campo.type == "password") {
                campo.type = "text";
                boton.textContent = "Ocultar";
            } else {
                campo.type = "password";
                boton.textContent = "Mostrar";
            }
            boton.setAttribute("aria-label", boton.textContent + " contraseña");
        });
    }
}

// ==================== TAREA: EFECTOS CON JAVASCRIPT ====================

// 1. Saludo según la hora del día (página de inicio).
function mostrarSaludo() {
    const portada = document.querySelector(".hero-copy");
    if (!portada) {
        return;
    }
    const hora = new Date().getHours();
    let saludo = "Buenas noches";
    if (hora >= 5 && hora < 12) {
        saludo = "Buenos días";
    } else if (hora >= 12 && hora < 19) {
        saludo = "Buenas tardes";
    }
    const mensaje = document.createElement("p");
    mensaje.className = "saludo-hora";
    mensaje.textContent = saludo + ", ¿qué quieres escuchar hoy?";
    portada.prepend(mensaje);
}

// 2 y 3. Barra de progreso arriba y botón "Arriba" (aparece después de bajar 400 px).
function activarBarraYBotonArriba() {
    const barra = document.createElement("div");
    barra.className = "barra-progreso";
    document.body.appendChild(barra);

    const botonArriba = document.createElement("button");
    botonArriba.type = "button";
    botonArriba.className = "boton-arriba";
    botonArriba.textContent = "↑ Arriba";
    document.body.appendChild(botonArriba);

    function alDesplazar() {
        const recorrido = document.documentElement.scrollHeight - window.innerHeight;
        let porcentaje = 0;
        if (recorrido > 0) {
            porcentaje = (window.scrollY / recorrido) * 100;
        }
        barra.style.width = porcentaje + "%";

        if (window.scrollY > 400) {
            botonArriba.classList.add("visible");
        } else {
            botonArriba.classList.remove("visible");
        }
    }
    window.addEventListener("scroll", alDesplazar);
    alDesplazar();

    botonArriba.addEventListener("click", function () {
        window.scrollTo(0, 0);
    });
}

// 5. Registro: nivel de la contraseña y aviso si las dos contraseñas coinciden.
function activarValidacionRegistro() {
    const clave = document.getElementById("clave");
    const confirmacion = document.getElementById("confirmacion");
    if (!clave || !confirmacion) {
        return;
    }

    const nivel = document.createElement("p");
    nivel.className = "ayuda-clave";
    clave.parentElement.after(nivel);

    const coincidencia = document.createElement("p");
    coincidencia.className = "ayuda-clave";
    confirmacion.after(coincidencia);

    function revisarClave() {
        const texto = clave.value;

        // Se revisa letra por letra qué tipos de caracteres tiene.
        let minuscula = false;
        let mayuscula = false;
        let numero = false;
        let simbolo = false;
        for (let i = 0; i < texto.length; i++) {
            const letra = texto[i];
            if (letra >= "a" && letra <= "z") {
                minuscula = true;
            } else if (letra >= "A" && letra <= "Z") {
                mayuscula = true;
            } else if (letra >= "0" && letra <= "9") {
                numero = true;
            } else {
                simbolo = true;
            }
        }

        // Un punto por cada cosa que cumple.
        let puntos = 0;
        if (minuscula && mayuscula) {
            puntos++;
        }
        if (numero) {
            puntos++;
        }
        if (simbolo) {
            puntos++;
        }

        if (texto == "") {
            nivel.textContent = "";
            nivel.className = "ayuda-clave";
        } else if (texto.length < 8) {
            nivel.textContent = "Muy corta: usa al menos 8 caracteres";
            nivel.className = "ayuda-clave error";
        } else if (puntos <= 1) {
            nivel.textContent = "Contraseña débil: mezcla mayúsculas, números o símbolos";
            nivel.className = "ayuda-clave error";
        } else if (puntos == 2) {
            nivel.textContent = "Contraseña media";
            nivel.className = "ayuda-clave media";
        } else {
            nivel.textContent = "Contraseña fuerte";
            nivel.className = "ayuda-clave ok";
        }

        // setCustomValidity impide enviar el formulario si las contraseñas no coinciden.
        if (confirmacion.value == "") {
            coincidencia.textContent = "";
            confirmacion.setCustomValidity("");
        } else if (confirmacion.value == texto) {
            coincidencia.textContent = "Las contraseñas coinciden";
            coincidencia.className = "ayuda-clave ok";
            confirmacion.setCustomValidity("");
        } else {
            coincidencia.textContent = "Las contraseñas no coinciden";
            coincidencia.className = "ayuda-clave error";
            confirmacion.setCustomValidity("Las contraseñas no coinciden");
        }
    }
    clave.addEventListener("input", revisarClave);
    confirmacion.addEventListener("input", revisarClave);
}

// 6. Ficha de la canción: botón para copiar el enlace de la página.
function activarCopiarEnlace() {
    const acciones = document.querySelector(".detail-copy .hero-actions");
    if (!acciones) {
        return;
    }
    const botonCopiar = document.createElement("button");
    botonCopiar.type = "button";
    botonCopiar.className = "button button-secondary btn btn-outline-light";
    botonCopiar.textContent = "Copiar enlace";
    acciones.appendChild(botonCopiar);

    botonCopiar.addEventListener("click", function () {
        navigator.clipboard.writeText(window.location.href);
        botonCopiar.textContent = "¡Enlace copiado!";
        // A los 2 segundos vuelve al texto normal.
        setTimeout(function () {
            botonCopiar.textContent = "Copiar enlace";
        }, 2000);
    });
}

// Si una portada no carga (por ejemplo, sin internet), se muestra la ilustración del género.
function activarPortadasDeRespaldo() {
    const imagenes = document.querySelectorAll("img[data-respaldo]");
    for (let i = 0; i < imagenes.length; i++) {
        imagenes[i].addEventListener("error", function () {
            // El if evita repetirlo sin fin si la ilustración tampoco carga.
            if (this.src.indexOf(this.dataset.respaldo) == -1) {
                this.src = this.dataset.respaldo;
            }
        });
    }
}

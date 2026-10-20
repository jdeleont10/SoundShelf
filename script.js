// SoundShelf: efectos y comportamiento dinámico de la página.
// Todo está dentro de la función activarEfectos().
// - En las páginas PHP (login, registro, mis favoritos) se llama cuando carga la página.
// - En las páginas .html se llama desde js/app.js, cuando ya llegaron los datos de la base de datos.

let efectosActivados = false;

function activarEfectos() {
    // Para que no se active dos veces.
    if (efectosActivados) {
        return;
    }
    efectosActivados = true;

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
    // Las páginas .html avisan con SOUNDSHELF_ASINCRONO que el contenido llega después (app.js).
    if (!window.SOUNDSHELF_ASINCRONO) {
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
        aviso.setAttribute("aria-live", "polite");
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
        contador.className = "contador-favoritos";
        contador.href = "mis-favoritos.php";
    } else {
        contador = document.createElement("span");
        contador.className = "contador-favoritos";
    }
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
    // Pequeño "latido" cuando cambia.
    if (animar) {
        contador.classList.remove("latido");
        void contador.offsetWidth; // truco para que la animación se repita
        contador.classList.add("latido");
    }
}

// ==================== FAVORITOS ====================
// Con sesión se guardan en MySQL (favoritos.php). Sin sesión, en el navegador (localStorage).

const CLAVE_FAVORITOS = "soundshelf-favoritos";
let favoritos = [];

// Lee los favoritos guardados en el navegador.
function leerFavoritosDelNavegador() {
    try {
        const datos = JSON.parse(localStorage.getItem(CLAVE_FAVORITOS) || "[]");
        if (!Array.isArray(datos)) {
            return [];
        }
        const lista = [];
        for (let i = 0; i < datos.length; i++) {
            lista.push(String(datos[i]));
        }
        return lista;
    } catch (error) {
        return [];
    }
}

// Pinta los botones ♥ según la lista de favoritos.
function actualizarBotonesFavoritos(animar) {
    const botones = document.querySelectorAll("[data-favorito]");
    for (let i = 0; i < botones.length; i++) {
        const boton = botones[i];
        const activo = favoritos.indexOf(boton.dataset.favorito) != -1;
        boton.setAttribute("aria-pressed", String(activo));
        if (activo) {
            boton.textContent = "♥ Favorita";
            boton.classList.add("es-favorita");
        } else {
            boton.textContent = "♡ Favoritos";
            boton.classList.remove("es-favorita");
        }
    }
    actualizarContador(favoritos.length, animar);
}

// Manda datos a favoritos.php y devuelve la respuesta en JSON.
function enviarFavoritos(datos) {
    const formulario = new URLSearchParams();
    formulario.append("csrf", document.querySelector('meta[name="csrf-token"]').content);
    for (const nombre in datos) {
        formulario.append(nombre, datos[nombre]);
    }
    return fetch("favoritos.php", { method: "POST", body: formulario })
        .then(function (respuesta) {
            return respuesta.json();
        });
}

// Convierte la lista que manda el servidor (números) en textos, para compararla con los botones.
function guardarListaDelServidor(lista) {
    favoritos = [];
    for (let i = 0; i < lista.length; i++) {
        favoritos.push(String(lista[i]));
    }
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

function activarFavoritos() {
    const conSesion = document.body.dataset.sesion == "1";

    // Lista inicial: con sesión viene en la página (data-favoritos="1,4,7"); sin sesión, del navegador.
    if (conSesion) {
        favoritos = [];
        const texto = document.body.dataset.favoritos || "";
        if (texto != "") {
            favoritos = texto.split(",");
        }
    } else {
        favoritos = leerFavoritosDelNavegador();
    }
    actualizarBotonesFavoritos(false);

    // Al hacer clic en cualquier botón ♥.
    document.addEventListener("click", function (evento) {
        const boton = evento.target.closest("[data-favorito]");
        if (!boton) {
            return;
        }
        const id = boton.dataset.favorito;
        const yaEstaba = favoritos.indexOf(id) != -1;

        if (conSesion) {
            // Con sesión: se guarda en la base de datos.
            boton.disabled = true;
            let accion = "agregar";
            if (yaEstaba) {
                accion = "quitar";
            }
            enviarFavoritos({ accion: accion, cancion: id })
                .then(function (datos) {
                    boton.disabled = false;
                    if (datos.error) {
                        notificar(datos.error);
                        return;
                    }
                    guardarListaDelServidor(datos.favoritos);
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
            return;
        }

        // Sin sesión: se guarda en el navegador.
        if (yaEstaba) {
            favoritos.splice(favoritos.indexOf(id), 1);
        } else {
            favoritos.push(id);
        }
        try {
            localStorage.setItem(CLAVE_FAVORITOS, JSON.stringify(favoritos));
        } catch (error) {
            notificar("El navegador no permite guardar favoritos.");
            return;
        }
        actualizarBotonesFavoritos(true);
        if (yaEstaba) {
            notificar("Canción eliminada de favoritos");
        } else {
            notificar("Guardada en este navegador. Inicia sesión para guardarla en tu cuenta.");
        }
    });

    // Al iniciar sesión, los favoritos que estaban en el navegador pasan a la cuenta.
    if (conSesion) {
        const delNavegador = leerFavoritosDelNavegador();
        if (delNavegador.length > 0) {
            enviarFavoritos({ accion: "sincronizar", canciones: delNavegador.join(",") })
                .then(function (datos) {
                    if (datos.error) {
                        return;
                    }
                    guardarListaDelServidor(datos.favoritos);
                    try {
                        localStorage.removeItem(CLAVE_FAVORITOS);
                    } catch (error) {
                        // si no se puede borrar, no pasa nada
                    }
                    actualizarBotonesFavoritos(true);
                    if (delNavegador.length == 1) {
                        notificar("Pasamos 1 favorito a tu cuenta");
                    } else {
                        notificar("Pasamos " + delNavegador.length + " favoritos a tu cuenta");
                    }
                })
                .catch(function () {
                    // si falla, los favoritos se quedan en el navegador
                });
        }
    }
}

// ==================== FILTRO EN VIVO Y ORDEN (catálogo) ====================

// Quita las tildes y pasa a minúsculas, para que "cancion" encuentre "Canción".
function normalizar(texto) {
    return String(texto).normalize("NFD").replace(/[̀-ͯ]/g, "").toLowerCase();
}

// Quita los signos ¿ y ¡ del inicio, para ordenar "¿Sabes?" en la S.
function sinSignos(titulo) {
    return titulo.replace(/^[¿¡]+/, "");
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
    const tarjetas = Array.from(cuadricula.querySelectorAll(".track-card"));
    const filas = Array.from(document.querySelectorAll("#tabla-canciones tbody tr"));

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
            filas[i].hidden = !tarjeta || tarjeta.hidden;
        }

        if (resumen) {
            if (cantidad == 1) {
                resumen.textContent = "1 canción encontrada";
            } else {
                resumen.textContent = cantidad + " canciones encontradas";
            }
        }
        if (mensaje) {
            mensaje.hidden = cantidad != 0;
        }
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

        for (let i = 0; i < tarjetas.length; i++) {
            cuadricula.appendChild(tarjetas[i]);
            if (cuerpoTabla) {
                const fila = cuerpoTabla.querySelector('tr[data-id="' + tarjetas[i].dataset.id + '"]');
                if (fila) {
                    cuerpoTabla.appendChild(fila);
                }
            }
        }
    }

    campo.addEventListener("input", filtrar);
    genero.addEventListener("change", filtrar);
    if (orden) {
        orden.addEventListener("change", ordenar);
    }
    filtrar();
}

// ==================== ANIMACIÓN AL BAJAR ====================
// Las tarjetas aparecen suavemente cuando entran en la pantalla.

function activarAnimaciones() {
    const menosMovimiento = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    if (!("IntersectionObserver" in window) || menosMovimiento) {
        return;
    }
    const observador = new IntersectionObserver(function (entradas) {
        for (let i = 0; i < entradas.length; i++) {
            if (entradas[i].isIntersecting) {
                entradas[i].target.classList.add("js-revelado");
                observador.unobserve(entradas[i].target);
            }
        }
    }, { threshold: 0.08 });

    const elementos = document.querySelectorAll(".track-card, .genre-card, .detail-section");
    for (let i = 0; i < elementos.length; i++) {
        elementos[i].classList.add("js-revelar");
        observador.observe(elementos[i]);
    }
}

// ==================== MOSTRAR U OCULTAR LA CONTRASEÑA ====================

function activarMostrarContrasena() {
    const botones = document.querySelectorAll(".toggle-password");
    for (let i = 0; i < botones.length; i++) {
        const boton = botones[i];
        boton.addEventListener("click", function () {
            const campo = document.getElementById(boton.dataset.target);
            if (!campo) {
                return;
            }
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
    botonArriba.setAttribute("aria-label", "Volver arriba");
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
        window.scrollTo({ top: 0, behavior: "smooth" });
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
    clave.closest(".password-field").after(nivel);

    const coincidencia = document.createElement("p");
    coincidencia.className = "ayuda-clave";
    confirmacion.after(coincidencia);

    function revisarClave() {
        const texto = clave.value;

        // Un punto por cada tipo de carácter que tenga.
        let puntos = 0;
        if (/[a-z]/.test(texto) && /[A-Z]/.test(texto)) {
            puntos++;
        }
        if (/[0-9]/.test(texto)) {
            puntos++;
        }
        if (/[^A-Za-z0-9]/.test(texto)) {
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
    if (!acciones || !navigator.clipboard) {
        return;
    }
    const botonCopiar = document.createElement("button");
    botonCopiar.type = "button";
    botonCopiar.className = "button button-secondary btn btn-outline-light";
    botonCopiar.textContent = "Copiar enlace";
    acciones.appendChild(botonCopiar);

    botonCopiar.addEventListener("click", function () {
        navigator.clipboard.writeText(window.location.href)
            .then(function () {
                botonCopiar.textContent = "¡Enlace copiado!";
            })
            .catch(function () {
                botonCopiar.textContent = "No se pudo copiar";
            });
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
        const imagen = imagenes[i];
        imagen.addEventListener("error", function () {
            usarRespaldo(imagen);
        });
        // Por si ya falló antes de que cargara este código.
        if (imagen.complete && imagen.naturalWidth == 0) {
            usarRespaldo(imagen);
        }
    }
}

function usarRespaldo(imagen) {
    if (!imagen.src.endsWith(imagen.dataset.respaldo)) {
        imagen.src = imagen.dataset.respaldo;
    }
}

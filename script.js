// SoundShelf: efectos con JavaScript.
// Las funciones se llaman desde el HTML con onclick, oninput, etc.

// 1. Saludo según la hora del día (solo en la página de inicio).
function mostrarSaludo() {
    let saludo = document.getElementById("saludo");
    if (saludo == null) {
        return;
    }

    let hora = new Date().getHours();
    let texto = "Buenas noches";
    if (hora >= 5 && hora < 12) {
        texto = "Buenos días";
    } else if (hora >= 12 && hora < 19) {
        texto = "Buenas tardes";
    }
    saludo.innerHTML = texto + ", ¿qué quieres escuchar hoy?";
}

// 2. Barra de progreso arriba y 3. botón "Arriba".
// Esta función se ejecuta cada vez que se mueve la página.
window.onscroll = function () {
    let barra = document.getElementById("barra-progreso");
    let boton = document.getElementById("boton-arriba");

    // Qué porcentaje de la página ya se bajó.
    let total = document.documentElement.scrollHeight - window.innerHeight;
    let porcentaje = (window.scrollY / total) * 100;
    barra.style.width = porcentaje + "%";

    // El botón aparece después de bajar 400 píxeles.
    if (window.scrollY > 400) {
        boton.style.display = "block";
    } else {
        boton.style.display = "none";
    }
};

function irArriba() {
    window.scrollTo(0, 0);
}

// 4. Mostrar u ocultar la contraseña (login y registro).
function mostrarClave() {
    let campo = document.getElementById("clave");
    let boton = document.getElementById("boton-clave");

    if (campo.type == "password") {
        campo.type = "text";
        boton.innerHTML = "Ocultar";
    } else {
        campo.type = "password";
        boton.innerHTML = "Mostrar";
    }
}

// 5. Registro: qué tan segura es la contraseña y si las dos coinciden.
function revisarClave() {
    let clave = document.getElementById("clave").value;
    let confirmacion = document.getElementById("confirmacion").value;
    let nivel = document.getElementById("nivel-clave");
    let coincidencia = document.getElementById("coincidencia-clave");

    // Se revisa letra por letra qué tipos de caracteres tiene.
    let minuscula = false;
    let mayuscula = false;
    let numero = false;
    let simbolo = false;
    for (let i = 0; i < clave.length; i++) {
        let letra = clave[i];
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
        puntos = puntos + 1;
    }
    if (numero) {
        puntos = puntos + 1;
    }
    if (simbolo) {
        puntos = puntos + 1;
    }

    if (clave == "") {
        nivel.innerHTML = "";
    } else if (clave.length < 8) {
        nivel.innerHTML = "Muy corta: usa al menos 8 caracteres";
        nivel.className = "ayuda-clave mb-3 rojo";
    } else if (puntos <= 1) {
        nivel.innerHTML = "Contraseña débil: mezcla mayúsculas, números o símbolos";
        nivel.className = "ayuda-clave mb-3 rojo";
    } else if (puntos == 2) {
        nivel.innerHTML = "Contraseña media";
        nivel.className = "ayuda-clave mb-3 amarillo";
    } else {
        nivel.innerHTML = "Contraseña fuerte";
        nivel.className = "ayuda-clave mb-3 verde";
    }

    if (confirmacion == "") {
        coincidencia.innerHTML = "";
    } else if (confirmacion == clave) {
        coincidencia.innerHTML = "Las contraseñas coinciden";
        coincidencia.className = "ayuda-clave mb-3 verde";
    } else {
        coincidencia.innerHTML = "Las contraseñas no coinciden";
        coincidencia.className = "ayuda-clave mb-3 rojo";
    }
}

// 6. Ficha de la canción: copiar el enlace de la página.
function copiarEnlace() {
    navigator.clipboard.writeText(window.location.href);

    let boton = document.getElementById("boton-copiar");
    boton.innerHTML = "¡Enlace copiado!";

    // A los 2 segundos vuelve al texto normal.
    setTimeout(function () {
        boton.innerHTML = "Copiar enlace";
    }, 2000);
}

// 7. Reproductor de abajo: al tocar "Escuchar" se carga
// el reproductor de Spotify con esa canción.
function reproducir(spotifyId) {
    let reproductor = document.getElementById("reproductor");
    let iframe = document.getElementById("reproductor-spotify");

    iframe.src = "https://open.spotify.com/embed/track/" + spotifyId + "?theme=0";
    reproductor.style.display = "flex";
}

function cerrarReproductor() {
    let reproductor = document.getElementById("reproductor");
    let iframe = document.getElementById("reproductor-spotify");

    iframe.src = "";
    reproductor.style.display = "none";
}

// Al cargar la página se muestra el saludo.
mostrarSaludo();

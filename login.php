<?php
// Inicio de sesión. Después de 5 intentos fallidos en 15 minutos, ese correo queda bloqueado un rato.
require_once __DIR__ . "/includes/iniciar.php";

// Si ya hay sesión, no hace falta el formulario.
if (usuarioActual() != null) {
    header("Location: " . paginaDeRegreso());
    exit;
}

$error = "";
$correo = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = strtolower(texto($_POST, "correo"));
    // La contraseña se lee tal cual (sin quitar espacios).
    $clave = "";
    if (isset($_POST["clave"]) && is_string($_POST["clave"])) {
        $clave = $_POST["clave"];
    }

    if (!verificarCsrf()) {
        $error = "Solicitud inválida. Recarga la página.";
    } else if (!filter_var($correo, FILTER_VALIDATE_EMAIL) || strlen($correo) > 190) {
        $error = "Ingresa un correo válido.";
    } else {
        // ¿Cuántos intentos fallidos tuvo este correo en los últimos 15 minutos?
        $intentos = buscarUna("SELECT COUNT(*) AS total, TIMESTAMPDIFF(SECOND, MIN(creado_en), NOW()) AS segundos
                               FROM intentos_login
                               WHERE correo = ? AND creado_en > NOW() - INTERVAL 15 MINUTE", array($correo));

        if ($intentos["total"] >= 5) {
            // Bloqueado: se cuenta desde el primer intento fallido y ni siquiera se revisa la contraseña.
            $minutos = ceil((15 * 60 - $intentos["segundos"]) / 60);
            if ($minutos <= 1) {
                $error = "Demasiados intentos fallidos. Espera 1 minuto y vuelve a intentarlo.";
            } else {
                $error = "Demasiados intentos fallidos. Espera " . $minutos . " minutos y vuelve a intentarlo.";
            }
        } else {
            $usuario = buscarUna("SELECT id, nombre, correo, clave_hash FROM usuarios WHERE correo = ?", array($correo));

            // password_verify compara la contraseña escrita con la cifrada.
            if ($usuario != null && password_verify($clave, $usuario["clave_hash"])) {
                ejecutar("DELETE FROM intentos_login WHERE correo = ?", array($correo));
                iniciarSesionUsuario($usuario);
                header("Location: " . paginaDeRegreso());
                exit;
            }

            // Se anota el intento fallido y se borran los que tienen más de un día.
            ejecutar("INSERT INTO intentos_login (correo, ip) VALUES (?, ?)", array($correo, $_SERVER["REMOTE_ADDR"]));
            ejecutar("DELETE FROM intentos_login WHERE creado_en < NOW() - INTERVAL 1 DAY");
            // El mensaje no dice si falló el correo o la contraseña.
            $error = "Correo o contraseña incorrectos.";
        }
    }
}

// Aviso que llega en la dirección (?aviso=...) o porque la sesión venció.
$aviso = "";
if (texto($_GET, "aviso") == "favoritos") {
    $aviso = "Inicia sesión para ver tus favoritos.";
}
if (texto($_GET, "aviso") == "canciones") {
    $aviso = "Inicia sesión para ver y escuchar las canciones.";
}
if (isset($_SESSION["sesion_expirada"])) {
    $aviso = "Tu sesión se cerró después de 30 minutos sin actividad. Vuelve a iniciar sesión.";
    unset($_SESSION["sesion_expirada"]);
}

$tituloPagina = "Iniciar sesión";
$pagina = "login";
require __DIR__ . "/includes/encabezado.php";
?>
<main id="contenido" class="container py-5">
    <section class="auth-panel shadow-lg mx-auto" aria-labelledby="titulo-auth">
        <p class="eyebrow">BIENVENIDO DE NUEVO</p>
        <h1 id="titulo-auth">Inicia sesión<span class="accent">.</span></h1>
        <p class="muted">Accede con tu correo y contraseña.</p>

        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert"><p><?= e($error) ?></p></div>
        <?php elseif ($aviso): ?>
            <div class="alert alert-info" role="status"><p><?= e($aviso) ?></p></div>
        <?php endif; ?>

        <form method="post" class="auth-form needs-validation">
            <input type="hidden" name="csrf" value="<?= e(tokenCsrf()) ?>">

            <label for="correo">Correo electrónico</label>
            <input class="form-control form-control-lg" id="correo" type="email" name="correo" maxlength="190" required autocomplete="email" value="<?= e($correo) ?>">

            <label for="clave">Contraseña</label>
            <div class="password-field input-group">
                <input class="form-control form-control-lg" id="clave" type="password" name="clave" required autocomplete="current-password">
                <button type="button" class="toggle-password btn btn-outline-light" data-target="clave" aria-label="Mostrar contraseña">Mostrar</button>
            </div>

            <button class="btn btn-accent btn-lg w-100 mt-3" type="submit">Entrar ↗</button>
        </form>

        <p class="auth-switch text-center mt-4">¿No tienes una cuenta? <a href="registro.php<?= e(parametroRegreso()) ?>">Regístrate</a></p>
    </section>
</main>
<?php require __DIR__ . '/includes/pie.php'; ?>

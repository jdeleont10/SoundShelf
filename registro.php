<?php
// Registro: revisa los datos, guarda el usuario con la contraseña cifrada y abre la sesión.
require_once __DIR__ . "/includes/iniciar.php";

// Si ya hay sesión, no hace falta el formulario.
if (usuarioActual() != null) {
    header("Location: " . paginaDeRegreso());
    exit;
}

$errores = array();
$nombre = "";
$correo = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = texto($_POST, "nombre");
    $correo = strtolower(texto($_POST, "correo"));
    // Las contraseñas se leen tal cual (sin quitar espacios).
    $clave = "";
    $confirmacion = "";
    if (isset($_POST["clave"]) && is_string($_POST["clave"])) {
        $clave = $_POST["clave"];
    }
    if (isset($_POST["confirmacion"]) && is_string($_POST["confirmacion"])) {
        $confirmacion = $_POST["confirmacion"];
    }

    // Validaciones (JavaScript también las revisa, pero el servidor siempre las vuelve a comprobar).
    if (!verificarCsrf()) {
        $errores[] = "Solicitud inválida. Recarga la página.";
    }
    if (mb_strlen($nombre) < 2 || mb_strlen($nombre) > 80) {
        $errores[] = "El nombre debe tener entre 2 y 80 caracteres.";
    }
    if (strlen($correo) > 190 || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "Ingresa un correo válido.";
    }
    if (strlen($clave) < 8 || strlen($clave) > 72) {
        $errores[] = "La contraseña debe tener entre 8 y 72 caracteres.";
    }
    if ($clave != $confirmacion) {
        $errores[] = "Las contraseñas no coinciden.";
    }
    if (count($errores) == 0 && buscarUna("SELECT id FROM usuarios WHERE correo = ?", array($correo)) != null) {
        $errores[] = "Ese correo ya está registrado.";
    }

    // Si todo está bien: se guarda con la contraseña cifrada y se abre la sesión.
    if (count($errores) == 0) {
        ejecutar("INSERT INTO usuarios (nombre, correo, clave_hash) VALUES (?, ?, ?)", array($nombre, $correo, password_hash($clave, PASSWORD_DEFAULT)));
        iniciarSesionUsuario(array("id" => mysqli_insert_id($conexion), "nombre" => $nombre, "correo" => $correo));
        header("Location: " . paginaDeRegreso());
        exit;
    }
}

$tituloPagina = "Crear cuenta";
$pagina = "registro";
require __DIR__ . "/includes/encabezado.php";
?>
<main id="contenido" class="container py-5">
    <section class="auth-panel shadow-lg mx-auto" aria-labelledby="titulo-auth">
        <p class="eyebrow">ÚNETE A SOUNDSHELF</p>
        <h1 id="titulo-auth">Crea tu cuenta<span class="accent">.</span></h1>
        <p class="muted">Regístrate para tener una sesión personal en SoundShelf.</p>

        <?php if ($errores): ?>
            <div class="alert alert-danger" role="alert">
                <?php foreach ($errores as $mensaje): ?>
                    <p><?= e($mensaje) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" class="auth-form needs-validation">
            <input type="hidden" name="csrf" value="<?= e(tokenCsrf()) ?>">

            <label for="nombre">Nombre</label>
            <input class="form-control form-control-lg" id="nombre" name="nombre" maxlength="80" required autocomplete="name" value="<?= e($nombre) ?>">

            <label for="correo">Correo electrónico</label>
            <input class="form-control form-control-lg" id="correo" type="email" name="correo" maxlength="190" required autocomplete="email" value="<?= e($correo) ?>">

            <label for="clave">Contraseña</label>
            <div class="password-field input-group">
                <input class="form-control form-control-lg" id="clave" type="password" name="clave" minlength="8" maxlength="72" required autocomplete="new-password">
                <button type="button" class="toggle-password btn btn-outline-light" data-target="clave" aria-label="Mostrar contraseña">Mostrar</button>
            </div>

            <label for="confirmacion">Confirmar contraseña</label>
            <input class="form-control form-control-lg" id="confirmacion" type="password" name="confirmacion" minlength="8" maxlength="72" required autocomplete="new-password">

            <button class="btn btn-accent btn-lg w-100 mt-3" type="submit">Crear cuenta ↗</button>
        </form>

        <p class="auth-switch text-center mt-4">¿Ya tienes cuenta? <a href="login.php<?= e(parametroRegreso()) ?>">Inicia sesión</a></p>
    </section>
</main>
<?php require __DIR__ . '/includes/pie.php'; ?>

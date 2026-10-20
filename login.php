<?php
// Inicio de sesión: busca el correo en la base de datos y compara la contraseña.
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
    $clave = $_POST["clave"];

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = "Ingresa un correo válido.";
    } else {
        $usuario = buscarUna("SELECT id, nombre, correo, clave_hash FROM usuarios WHERE correo = ?", array($correo));

        // password_verify compara la contraseña escrita con la cifrada.
        if ($usuario != null && password_verify($clave, $usuario["clave_hash"])) {
            iniciarSesionUsuario($usuario);
            header("Location: " . paginaDeRegreso());
            exit;
        }

        // El mensaje no dice si falló el correo o la contraseña.
        $error = "Correo o contraseña incorrectos.";
    }
}

// Aviso que llega en la dirección (?aviso=...).
$aviso = "";
if (texto($_GET, "aviso") == "favoritos") {
    $aviso = "Inicia sesión para ver tus favoritos.";
}
if (texto($_GET, "aviso") == "canciones") {
    $aviso = "Inicia sesión para ver y escuchar las canciones.";
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

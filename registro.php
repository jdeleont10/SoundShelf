<?php
// Crear una cuenta nueva.
include "includes/sesion.php";
include "includes/conexion.php";

// Si ya inició sesión, no necesita este formulario.
if (isset($_SESSION["usuario_id"])) {
    header("Location: " . paginaDeRegreso());
    exit;
}

$errores = array();
$nombre = "";
$correo = "";

// Cuando se envía el formulario.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $clave = "";
    $confirmacion = "";
    if (isset($_POST["nombre"]) && is_string($_POST["nombre"])) {
        $nombre = trim($_POST["nombre"]);
    }
    if (isset($_POST["correo"]) && is_string($_POST["correo"])) {
        $correo = strtolower(trim($_POST["correo"]));
    }
    if (isset($_POST["clave"]) && is_string($_POST["clave"])) {
        $clave = $_POST["clave"];
    }
    if (isset($_POST["confirmacion"]) && is_string($_POST["confirmacion"])) {
        $confirmacion = $_POST["confirmacion"];
    }

    // Revisar los datos (el navegador también los revisa, pero aquí se vuelve a comprobar).
    if (!tokenValido()) {
        $errores[] = "Solicitud inválida. Recarga la página.";
    }
    if (mb_strlen($nombre) < 2 || mb_strlen($nombre) > 80) {
        $errores[] = "El nombre debe tener entre 2 y 80 caracteres.";
    }
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL) || strlen($correo) > 190) {
        $errores[] = "Escribe un correo válido.";
    }
    if (strlen($clave) < 8 || strlen($clave) > 72) {
        $errores[] = "La contraseña debe tener entre 8 y 72 caracteres.";
    }
    if ($clave != $confirmacion) {
        $errores[] = "Las contraseñas no coinciden.";
    }

    // ¿Ya hay alguien con ese correo?
    $consulta = mysqli_prepare($conexion, "SELECT id FROM usuarios WHERE correo = ?");
    mysqli_stmt_bind_param($consulta, "s", $correo);
    mysqli_stmt_execute($consulta);
    $resultado = mysqli_stmt_get_result($consulta);
    if (mysqli_num_rows($resultado) > 0) {
        $errores[] = "Ese correo ya está registrado.";
    }

    // Si todo está bien, se guarda el usuario con la contraseña cifrada.
    if (count($errores) == 0) {
        $claveCifrada = password_hash($clave, PASSWORD_DEFAULT);
        $consulta = mysqli_prepare($conexion, "INSERT INTO usuarios (nombre, correo, clave_hash) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($consulta, "sss", $nombre, $correo, $claveCifrada);
        mysqli_stmt_execute($consulta);

        // Y se inicia la sesión con la cuenta nueva (con un id de sesión nuevo).
        session_regenerate_id(true);
        $_SESSION["usuario_id"] = mysqli_insert_id($conexion);
        $_SESSION["usuario_nombre"] = $nombre;
        header("Location: " . paginaDeRegreso());
        exit;
    }
}

// Para no perder la página a la que se quería entrar.
$volver = "?volver=" . urlencode(paginaDeRegreso());

$titulo = "Crear cuenta";
include "includes/encabezado.php";
?>

<main class="container">
    <div class="caja formulario">
        <p class="subtitulo-chico">ÚNETE A SOUNDSHELF</p>
        <h1>Crea tu cuenta<span class="lila">.</span></h1>
        <p class="texto-suave">Regístrate para tener una sesión personal en SoundShelf.</p>

        <?php if (count($errores) > 0) { ?>
            <div class="alert alert-danger">
                <?php
                foreach ($errores as $error) {
                    echo $error . "<br>";
                }
                ?>
            </div>
        <?php } ?>

        <form action="registro.php<?php echo $volver; ?>" method="post">
            <input type="hidden" name="token" value="<?php echo $_SESSION["token"]; ?>">

            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" id="nombre" name="nombre" class="form-control mb-3" maxlength="80" required value="<?php echo htmlspecialchars($nombre); ?>">

            <label for="correo" class="form-label">Correo electrónico</label>
            <input type="email" id="correo" name="correo" class="form-control mb-3" maxlength="190" required value="<?php echo htmlspecialchars($correo); ?>">

            <label for="clave" class="form-label">Contraseña</label>
            <div class="input-group">
                <input type="password" id="clave" name="clave" class="form-control" minlength="8" maxlength="72" required oninput="revisarClave()">
                <button type="button" id="boton-clave" class="btn btn-outline-light" onclick="mostrarClave()">Mostrar</button>
            </div>
            <p id="nivel-clave" class="ayuda-clave mb-3"></p>

            <label for="confirmacion" class="form-label">Confirmar contraseña</label>
            <input type="password" id="confirmacion" name="confirmacion" class="form-control" minlength="8" maxlength="72" required oninput="revisarClave()">
            <p id="coincidencia-clave" class="ayuda-clave mb-3"></p>

            <button type="submit" class="btn btn-lila w-100">Crear cuenta</button>
        </form>

        <p class="text-center mt-4 small">¿Ya tienes cuenta? <a class="lila" href="login.php<?php echo $volver; ?>">Inicia sesión</a></p>
    </div>
</main>

<?php include "includes/pie.php"; ?>

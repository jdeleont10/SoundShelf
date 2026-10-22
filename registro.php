<?php
// Crear una cuenta nueva.
session_start();
include "includes/conexion.php";

// Si ya inició sesión, no necesita este formulario.
if (isset($_SESSION["usuario_id"])) {
    header("Location: index.html");
    exit;
}

$errores = array();
$nombre = "";
$correo = "";

// Cuando se envía el formulario.
if (isset($_POST["correo"])) {
    $nombre = trim($_POST["nombre"]);
    $correo = trim($_POST["correo"]);
    $clave = $_POST["clave"];
    $confirmacion = $_POST["confirmacion"];

    // Revisar los datos.
    if (strlen($nombre) < 2) {
        $errores[] = "Escribe tu nombre.";
    }
    if (strlen($clave) < 8) {
        $errores[] = "La contraseña debe tener al menos 8 caracteres.";
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

        // Y se inicia la sesión con la cuenta nueva.
        $_SESSION["usuario_id"] = mysqli_insert_id($conexion);
        $_SESSION["usuario_nombre"] = $nombre;
        header("Location: index.html");
        exit;
    }
}

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

        <form action="registro.php" method="post">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" id="nombre" name="nombre" class="form-control mb-3" required value="<?php echo htmlspecialchars($nombre); ?>">

            <label for="correo" class="form-label">Correo electrónico</label>
            <input type="email" id="correo" name="correo" class="form-control mb-3" required value="<?php echo htmlspecialchars($correo); ?>">

            <label for="clave" class="form-label">Contraseña</label>
            <div class="input-group">
                <input type="password" id="clave" name="clave" class="form-control" minlength="8" required oninput="revisarClave()">
                <button type="button" id="boton-clave" class="btn btn-outline-light" onclick="mostrarClave()">Mostrar</button>
            </div>
            <p id="nivel-clave" class="ayuda-clave mb-3"></p>

            <label for="confirmacion" class="form-label">Confirmar contraseña</label>
            <input type="password" id="confirmacion" name="confirmacion" class="form-control" minlength="8" required oninput="revisarClave()">
            <p id="coincidencia-clave" class="ayuda-clave mb-3"></p>

            <button type="submit" class="btn btn-lila w-100">Crear cuenta</button>
        </form>

        <p class="text-center mt-4 small">¿Ya tienes cuenta? <a class="lila" href="login.php">Inicia sesión</a></p>
    </div>
</main>

<?php include "includes/pie.php"; ?>

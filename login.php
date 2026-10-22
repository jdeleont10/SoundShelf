<?php
// Iniciar sesión.
include "includes/sesion.php";
include "includes/conexion.php";

// Si ya inició sesión, no necesita este formulario.
if (isset($_SESSION["usuario_id"])) {
    header("Location: " . paginaDeRegreso());
    exit;
}

$error = "";
$correo = "";

// Cuando se envía el formulario.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $clave = "";
    if (isset($_POST["correo"]) && is_string($_POST["correo"])) {
        $correo = strtolower(trim($_POST["correo"]));
    }
    if (isset($_POST["clave"]) && is_string($_POST["clave"])) {
        $clave = $_POST["clave"];
    }

    // ¿Cuántas veces se equivocaron con este correo en los últimos 15 minutos?
    $consulta = mysqli_prepare($conexion, "SELECT COUNT(*) AS total FROM intentos_login WHERE correo = ? AND creado_en > NOW() - INTERVAL 15 MINUTE");
    mysqli_stmt_bind_param($consulta, "s", $correo);
    mysqli_stmt_execute($consulta);
    $resultado = mysqli_stmt_get_result($consulta);
    $intentos = mysqli_fetch_assoc($resultado);

    if (!tokenValido()) {
        $error = "Solicitud inválida. Recarga la página.";
    } else if ($intentos["total"] >= 5) {
        // Después de 5 intentos fallidos, ese correo queda bloqueado 15 minutos.
        $error = "Demasiados intentos fallidos. Espera 15 minutos y vuelve a intentarlo.";
    } else {
        // Buscar al usuario por su correo.
        $consulta = mysqli_prepare($conexion, "SELECT * FROM usuarios WHERE correo = ?");
        mysqli_stmt_bind_param($consulta, "s", $correo);
        mysqli_stmt_execute($consulta);
        $resultado = mysqli_stmt_get_result($consulta);
        $usuario = mysqli_fetch_assoc($resultado);

        // password_verify compara la contraseña escrita con la que está cifrada en la base de datos.
        if ($usuario != null && password_verify($clave, $usuario["clave_hash"])) {
            // Entró bien: se borran sus intentos fallidos.
            $consulta = mysqli_prepare($conexion, "DELETE FROM intentos_login WHERE correo = ?");
            mysqli_stmt_bind_param($consulta, "s", $correo);
            mysqli_stmt_execute($consulta);

            // Se cambia el id de la sesión para que nadie pueda usar uno viejo.
            session_regenerate_id(true);
            $_SESSION["usuario_id"] = $usuario["id"];
            $_SESSION["usuario_nombre"] = $usuario["nombre"];
            header("Location: " . paginaDeRegreso());
            exit;
        } else {
            // Se anota el intento fallido.
            $consulta = mysqli_prepare($conexion, "INSERT INTO intentos_login (correo, ip) VALUES (?, ?)");
            mysqli_stmt_bind_param($consulta, "ss", $correo, $_SERVER["REMOTE_ADDR"]);
            mysqli_stmt_execute($consulta);
            $error = "Correo o contraseña incorrectos.";
        }
    }
}

// Mensaje de aviso arriba del formulario.
$aviso = "";
if (isset($_GET["aviso"])) {
    $aviso = "Inicia sesión para ver y escuchar las canciones.";
}
if (isset($_SESSION["sesion_vencida"])) {
    $aviso = "Tu sesión se cerró después de 30 minutos sin actividad. Vuelve a iniciar sesión.";
    unset($_SESSION["sesion_vencida"]);
}

// Para no perder la página a la que se quería entrar.
$volver = "?volver=" . urlencode(paginaDeRegreso());

$titulo = "Iniciar sesión";
include "includes/encabezado.php";
?>

<main class="container">
    <div class="caja formulario">
        <p class="subtitulo-chico">BIENVENIDO DE NUEVO</p>
        <h1>Inicia sesión<span class="lila">.</span></h1>
        <p class="texto-suave">Accede con tu correo y contraseña.</p>

        <?php if ($error != "") { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } else if ($aviso != "") { ?>
            <div class="alert alert-info"><?php echo $aviso; ?></div>
        <?php } ?>

        <form action="login.php<?php echo $volver; ?>" method="post">
            <input type="hidden" name="token" value="<?php echo $_SESSION["token"]; ?>">

            <label for="correo" class="form-label">Correo electrónico</label>
            <input type="email" id="correo" name="correo" class="form-control mb-3" required value="<?php echo htmlspecialchars($correo); ?>">

            <label for="clave" class="form-label">Contraseña</label>
            <div class="input-group mb-3">
                <input type="password" id="clave" name="clave" class="form-control" required>
                <button type="button" id="boton-clave" class="btn btn-outline-light" onclick="mostrarClave()">Mostrar</button>
            </div>

            <button type="submit" class="btn btn-lila w-100">Entrar</button>
        </form>

        <p class="text-center mt-4 small">¿No tienes una cuenta? <a class="lila" href="registro.php<?php echo $volver; ?>">Regístrate</a></p>
    </div>
</main>

<?php include "includes/pie.php"; ?>

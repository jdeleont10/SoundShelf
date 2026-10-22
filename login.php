<?php
// Iniciar sesión.
session_start();
include "includes/conexion.php";

// Si ya inició sesión, no necesita este formulario.
if (isset($_SESSION["usuario_id"])) {
    header("Location: index.html");
    exit;
}

$error = "";
$correo = "";

// Cuando se envía el formulario.
if (isset($_POST["correo"])) {
    $correo = trim($_POST["correo"]);
    $clave = $_POST["clave"];

    // Buscar al usuario por su correo.
    $consulta = mysqli_prepare($conexion, "SELECT * FROM usuarios WHERE correo = ?");
    mysqli_stmt_bind_param($consulta, "s", $correo);
    mysqli_stmt_execute($consulta);
    $resultado = mysqli_stmt_get_result($consulta);
    $usuario = mysqli_fetch_assoc($resultado);

    // password_verify compara la contraseña escrita con la que está cifrada en la base de datos.
    if ($usuario != null && password_verify($clave, $usuario["clave_hash"])) {
        $_SESSION["usuario_id"] = $usuario["id"];
        $_SESSION["usuario_nombre"] = $usuario["nombre"];
        header("Location: index.html");
        exit;
    } else {
        $error = "Correo o contraseña incorrectos.";
    }
}

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
        <?php } else if (isset($_GET["aviso"])) { ?>
            <div class="alert alert-info">Inicia sesión para ver y escuchar las canciones.</div>
        <?php } ?>

        <form action="login.php" method="post">
            <label for="correo" class="form-label">Correo electrónico</label>
            <input type="email" id="correo" name="correo" class="form-control mb-3" required value="<?php echo htmlspecialchars($correo); ?>">

            <label for="clave" class="form-label">Contraseña</label>
            <div class="input-group mb-3">
                <input type="password" id="clave" name="clave" class="form-control" required>
                <button type="button" id="boton-clave" class="btn btn-outline-light" onclick="mostrarClave()">Mostrar</button>
            </div>

            <button type="submit" class="btn btn-lila w-100">Entrar</button>
        </form>

        <p class="text-center mt-4 small">¿No tienes una cuenta? <a class="lila" href="registro.php">Regístrate</a></p>
    </div>
</main>

<?php include "includes/pie.php"; ?>

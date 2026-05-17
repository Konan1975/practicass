<?php
include('conexion.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: registrar_usuario.html');
    exit;
}

$cedula = trim($_POST['cedula'] ?? '');
$nombre = trim($_POST['nombre'] ?? '');
$correo = trim($_POST['correo'] ?? '');
$password = $_POST['password'] ?? '';

$errors = [];
if ($cedula === '') $errors[] = 'Cédula requerida.';
if ($nombre === '') $errors[] = 'Nombre requerido.';
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) $errors[] = 'Correo inválido.';
if (strlen($password) < 8) $errors[] = 'La contraseña debe tener al menos 8 caracteres.';

if (!empty($errors)) {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error de registro</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="p-4">
        <div class="container">
            <div class="alert alert-danger">
                <h4 class="alert-heading">Errores en el formulario</h4>
                <ul>
                    <?php foreach ($errors as $e) { echo '<li>' . htmlspecialchars($e) . '</li>'; } ?>
                </ul>
                <a href="registrar_usuario.html" class="btn btn-primary">Volver</a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

$hashed = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conexion->prepare("INSERT INTO usuarios (cedula, nombre, correo, password) VALUES (?, ?, ?, ?)");
if (!$stmt) {
    die('Error en la preparación: ' . $conexion->error);
}

$stmt->bind_param('ssss', $cedula, $nombre, $correo, $hashed);
if ($stmt->execute()) {
    $stmt->close();
    $conexion->close();
    header('Location: listar_usuarios.php');
    exit;
} else {
    $err = $stmt->error;
    $stmt->close();
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error al guardar</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="p-4">
        <div class="container">
            <div class="alert alert-danger">
                <h4 class="alert-heading">No se pudo guardar el usuario</h4>
                <p><?php echo htmlspecialchars($err); ?></p>
                <a href="registrar_usuario.html" class="btn btn-primary">Volver</a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

?>

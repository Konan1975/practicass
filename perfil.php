<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

// Obtener datos del usuario
$stmt = $conexion->prepare("SELECT cedula, nombre, correo FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();

// Actualizar perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);

    // Verificar si el correo ya existe (excepto el propio)
    $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE correo = ? AND id != ?");
    $stmt->bind_param("si", $correo, $_SESSION['usuario_id']);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $error = "El correo ya está en uso.";
    } else {
        $stmt = $conexion->prepare("UPDATE usuarios SET nombre = ?, correo = ? WHERE id = ?");
        $stmt->bind_param("ssi", $nombre, $correo, $_SESSION['usuario_id']);
        if ($stmt->execute()) {
            $exito = "Perfil actualizado.";
            // Actualizar sesión
            $_SESSION['nombre'] = $nombre;
            // Recargar datos
            $usuario['nombre'] = $nombre;
            $usuario['correo'] = $correo;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil</title>
</head>
<body>
    <h2>Bienvenido, <?= htmlspecialchars($_SESSION['nombre']) ?>!</h2>
    <p><a href="cambiar_password.php">Cambiar contraseña</a> | <a href="logout.php">Cerrar sesión</a></p>

    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <?php if (!empty($exito)) echo "<p style='color:green;'>$exito</p>"; ?>

    <h3>Datos del perfil</h3>
    <form method="POST">
        Nombre: <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required><br><br>
        Correo: <input type="email" name="correo" value="<?= htmlspecialchars($usuario['correo']) ?>" required><br><br>
        <button type="submit">Actualizar</button>
    </form>
</body>
</html>
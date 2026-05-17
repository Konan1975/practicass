<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clave_actual = $_POST['clave_actual'];
    $nueva_clave = $_POST['nueva_clave'];
    $confirmar = $_POST['confirmar'];

    if ($nueva_clave !== $confirmar) {
        $error = "Las nuevas contraseñas no coinciden.";
    } else {
        // Obtener hash actual
        $stmt = $conexion->prepare("SELECT password FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['usuario_id']);
        $stmt->execute();
        $hash_actual = $stmt->get_result()->fetch_assoc()['password'];

        if (password_verify($clave_actual, $hash_actual)) {
            $nuevo_hash = password_hash($nueva_clave, PASSWORD_DEFAULT);
            $stmt = $conexion->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $nuevo_hash, $_SESSION['usuario_id']);
            if ($stmt->execute()) {
                $exito = "Contraseña actualizada correctamente.";
            }
        } else {
            $error = "La contraseña actual es incorrecta.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cambiar Contraseña</title>
</head>
<body>
    <h2>Cambiar Contraseña</h2>
    <p><a href="perfil.php">← Volver al perfil</a></p>

    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <?php if (!empty($exito)) echo "<p style='color:green;'>$exito</p>"; ?>

    <form method="POST">
        Contraseña actual: <input type="password" name="clave_actual" required><br><br>
        Nueva contraseña: <input type="password" name="nueva_clave" required><br><br>
        Confirmar nueva: <input type="password" name="confirmar" required><br><br>
        <button type="submit">Actualizar</button>
    </form>
</body>
</html>
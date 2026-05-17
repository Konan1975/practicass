<?php
session_start();
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'];
    $clave = $_POST['clave'];

    $stmt = $conexion->prepare("SELECT id, nombre, password FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($fila = $resultado->fetch_assoc()) {
        if (password_verify($clave, $fila['password'])) {
            $_SESSION['usuario_id'] = $fila['id'];
            $_SESSION['nombre'] = $fila['nombre'];
            header("Location: perfil.php");
            exit;
        }
    }
    $error = "Correo o contraseña incorrectos.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h2>Iniciar Sesión</h2>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
        Correo: <input type="email" name="correo" required><br><br>
        Contraseña: <input type="password" name="clave" required><br><br>
        <button type="submit">Entrar</button>
    </form>
    <p><a href="registro.php">Crear cuenta</a></p>
</body>
</html>
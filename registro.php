<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = trim($_POST['cedula']);
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $clave = $_POST['clave'];

    // Validar correo único
    $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = "El correo ya está registrado.";
    } else {
        $clave_hash = password_hash($clave, PASSWORD_DEFAULT);
        $stmt = $conexion->prepare("INSERT INTO usuarios (cedula, nombre, correo, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $cedula, $nombre, $correo, $clave_hash);
        if ($stmt->execute()) {
            $exito = "Usuario registrado. <a href='index.php'>Iniciar sesión</a>";
        } else {
            $error = "Error al registrar.";
        }
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
</head>
<body>
    <h2>Registro de Usuario</h2>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <?php if (!empty($exito)) echo "<p style='color:green;'>$exito</p>"; ?>
    
    <form method="POST">
        Cédula: <input type="text" name="cedula" required><br><br>
        Nombre: <input type="text" name="nombre" required><br><br>
        Correo: <input type="email" name="correo" required><br><br>
        Contraseña: <input type="password" name="clave" required><br><br>
        <button type="submit">Registrar</button>
    </form>
    <p><a href="index.php">¿Ya tienes cuenta? Inicia sesión</a></p>
</body>
</html>
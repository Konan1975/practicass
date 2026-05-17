<?php
include('conexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $cedula = trim($_POST['cedula'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($id <= 0) { header('Location: listar_usuarios.php'); exit; }

    if ($password !== '' && strlen($password) < 8) {
        $error = 'La contraseña debe tener al menos 8 caracteres.';
    } else {
        if ($password !== '') {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conexion->prepare("UPDATE usuarios SET cedula=?, nombre=?, correo=?, password=? WHERE id=?");
            $stmt->bind_param('ssssi', $cedula, $nombre, $correo, $hashed, $id);
        } else {
            $stmt = $conexion->prepare("UPDATE usuarios SET cedula=?, nombre=?, correo=? WHERE id=?");
            $stmt->bind_param('sssi', $cedula, $nombre, $correo, $id);
        }
        if ($stmt->execute()) {
            $stmt->close();
            $conexion->close();
            header('Location: listar_usuarios.php'); exit;
        } else {
            $error = 'Error al actualizar: ' . $stmt->error;
        }
    }
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: listar_usuarios.php'); exit; }

$stmt = $conexion->prepare('SELECT id, cedula, nombre, correo FROM usuarios WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) { $stmt->close(); $conexion->close(); header('Location: listar_usuarios.php'); exit; }
$user = $res->fetch_assoc();
$stmt->close();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Editar Usuario</h2>
        <?php if (!empty($error)) echo '<div class="alert alert-danger">'.htmlspecialchars($error).'</div>'; ?>
        <form method="post" action="editar.php">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">
            <div class="mb-3">
                <label>Cédula</label>
                <input name="cedula" class="form-control" value="<?php echo htmlspecialchars($user['cedula']); ?>" required>
            </div>
            <div class="mb-3">
                <label>Nombres</label>
                <input name="nombre" class="form-control" value="<?php echo htmlspecialchars($user['nombre']); ?>" required>
            </div>
            <div class="mb-3">
                <label>Correo</label>
                <input name="correo" type="email" class="form-control" value="<?php echo htmlspecialchars($user['correo']); ?>" required>
            </div>
            <div class="mb-3">
                <label>Contraseña (dejar en blanco para mantener)</label>
                <input name="password" type="password" class="form-control" minlength="8">
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-primary" type="submit">Guardar</button>
                <a class="btn btn-secondary" href="listar_usuarios.php">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>

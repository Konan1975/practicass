<?php
include('conexion.php');

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: listar_usuarios.php'); exit; }

$stmt = $conexion->prepare('DELETE FROM usuarios WHERE id = ?');
$stmt->bind_param('i', $id);
if ($stmt->execute()) {
    $stmt->close();
    $conexion->close();
    header('Location: listar_usuarios.php'); exit;
} else {
    $err = $stmt->error;
    $stmt->close();
    $conexion->close();
    echo 'Error al eliminar: ' . htmlspecialchars($err);
}

?>

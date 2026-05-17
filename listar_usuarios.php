<?php
include("conexion.php");

$sql = "SELECT id, cedula, nombre, correo FROM usuarios";
$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios - Sistema Jurídico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.html">⚖️ Gestión Jurídica</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.html">Inicio</a>
                <a class="nav-link" href="registrar_usuario.html">Registro</a>
                <a class="nav-link active" href="listar_usuarios.php">Usuarios</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="text-center mb-4 text-primary">Usuarios del Sistema Jurídico</h2>
        <p class="text-center text-muted mb-4">Listado de abogados y personal autorizado para gestionar expedientes judiciales</p>
        
        <div class="d-flex justify-content-between mb-3">
            <a href="registrar_usuario.html" class="btn btn-success">
                <i class="bi bi-person-plus"></i> Nuevo Usuario
            </a>
            <a href="index.html" class="btn btn-secondary">Volver al Inicio</a>
        </div>

        <?php
        if($resultado->num_rows > 0){
            echo "<div class='table-responsive'>";
            echo "<table class='table table-striped table-hover table-bordered'>";
            echo "<thead class='table-dark'>";
                        echo "<tr>
                                        <th>ID</th>
                                        <th>Cédula</th>
                                        <th>Nombres</th>
                                        <th>Correo Institucional</th>
                                        <th>Acciones</th>
                                    </tr>";
            echo "</thead>";
            echo "<tbody>";
            
            while($fila = $resultado->fetch_assoc()){
                $id_esc = htmlspecialchars($fila['id']);
                $cedula_esc = htmlspecialchars($fila['cedula']);
                $nombre_esc = htmlspecialchars($fila['nombre']);
                $correo_esc = htmlspecialchars($fila['correo']);
                
                echo "<tr>";
                echo "<td>{$id_esc}</td>";
                echo "<td>{$cedula_esc}</td>";
                echo "<td>{$nombre_esc}</td>";
                echo "<td>{$correo_esc}</td>";
                echo "<td>
                        <a href='editar.php?id={$id_esc}' class='btn btn-sm btn-info'>Editar</a>
                        <a href='borrar.php?id={$id_esc}' class='btn btn-sm btn-danger' 
                           onclick='return confirm(\"¿Desea eliminar este usuario del sistema?\")'>Eliminar</a>
                      </td>";
                echo "</tr>";
            }
            
            echo "</tbody>";
            echo "</table>";
            echo "</div>";
        } else {
            echo "<div class='alert alert-info text-center'>
                    <i class='bi bi-info-circle'></i> No hay usuarios registrados en el sistema.
                  </div>";
        }
        
        $conexion->close();
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
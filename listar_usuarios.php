<?php
session_start();
include("conexion.php");

$busqueda = trim($_GET['busqueda'] ?? '');
$pagina = intval($_GET['pagina'] ?? 1);
if ($pagina < 1) $pagina = 1;

$por_pagina = 10;
$offset = ($pagina - 1) * $por_pagina;

$condicion = '';
$params = [];
if ($busqueda !== '') {
    $condicion = " WHERE nombre LIKE ? OR cedula LIKE ?";
    $buscar = "%$busqueda%";
    $params = [$buscar, $buscar];
}

$stmt_count = $conexion->prepare("SELECT COUNT(*) as total FROM usuarios" . $condicion);
if ($params) $stmt_count->bind_param('ss', ...$params);
$stmt_count->execute();
$total_result = $stmt_count->get_result()->fetch_assoc();
$total = $total_result['total'];
$stmt_count->close();

$total_paginas = ceil($total / $por_pagina);
if ($pagina > $total_paginas && $total_paginas > 0) $pagina = $total_paginas;
$offset = ($pagina - 1) * $por_pagina;

$sql = "SELECT id, cedula, nombre, correo FROM usuarios" . $condicion . " LIMIT ? OFFSET ?";
$stmt = $conexion->prepare($sql);
if ($params) {
    $stmt->bind_param('ssii', $params[0], $params[1], $por_pagina, $offset);
} else {
    $stmt->bind_param('ii', $por_pagina, $offset);
}
$stmt->execute();
$resultado = $stmt->get_result();
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
        
        <?php
        if (!empty($_SESSION['mensaje'])) {
            $tipo = $_SESSION['tipo'] ?? 'info';
            echo "<div class='alert alert-{$tipo} alert-dismissible fade show'>";
            echo htmlspecialchars($_SESSION['mensaje']);
            echo "<button class='btn-close' data-bs-dismiss='alert'></button></div>";
            unset($_SESSION['mensaje']);
            unset($_SESSION['tipo']);
        }
        ?>
        
        <div class="d-flex justify-content-between mb-3 gap-2">
            <a href="registrar_usuario.html" class="btn btn-success">
                <i class="bi bi-person-plus"></i> Nuevo Usuario
            </a>
            <form method="get" class="d-flex gap-2 flex-grow-1" style="max-width: 400px;">
                <input type="text" name="busqueda" class="form-control" placeholder="Buscar por nombre o cédula" value="<?php echo htmlspecialchars($busqueda); ?>">
                <button type="submit" class="btn btn-primary">Buscar</button>
                <?php if ($busqueda !== '') echo '<a href="listar_usuarios.php" class="btn btn-secondary">Limpiar</a>'; ?>
            </form>
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
            
            if ($total_paginas > 1) {
                echo "<nav aria-label='Paginación' class='mt-4'>";
                echo "<ul class='pagination justify-content-center'>";
                
                if ($pagina > 1) {
                    $url = "listar_usuarios.php?pagina=1" . ($busqueda ? "&busqueda=" . urlencode($busqueda) : "");
                    echo "<li class='page-item'><a class='page-link' href='$url'>Primera</a></li>";
                    $url = "listar_usuarios.php?pagina=" . ($pagina - 1) . ($busqueda ? "&busqueda=" . urlencode($busqueda) : "");
                    echo "<li class='page-item'><a class='page-link' href='$url'>Anterior</a></li>";
                }
                
                for ($p = max(1, $pagina - 2); $p <= min($total_paginas, $pagina + 2); $p++) {
                    $active = ($p == $pagina) ? 'active' : '';
                    $url = "listar_usuarios.php?pagina=$p" . ($busqueda ? "&busqueda=" . urlencode($busqueda) : "");
                    echo "<li class='page-item $active'><a class='page-link' href='$url'>$p</a></li>";
                }
                
                if ($pagina < $total_paginas) {
                    $url = "listar_usuarios.php?pagina=" . ($pagina + 1) . ($busqueda ? "&busqueda=" . urlencode($busqueda) : "");
                    echo "<li class='page-item'><a class='page-link' href='$url'>Siguiente</a></li>";
                    $url = "listar_usuarios.php?pagina=$total_paginas" . ($busqueda ? "&busqueda=" . urlencode($busqueda) : "");
                    echo "<li class='page-item'><a class='page-link' href='$url'>Última</a></li>";
                }
                
                echo "</ul>";
                echo "</nav>";
                echo "<p class='text-center text-muted small'>Página $pagina de $total_paginas (Total: $total usuarios)</p>";
            }
        } else {
            echo "<div class='alert alert-info text-center'>
                    <i class='bi bi-info-circle'></i> No hay usuarios registrados en el sistema.
                  </div>";
        }
        
        $stmt->close();
        $conexion->close();
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
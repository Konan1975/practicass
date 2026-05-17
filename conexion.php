<?php
$host = 'localhost';
$usuario = 'root';
$clave = '';
$bd = 'practicass';

$conexion = new mysqli($host, $usuario, $clave, $bd);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>
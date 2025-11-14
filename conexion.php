<?php // conexion.php 
$conexion = new mysqli('localhost', 'root', 'Monse171002.', 'mundial_reddit'); 
if ($conexion->connect_errno) { 
    die('Error de conexión MySQL: ' . $conexion->connect_error); 
} 
    $conexion->set_charset('utf8mb4');
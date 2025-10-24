<?php
require_once __DIR__ . '/../conexion.php';

echo "<pre>";
echo "Host: " . $conexion->host_info . "\n";

$r = $conexion->query("SELECT DATABASE() db"); 
echo "BD: " . ($r->fetch_assoc()['db'] ?? 'N/A') . "\n";

$ok = $conexion->query("SHOW TABLES LIKE 'usuarios'");
echo $ok && $ok->num_rows ? "Tabla 'usuarios' ✅\n" : "Tabla 'usuarios' ❌\n";

$r2 = $conexion->query("SELECT COUNT(*) c FROM usuarios");
echo "Usuarios: " . ($r2->fetch_assoc()['c'] ?? 0) . "\n";

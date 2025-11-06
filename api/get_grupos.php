<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../conexion.php';

try {
  $sql = "SELECT 
            mundial_id AS id,
            nombre_comunidad AS nombre,
            descripcion,
            sede,
            logo_url AS logo,
            portada_url AS portada,
            slug,
            creado_en
          FROM mundial
          ORDER BY creado_en DESC
          LIMIT 6"; // solo los 6 más recientes

  $res = $conexion->query($sql);

  if (!$res) throw new Exception("Error en la consulta: " . $conexion->error);

  $grupos = [];
  while ($row = $res->fetch_assoc()) {
    $grupos[] = $row;
  }

  echo json_encode([
    'ok' => true,
    'data' => $grupos
  ]);

} catch (Exception $e) {
  echo json_encode([
    'ok' => false,
    'error' => $e->getMessage()
  ]);
}

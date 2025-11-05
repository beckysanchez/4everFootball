<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../conexion.php';

$sql = "SELECT * FROM vista_publicaciones_detalle ORDER BY creada_en DESC";
$result = $conexion->query($sql);

if (!$result) {
    echo json_encode(['ok' => false, 'error' => $conexion->error]);
    exit;
}

$publicaciones = [];
while ($fila = $result->fetch_assoc()) {
    $publicaciones[] = $fila;
}

echo json_encode([
    'ok' => true,
    'total' => count($publicaciones),
    'publicaciones' => $publicaciones
]);
async function aplicarFiltros() {
  const cat = document.querySelector('#selectCategoria').value; // id que tengas
  const res = await fetch(`api/feed.php?categoria=${encodeURIComponent(cat)}`);
  const { ok, publicaciones } = await res.json();
  if (ok) {
    document.getElementById('feed').innerHTML = publicaciones.map(renderCard).join('');
    hookCommentForms();
  }
}


$conexion->close();
?>

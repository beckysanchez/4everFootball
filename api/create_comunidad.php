<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../conexion.php';

// Función para generar slug único
function generarSlug($texto, $conexion) {
    $slugBase = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $texto), '-'));
    $slug = $slugBase;
    $contador = 1;

    // Evitar duplicados
    while (true) {
        $query = $conexion->prepare("SELECT COUNT(*) FROM comunidades WHERE slug = ?");
        $query->bind_param("s", $slug);
        $query->execute();
        $query->bind_result($existe);
        $query->fetch();
        $query->close();

        if ($existe == 0) break;
        $slug = $slugBase . '-' . $contador;
        $contador++;
    }

    return $slug;
}

try {
    $json = json_decode(file_get_contents("php://input"), true);

    if (!$json || empty($json['nombre'])) {
        echo json_encode(['ok' => false, 'error' => 'Nombre de comunidad requerido']);
        exit;
    }

    $nombre = trim($json['nombre']);
    $descripcion = $json['descripcion'] ?? '';
    $sede = $json['sede'] ?? '';
    $logo = $json['logo'] ?? '';
    $portada = $json['portada'] ?? '';

    // Generar slug automáticamente
    $slug = generarSlug($nombre, $conexion);

    $stmt = $conexion->prepare("
        INSERT INTO comunidades (nombre, descripcion, sede, logo, portada, slug, fecha_creacion)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("ssssss", $nombre, $descripcion, $sede, $logo, $portada, $slug);
    $ok = $stmt->execute();

    if ($ok) {
        echo json_encode(['ok' => true, 'slug' => $slug]);
    } else {
        echo json_encode(['ok' => false, 'error' => $conexion->error]);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
?>

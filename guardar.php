<?php
// guardar.php
// Guarda directamente el index.html que está en la misma carpeta.
// IMPORTANTE: proteger esta carpeta con contraseña/Basic Auth en producción.

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
    exit;
}

$input = file_get_contents('php://input');

if ($input === false || $input === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'No se recibió contenido']);
    exit;
}

$archivo = __DIR__ . '/index.html';

// Verificación básica para evitar guardar cualquier cosa accidentalmente.
if (stripos($input, '<!DOCTYPE html') === false && stripos($input, '<html') === false) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'El contenido recibido no parece ser un HTML válido']);
    exit;
}

// Backup automático antes de sobrescribir.
$backup = __DIR__ . '/index.backup.html';
if (file_exists($archivo)) {
    @copy($archivo, $backup);
}

if (file_put_contents($archivo, $input, LOCK_EX) === false) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'No se pudo escribir index.html. Revisá permisos del servidor.']);
    exit;
}

echo json_encode(['ok' => true, 'message' => 'index.html actualizado correctamente']);
?>

<?php
/**
 * Endpoint para recibir y guardar mensajes de contacto vía AJAX
 */
header('Content-Type: application/json; charset=utf-8');

// Leer los datos JSON del cuerpo de la petición (Payload)
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No se recibieron datos válidos.']);
    exit;
}

// Saneamiento de los datos de entrada
$nombre = htmlspecialchars(strip_tags(trim($data['nombre'] ?? '')));
$correo = filter_var(trim($data['correo'] ?? ''), FILTER_SANITIZE_EMAIL);
$asunto = htmlspecialchars(strip_tags(trim($data['asunto'] ?? '')));
$mensaje = htmlspecialchars(strip_tags(trim($data['mensaje'] ?? '')));

// Validación
if (empty($nombre) || empty($correo) || empty($asunto) || empty($mensaje)) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios.']);
    exit;
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'El formato del correo electrónico no es válido.']);
    exit;
}

// Inclusión de la conexión a la base de datos
require_once 'conexion.php';

try {
    // Inserción segura con sentencias preparadas
    $sql = "INSERT INTO mensajes_contacto (nombre, correo, asunto, mensaje, fecha) VALUES (:nombre, :correo, :asunto, :mensaje, NOW())";
    $stmt = $pdo->prepare($sql);
    
    $resultado = $stmt->execute([
        'nombre' => $nombre,
        'correo' => $correo,
        'asunto' => $asunto,
        'mensaje' => $mensaje
    ]);
    
    if ($resultado) {
        echo json_encode(['success' => true, 'message' => '¡Mensaje enviado con éxito! Te responderé a la brevedad.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se pudo guardar el mensaje.']);
    }
} catch (PDOException $e) {
    // En producción se recomienda registrar en un log y no enviar el detalle al frontend
    echo json_encode(['success' => false, 'message' => 'Error interno en el servidor. Por favor intenta más tarde.']);
}
?>

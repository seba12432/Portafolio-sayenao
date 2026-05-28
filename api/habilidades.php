<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

require_once '../conexion.php';

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

try {
    if ($method === 'GET') {
        $stmt = $pdo->query("SELECT * FROM habilidades");
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    } 
    elseif ($method === 'POST') {
        // Para emular PUT y DELETE via POST con un action
        $action = $input['action'] ?? 'create';
        
        if ($action === 'create') {
            $nombre = $input['nombre'] ?? '';
            $icono = $input['icono'] ?? '';
            $color = $input['color_clase'] ?? '';
            
            $stmt = $pdo->prepare("INSERT INTO habilidades (nombre, icono, color_clase) VALUES (:nombre, :icono, :color)");
            $stmt->execute(['nombre' => $nombre, 'icono' => $icono, 'color' => $color]);
            echo json_encode(['success' => true, 'message' => 'Habilidad creada.', 'id' => $pdo->lastInsertId()]);
        } 
        elseif ($action === 'update') {
            $id = $input['id'] ?? 0;
            $nombre = $input['nombre'] ?? '';
            $icono = $input['icono'] ?? '';
            $color = $input['color_clase'] ?? '';
            
            $stmt = $pdo->prepare("UPDATE habilidades SET nombre = :nombre, icono = :icono, color_clase = :color WHERE id = :id");
            $stmt->execute(['nombre' => $nombre, 'icono' => $icono, 'color' => $color, 'id' => $id]);
            echo json_encode(['success' => true, 'message' => 'Habilidad actualizada.']);
        }
        elseif ($action === 'delete') {
            $id = $input['id'] ?? 0;
            $stmt = $pdo->prepare("DELETE FROM habilidades WHERE id = :id");
            $stmt->execute(['id' => $id]);
            echo json_encode(['success' => true, 'message' => 'Habilidad eliminada.']);
        }
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>

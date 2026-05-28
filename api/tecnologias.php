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
        $stmt = $pdo->query("SELECT * FROM tecnologias");
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    } 
    elseif ($method === 'POST') {
        $action = $input['action'] ?? 'create';
        
        if ($action === 'create') {
            $nombre = $input['nombre'] ?? '';
            $porcentaje = $input['porcentaje'] ?? 0;
            $color = $input['color_clase'] ?? '';
            
            $stmt = $pdo->prepare("INSERT INTO tecnologias (nombre, porcentaje, color_clase) VALUES (:nombre, :porcentaje, :color)");
            $stmt->execute(['nombre' => $nombre, 'porcentaje' => $porcentaje, 'color' => $color]);
            echo json_encode(['success' => true, 'message' => 'Tecnología creada.', 'id' => $pdo->lastInsertId()]);
        } 
        elseif ($action === 'update') {
            $id = $input['id'] ?? 0;
            $nombre = $input['nombre'] ?? '';
            $porcentaje = $input['porcentaje'] ?? 0;
            $color = $input['color_clase'] ?? '';
            
            $stmt = $pdo->prepare("UPDATE tecnologias SET nombre = :nombre, porcentaje = :porcentaje, color_clase = :color WHERE id = :id");
            $stmt->execute(['nombre' => $nombre, 'porcentaje' => $porcentaje, 'color' => $color, 'id' => $id]);
            echo json_encode(['success' => true, 'message' => 'Tecnología actualizada.']);
        }
        elseif ($action === 'delete') {
            $id = $input['id'] ?? 0;
            $stmt = $pdo->prepare("DELETE FROM tecnologias WHERE id = :id");
            $stmt->execute(['id' => $id]);
            echo json_encode(['success' => true, 'message' => 'Tecnología eliminada.']);
        }
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>

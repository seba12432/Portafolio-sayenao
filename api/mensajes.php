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
        $stmt = $pdo->query("SELECT * FROM mensajes_contacto ORDER BY fecha DESC");
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    } 
    elseif ($method === 'POST') {
        $action = $input['action'] ?? '';
        
        if ($action === 'delete') {
            $id = $input['id'] ?? 0;
            $stmt = $pdo->prepare("DELETE FROM mensajes_contacto WHERE id = :id");
            $stmt->execute(['id' => $id]);
            echo json_encode(['success' => true, 'message' => 'Mensaje eliminado.']);
        }
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>

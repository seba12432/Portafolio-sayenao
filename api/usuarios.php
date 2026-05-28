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
        $stmt = $pdo->query("SELECT id, nombre, email, creado_en FROM usuarios_admin");
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    } 
    elseif ($method === 'POST') {
        $action = $input['action'] ?? 'create';
        
        if ($action === 'create') {
            $nombre = $input['nombre'] ?? '';
            $email = $input['email'] ?? '';
            $password = $input['password'] ?? '';
            
            $hash = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("INSERT INTO usuarios_admin (nombre, email, password) VALUES (:nombre, :email, :password)");
            $stmt->execute(['nombre' => $nombre, 'email' => $email, 'password' => $hash]);
            echo json_encode(['success' => true, 'message' => 'Usuario creado.', 'id' => $pdo->lastInsertId()]);
        } 
        elseif ($action === 'update') {
            $id = $input['id'] ?? 0;
            $nombre = $input['nombre'] ?? '';
            $email = $input['email'] ?? '';
            $password = $input['password'] ?? '';
            
            if (!empty($password)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE usuarios_admin SET nombre = :nombre, email = :email, password = :password WHERE id = :id");
                $stmt->execute(['nombre' => $nombre, 'email' => $email, 'password' => $hash, 'id' => $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE usuarios_admin SET nombre = :nombre, email = :email WHERE id = :id");
                $stmt->execute(['nombre' => $nombre, 'email' => $email, 'id' => $id]);
            }
            
            echo json_encode(['success' => true, 'message' => 'Usuario actualizado.']);
        }
        elseif ($action === 'delete') {
            $id = $input['id'] ?? 0;
            if ($id == $_SESSION['admin_id']) {
                echo json_encode(['success' => false, 'message' => 'No puedes eliminar tu propio usuario.']);
                exit;
            }
            $stmt = $pdo->prepare("DELETE FROM usuarios_admin WHERE id = :id");
            $stmt->execute(['id' => $id]);
            echo json_encode(['success' => true, 'message' => 'Usuario eliminado.']);
        }
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>

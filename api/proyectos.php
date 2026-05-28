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
        $stmt = $pdo->query("SELECT * FROM proyectos");
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    } 
    elseif ($method === 'POST') {
        $action = $input['action'] ?? 'create';
        
        if ($action === 'create') {
            $titulo = $input['titulo'] ?? '';
            $descripcion = $input['descripcion'] ?? '';
            $imagen = $input['imagen'] ?? '';
            $url_demo = $input['url_demo'] ?? '';
            $url_github = $input['url_github'] ?? '';
            
            $stmt = $pdo->prepare("INSERT INTO proyectos (titulo, descripcion, imagen, url_demo, url_github) VALUES (:titulo, :descripcion, :imagen, :url_demo, :url_github)");
            $stmt->execute([
                'titulo' => $titulo, 
                'descripcion' => $descripcion, 
                'imagen' => $imagen, 
                'url_demo' => $url_demo, 
                'url_github' => $url_github
            ]);
            echo json_encode(['success' => true, 'message' => 'Proyecto creado.', 'id' => $pdo->lastInsertId()]);
        } 
        elseif ($action === 'update') {
            $id = $input['id'] ?? 0;
            $titulo = $input['titulo'] ?? '';
            $descripcion = $input['descripcion'] ?? '';
            $imagen = $input['imagen'] ?? '';
            $url_demo = $input['url_demo'] ?? '';
            $url_github = $input['url_github'] ?? '';
            
            $stmt = $pdo->prepare("UPDATE proyectos SET titulo = :titulo, descripcion = :descripcion, imagen = :imagen, url_demo = :url_demo, url_github = :url_github WHERE id = :id");
            $stmt->execute([
                'titulo' => $titulo, 
                'descripcion' => $descripcion, 
                'imagen' => $imagen, 
                'url_demo' => $url_demo, 
                'url_github' => $url_github,
                'id' => $id
            ]);
            echo json_encode(['success' => true, 'message' => 'Proyecto actualizado.']);
        }
        elseif ($action === 'delete') {
            $id = $input['id'] ?? 0;
            $stmt = $pdo->prepare("DELETE FROM proyectos WHERE id = :id");
            $stmt->execute(['id' => $id]);
            echo json_encode(['success' => true, 'message' => 'Proyecto eliminado.']);
        }
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>

<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

require_once '../conexion.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $stmt = $pdo->query("SELECT * FROM biografia WHERE id = 1");
        $data = $stmt->fetch();
        if (!$data) {
            // Default si no existe
            $data = ['saludo' => '', 'nombre_completo' => '', 'titulo' => '', 'descripcion' => '', 'cv_url' => '', 'github_url' => '', 'linkedin_url' => '', 'email_contacto' => ''];
        }
        echo json_encode(['success' => true, 'data' => $data]);
    } 
    elseif ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        $saludo = $input['saludo'] ?? '';
        $nombre = $input['nombre_completo'] ?? '';
        $titulo = $input['titulo'] ?? '';
        $descripcion = $input['descripcion'] ?? '';
        $cv = $input['cv_url'] ?? '';
        $github = $input['github_url'] ?? '';
        $linkedin = $input['linkedin_url'] ?? '';
        $email = $input['email_contacto'] ?? '';
        
        $sql = "UPDATE biografia SET 
                saludo = :saludo, 
                nombre_completo = :nombre, 
                titulo = :titulo, 
                descripcion = :descripcion, 
                cv_url = :cv, 
                github_url = :github, 
                linkedin_url = :linkedin, 
                email_contacto = :email 
                WHERE id = 1";
                
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'saludo' => $saludo,
            'nombre' => $nombre,
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'cv' => $cv,
            'github' => $github,
            'linkedin' => $linkedin,
            'email' => $email
        ]);
        
        // Si no se actualizó porque no existía el registro 1, lo insertamos
        if ($stmt->rowCount() === 0) {
            $check = $pdo->query("SELECT COUNT(*) FROM biografia WHERE id = 1")->fetchColumn();
            if ($check == 0) {
                $sqlInsert = "INSERT INTO biografia (id, saludo, nombre_completo, titulo, descripcion, cv_url, github_url, linkedin_url, email_contacto) 
                              VALUES (1, :saludo, :nombre, :titulo, :descripcion, :cv, :github, :linkedin, :email)";
                $stmtInsert = $pdo->prepare($sqlInsert);
                $stmtInsert->execute(['saludo'=>$saludo, 'nombre'=>$nombre, 'titulo'=>$titulo, 'descripcion'=>$descripcion, 'cv'=>$cv, 'github'=>$github, 'linkedin'=>$linkedin, 'email'=>$email]);
            }
        }
        
        echo json_encode(['success' => true, 'message' => 'Biografía actualizada correctamente.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>

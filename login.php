<?php
session_start();

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit;
}

require_once 'conexion.php';
$error = '';

// Procesar el formulario si se envió por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error = 'Por favor, completa todos los campos.';
    } else {
        try {
            // Consulta segura con sentencias preparadas
            $stmt = $pdo->prepare("SELECT id, nombre, password FROM usuarios_admin WHERE email = :email LIMIT 1");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            // Verificar si el usuario existe y comprobar la contraseña con password_verify
            if ($user && password_verify($password, $user['password'])) {
                // Regenerar el ID de sesión por seguridad
                session_regenerate_id(true);

                // Configurar variables de sesión
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_nombre'] = $user['nombre'];
                
                // Redirigir al dashboard
                header("Location: dashboard.php");
                exit;
            } else {
                $error = 'Correo o contraseña incorrectos.';
            }
        } catch (PDOException $e) {
            $error = 'Error en el sistema. Por favor intenta más tarde.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Portafolio</title>
    <!-- Bootstrap CSS (5.3.x) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Hoja de Estilos Principal -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="login-layout">

    <div class="login-card mx-3">
        <div class="login-logo shadow-sm">
            <i class="bi bi-shield-lock-fill fs-2"></i>
        </div>
        <h4 class="text-center mb-4 fw-bold">Acceso Administrativo</h4>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label fw-medium text-secondary small text-uppercase">Correo Electrónico</label>
                <div class="input-group">
                    <span class="input-group-text text-muted"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control py-2" id="email" name="email" required placeholder="admin@correo.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" autofocus>
                </div>
            </div>
            
            <div class="mb-4">
                <label for="password" class="form-label fw-medium text-secondary small text-uppercase">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text text-muted"><i class="bi bi-key"></i></span>
                    <input type="password" class="form-control py-2" id="password" name="password" required placeholder="••••••••">
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary w-100 py-2 mb-3 fw-bold shadow-sm">
                Iniciar Sesión <i class="bi bi-box-arrow-in-right ms-2"></i>
            </button>
            
            <div class="text-center">
                <a href="index.php" class="text-decoration-none text-muted small hover-primary">
                    <i class="bi bi-arrow-left me-1"></i> Volver al sitio público
                </a>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Script Principal -->
    <script src="assets/js/main.js"></script>
</body>
</html>

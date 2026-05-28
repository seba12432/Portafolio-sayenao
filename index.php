<?php
session_start();

// Manejo de Logout
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
    session_destroy();
    header("Location: index.php");
    exit;
}

require_once 'conexion.php';

// Consultar datos
try {
    $bio = $pdo->query("SELECT * FROM biografia WHERE id = 1")->fetch();
    if (!$bio) {
        $bio = [
            'saludo' => 'Hola, Soy',
            'nombre_completo' => 'Tu Nombre',
            'titulo' => 'Desarrollador Web',
            'descripcion' => 'Descripción de ejemplo.',
            'cv_url' => '#',
            'github_url' => '#',
            'linkedin_url' => '#',
            'email_contacto' => '#'
        ];
    }
    
    $habilidades = $pdo->query("SELECT * FROM habilidades")->fetchAll();
    $tecnologias = $pdo->query("SELECT * FROM tecnologias")->fetchAll();
    $proyectos = $pdo->query("SELECT * FROM proyectos")->fetchAll();
    
} catch (PDOException $e) {
    die("Error conectando a la base de datos.");
}

$is_logged_in = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portafolio | <?= htmlspecialchars($bio['nombre_completo']) ?></title>
    
    <!-- Bootstrap CSS (5.3.x) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Hoja de Estilos Principal -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="public-layout">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <!-- Logo / Nombre Izquierda -->
            <a class="navbar-brand d-flex align-items-center" href="#">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 avatar-sm">
                    <img src="assets/img/sayenao.jpg" alt="MiniAvatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                </div>
                <div>
                    <h5 class="mb-0 fw-bold"><?= htmlspecialchars($bio['nombre_completo']) ?></h5>
                    <small class="text-muted d-block text-xs"><?= htmlspecialchars($bio['titulo']) ?></small>
                </div>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Enlaces Derecha -->
                <ul class="navbar-nav ms-auto align-items-center gap-3">
                    <li class="nav-item">
                        <a class="nav-link" href="#biografia"><i class="bi bi-person me-1"></i> Biografía</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#habilidades"><i class="bi bi-star me-1"></i> Habilidades</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#tecnologias"><i class="bi bi-code-slash me-1"></i> Tecnologías</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#proyectos"><i class="bi bi-briefcase me-1"></i> Proyectos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contacto"><i class="bi bi-envelope me-1"></i> Contacto</a>
                    </li>
                    <li class="nav-item ms-lg-3">
                        <?php if ($is_logged_in): ?>
                            <a href="dashboard.php" class="btn btn-outline-primary fw-medium px-4 py-2">
                                <i class="bi bi-speedometer2 me-1"></i> Ir al Dashboard
                            </a>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-outline-primary fw-medium px-4 py-2">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar Sesión
                            </a>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero / Biografía -->
    <section id="biografia" class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <!-- Avatar Izquierda -->
               <div class="col-lg-5 mb-5 mb-lg-0 d-flex justify-content-center">
                    <img src="assets/img/sayenao.jpg" alt="Avatar" class="img-fluid rounded-circle shadow-lg" style="width: 320px; height: 320px; object-fit: cover; border: 5px solid white;">
            </div>
                <!-- Textos Derecha -->
                <div class="col-lg-7 px-lg-5">
                    <h6 class="text-primary fw-bold text-uppercase mb-3 letter-spacing-1"><?= htmlspecialchars($bio['saludo']) ?></h6>
                    <h1 class="display-3 fw-bold mb-3"><?= htmlspecialchars($bio['nombre_completo']) ?></h1>
                    <h3 class="text-primary mb-4 fw-normal"><?= htmlspecialchars($bio['titulo']) ?></h3>
                    <p class="lead text-secondary mb-5 line-height-lg">
                        <?= nl2br(htmlspecialchars($bio['descripcion'])) ?>
                    </p>
                    <div class="d-flex flex-wrap align-items-center gap-4">
                        <?php if (!empty($bio['cv_url']) && $bio['cv_url'] !== '#'): ?>
                            <a href="<?= htmlspecialchars($bio['cv_url']) ?>" target="_blank" class="btn btn-primary btn-lg px-4 py-3 fw-medium">
                                <i class="bi bi-download me-2"></i> Ver CV
                            </a>
                        <?php endif; ?>
                        <div class="hero-social-icons">
                            <?php if (!empty($bio['github_url']) && $bio['github_url'] !== '#'): ?>
                                <a href="<?= htmlspecialchars($bio['github_url']) ?>" target="_blank"><i class="bi bi-github"></i></a>
                            <?php endif; ?>
                            <?php if (!empty($bio['linkedin_url']) && $bio['linkedin_url'] !== '#'): ?>
                                <a href="<?= htmlspecialchars($bio['linkedin_url']) ?>" target="_blank"><i class="bi bi-linkedin"></i></a>
                            <?php endif; ?>
                            <?php if (!empty($bio['email_contacto']) && $bio['email_contacto'] !== '#'): ?>
                                <a href="mailto:<?= htmlspecialchars($bio['email_contacto']) ?>"><i class="bi bi-envelope"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección Dividida: Habilidades y Tecnologías -->
    <section class="section-padding bg-light">
        <div class="container">
            <div class="row g-5">
                <!-- Columna Izquierda: Habilidades y Herramientas -->
                <div class="col-lg-6" id="habilidades">
                    <h4 class="section-title"><i class="bi bi-star text-primary me-2"></i> Habilidades y Herramientas</h4>
                    <div class="row g-3">
                        <?php if (empty($habilidades)): ?>
                            <p class="text-muted">Aún no hay habilidades registradas.</p>
                        <?php else: ?>
                            <?php foreach ($habilidades as $hab): ?>
                                <div class="col-6 col-sm-3">
                                    <div class="skill-card">
                                        <i class="bi <?= htmlspecialchars($hab['icono']) ?> skill-icon <?= htmlspecialchars($hab['color_clase']) ?>"></i>
                                        <h6 class="mb-0 fw-bold text-secondary"><?= htmlspecialchars($hab['nombre']) ?></h6>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Columna Derecha: Tecnologías Dominadas -->
                <div class="col-lg-6 ps-lg-5" id="tecnologias">
                    <h4 class="section-title"><i class="bi bi-code-slash text-primary me-2"></i> Tecnologías Dominadas</h4>
                    
                    <?php if (empty($tecnologias)): ?>
                        <p class="text-muted">Aún no hay tecnologías registradas.</p>
                    <?php else: ?>
                        <?php foreach ($tecnologias as $tec): ?>
                            <div class="progress-wrapper">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fw-bold text-secondary"><?= htmlspecialchars($tec['nombre']) ?></span>
                                    <span class="text-muted small"><?= (int)$tec['porcentaje'] ?>%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar <?= htmlspecialchars($tec['color_clase']) ?>" role="progressbar" style="width: <?= (int)$tec['porcentaje'] ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Proyectos Realizados -->
    <section class="section-padding bg-white" id="proyectos">
        <div class="container">
            <h4 class="section-title text-center mb-5"><i class="bi bi-briefcase text-primary me-2"></i> Proyectos Realizados</h4>
            
            <div class="row g-4 mb-5">
                <?php if (empty($proyectos)): ?>
                    <div class="col-12 text-center text-muted">Aún no hay proyectos registrados.</div>
                <?php else: ?>
                    <?php foreach ($proyectos as $pro): ?>
                        <div class="col-md-4">
                            <div class="project-card">
                                <?php if (strpos($pro['imagen'], 'http') === 0 || strpos($pro['imagen'], '/') === 0): ?>
                                    <img src="<?= htmlspecialchars($pro['imagen']) ?>" alt="Proyecto" style="width: 100%; height: 200px; object-fit: cover; border-top-left-radius: inherit; border-top-right-radius: inherit;">
                                <?php else: ?>
                                    <div class="project-img-placeholder">
                                        <i class="bi <?= htmlspecialchars($pro['imagen'] ?: 'bi-image') ?>"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="project-card-body">
                                    <h5 class="text-primary fw-bold mb-3"><?= htmlspecialchars($pro['titulo']) ?></h5>
                                    <p class="text-muted mb-4" style="font-size: 0.95rem;"><?= htmlspecialchars($pro['descripcion']) ?></p>
                                    <div class="d-flex gap-2">
                                        <?php if (!empty($pro['url_demo'])): ?>
                                            <a href="<?= htmlspecialchars($pro['url_demo']) ?>" target="_blank" class="btn btn-primary w-50 py-2"><i class="bi bi-box-arrow-up-right me-1"></i> Demo</a>
                                        <?php endif; ?>
                                        <?php if (!empty($pro['url_github'])): ?>
                                            <a href="<?= htmlspecialchars($pro['url_github']) ?>" target="_blank" class="btn btn-outline-dark w-50 py-2"><i class="bi bi-github me-1"></i> GitHub</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Contacto -->
    <section class="section-padding bg-light" id="contacto">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="contact-form-card">
                        <h4 class="mb-4 fw-bold text-center"><i class="bi bi-envelope text-primary me-2"></i> Formulario de Contacto</h4>
                        <form id="contactForm">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <input type="text" class="form-control form-control-lg bg-light" id="contactNombre" placeholder="Nombre Completo" required>
                                </div>
                                <div class="col-md-6">
                                    <input type="email" class="form-control form-control-lg bg-light" id="contactCorreo" placeholder="Correo Electrónico" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <input type="text" class="form-control form-control-lg bg-light" id="contactAsunto" placeholder="Asunto" required>
                            </div>
                            <div class="mb-4">
                                <textarea class="form-control form-control-lg bg-light" id="contactMensaje" rows="5" placeholder="Mensaje" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold" id="btnSubmit">
                                <span>Enviar Mensaje</span> <i class="bi bi-send ms-2"></i>
                            </button>
                            
                            <!-- Contenedor para la alerta dinámica -->
                            <div id="formAlert" class="alert mt-3 d-none fw-medium" role="alert"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row align-items-center">
                <!-- Íconos de redes (Izquierda) -->
                <div class="col-md-3 mb-4 mb-md-0 d-flex justify-content-center justify-content-md-start">
                    <div class="footer-social-icons">
                        <?php if (!empty($bio['github_url']) && $bio['github_url'] !== '#'): ?>
                            <a href="<?= htmlspecialchars($bio['github_url']) ?>" target="_blank"><i class="bi bi-github"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($bio['linkedin_url']) && $bio['linkedin_url'] !== '#'): ?>
                            <a href="<?= htmlspecialchars($bio['linkedin_url']) ?>" target="_blank"><i class="bi bi-linkedin"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($bio['email_contacto']) && $bio['email_contacto'] !== '#'): ?>
                            <a href="mailto:<?= htmlspecialchars($bio['email_contacto']) ?>"><i class="bi bi-envelope"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Textos (Centro) -->
                <div class="col-md-6 text-center mb-4 mb-md-0">
                    <p class="fw-bold mb-2 text-dark">Sebastian Ayenao</p>
                    <p class="text-muted small mb-0"><?= htmlspecialchars($bio['titulo']) ?></p>
                </div>
                
                <div class="col-md-3 d-flex justify-content-center justify-content-md-end">
                </div>
            </div>
            
            <div class="row mt-5 pt-4 border-top">
                <div class="col-12 text-center">
                    <p class="fw-bold text-muted mb-0">&copy; <?= date('Y') ?> Sebastian Ayenao</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script Principal -->
    <script src="assets/js/main.js"></script>
</body>
</html>

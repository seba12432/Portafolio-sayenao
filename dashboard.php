<?php
session_start();

// Validar que exista la sesión activa
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

require_once 'conexion.php';

// Obtener datos del usuario desde la sesión
$admin_nombre = $_SESSION['admin_nombre'] ?? 'Administrador';
$admin_email = 'admin@portafolio.com'; 

// ==========================================
// CONSULTAS PARA EL DASHBOARD
// ==========================================
try {
    $countBiografia = $pdo->query("SELECT COUNT(*) FROM biografia")->fetchColumn();
    $countHabilidades = $pdo->query("SELECT COUNT(*) FROM habilidades")->fetchColumn();
    $countTecnologias = $pdo->query("SELECT COUNT(*) FROM tecnologias")->fetchColumn();
    $countProyectos = $pdo->query("SELECT COUNT(*) FROM proyectos")->fetchColumn();
    $countMensajes = $pdo->query("SELECT COUNT(*) FROM mensajes_contacto")->fetchColumn();
    $countUsuarios = $pdo->query("SELECT COUNT(*) FROM usuarios_admin")->fetchColumn();

    $stmtMensajes = $pdo->query("SELECT * FROM mensajes_contacto ORDER BY fecha DESC LIMIT 5");
    $ultimosMensajes = $stmtMensajes->fetchAll();

} catch (PDOException $e) {
    $countBiografia = $countHabilidades = $countTecnologias = $countProyectos = $countMensajes = $countUsuarios = 0;
    $ultimosMensajes = [];
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Portafolio Admin</title>
    <!-- Bootstrap CSS (5.3.x) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Hoja de Estilos Principal -->
    <link href="assets/css/style.css" rel="stylesheet">
    
    <style>
        .dashboard-view.d-none { display: none !important; }
    </style>
</head>

<body class="admin-layout">

    <div class="wrapper">
        <!-- Overlay Móvil -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar Lateral -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h5 class="mb-0 fw-bold tracking-wider">PORTAFOLIO<br>ADMIN</h5>
            </div>

            <div class="sidebar-profile">
                <div class="avatar shadow-sm">
                    <i class="bi bi-person-fill"></i>
                </div>
                <h6 class="mb-1 fw-bold"><?= htmlspecialchars($admin_nombre) ?></h6>
                <small class="text-white-50">Admin</small>
            </div>

            <nav class="sidebar-menu">
                <a href="#" class="sidebar-link active" data-view="dashboard"><i class="bi bi-speedometer2"></i> Dashboard</a>
                <a href="#" class="sidebar-link" data-view="biografia"><i class="bi bi-person-vcard"></i> Biografía</a>
                <a href="#" class="sidebar-link" data-view="habilidades"><i class="bi bi-star"></i> Habilidades</a>
                <a href="#" class="sidebar-link" data-view="tecnologias"><i class="bi bi-code-slash"></i> Tecnologías</a>
                <a href="#" class="sidebar-link" data-view="proyectos"><i class="bi bi-briefcase"></i> Proyectos</a>
                <a href="#" class="sidebar-link" data-view="mensajes"><i class="bi bi-envelope"></i> Mensajes</a>
                <a href="#" class="sidebar-link" data-view="usuarios"><i class="bi bi-people"></i> Usuarios</a>
            </nav>

            <div class="mt-auto p-3">
                <a href="index.php?logout=true" class="btn btn-outline-light w-100"><i class="bi bi-box-arrow-left me-2"></i> Cerrar Sesión</a>
            </div>
        </aside>

        <!-- Área Principal -->
        <div class="main-content">
            <!-- Topbar -->
            <header class="topbar">
                <div class="d-flex align-items-center">
                    <button class="btn btn-light d-lg-none me-3 shadow-sm border" id="sidebarToggle">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <div>
                        <h4 class="mb-0 fw-bold" id="topbar-title">Dashboard</h4>
                        <small class="text-muted d-none d-sm-block">Panel de Administración</small>
                    </div>
                </div>

                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2 border shadow-sm"
                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center avatar-xs">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <span class="d-none d-sm-block fw-medium"><?= htmlspecialchars($admin_nombre) ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li><a class="dropdown-item" href="index.php?logout=true"><i class="bi bi-box-arrow-left me-2"></i> Cerrar Sesión</a></li>
                    </ul>
                </div>
            </header>

            <!-- Contenedor Principal de Vistas -->
            <div class="p-3 p-md-4">
                
                <!-- Alertas Globales -->
                <div id="globalAlert" class="alert d-none alert-dismissible fade show" role="alert">
                    <span id="globalAlertMsg"></span>
                    <button type="button" class="btn-close" onclick="document.getElementById('globalAlert').classList.add('d-none')"></button>
                </div>

                <!-- ========================================== -->
                <!-- VISTA: DASHBOARD -->
                <!-- ========================================== -->
                <div id="view-dashboard" class="dashboard-view">
                    <!-- Fila de Tarjetas -->
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-4 col-xl">
                            <div class="summary-card bg-blue-light" style="cursor:pointer;" onclick="document.querySelector('[data-view=biografia]').click()">
                                <div class="summary-icon"><i class="bi bi-person-vcard"></i></div>
                                <div><h6 class="mb-0 small fw-bold text-uppercase">Biografía</h6><h3 class="mb-0 fw-bold"><?= htmlspecialchars($countBiografia) ?></h3></div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 col-xl">
                            <div class="summary-card bg-green-light" style="cursor:pointer;" onclick="document.querySelector('[data-view=habilidades]').click()">
                                <div class="summary-icon"><i class="bi bi-star"></i></div>
                                <div><h6 class="mb-0 small fw-bold text-uppercase">Habilidades</h6><h3 class="mb-0 fw-bold"><?= htmlspecialchars($countHabilidades) ?></h3></div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 col-xl">
                            <div class="summary-card bg-yellow-light" style="cursor:pointer;" onclick="document.querySelector('[data-view=tecnologias]').click()">
                                <div class="summary-icon"><i class="bi bi-code-slash"></i></div>
                                <div><h6 class="mb-0 small fw-bold text-uppercase">Tecnologías</h6><h3 class="mb-0 fw-bold"><?= htmlspecialchars($countTecnologias) ?></h3></div>
                            </div>
                        </div>
                        <div class="col-6 col-md-6 col-xl">
                            <div class="summary-card bg-purple-light" style="cursor:pointer;" onclick="document.querySelector('[data-view=proyectos]').click()">
                                <div class="summary-icon"><i class="bi bi-briefcase"></i></div>
                                <div><h6 class="mb-0 small fw-bold text-uppercase">Proyectos</h6><h3 class="mb-0 fw-bold"><?= htmlspecialchars($countProyectos) ?></h3></div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-xl">
                            <div class="summary-card bg-red-light" style="cursor:pointer;" onclick="document.querySelector('[data-view=mensajes]').click()">
                                <div class="summary-icon"><i class="bi bi-envelope"></i></div>
                                <div><h6 class="mb-0 small fw-bold text-uppercase">Mensajes</h6><h3 class="mb-0 fw-bold"><?= htmlspecialchars($countMensajes) ?></h3></div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla Últimos Mensajes -->
                    <div class="table-card mb-4 shadow-sm">
                        <h5 class="fw-bold mb-4">Últimos Mensajes de Contacto</h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Correo</th>
                                        <th>Asunto</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($ultimosMensajes)): ?>
                                        <tr><td colspan="4" class="text-center text-muted py-4">No hay mensajes recientes.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($ultimosMensajes as $msg): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($msg['nombre']) ?></td>
                                                <td><?= htmlspecialchars($msg['correo']) ?></td>
                                                <td><?= htmlspecialchars($msg['asunto']) ?></td>
                                                <td><?= date('d/m/Y H:i', strtotime($msg['fecha'])) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-4 pt-3 border-top">
                            <button class="btn btn-outline-primary px-4 py-2 fw-medium" onclick="document.querySelector('[data-view=mensajes]').click()">
                                Ver todos los mensajes <i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- VISTA: BIOGRAFIA -->
                <!-- ========================================== -->
                <div id="view-biografia" class="dashboard-view d-none">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4">Editar Biografía</h5>
                            <form id="formBiografia">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Saludo</label>
                                        <input type="text" class="form-control" name="saludo" id="bio_saludo">
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">Nombre Completo</label>
                                        <input type="text" class="form-control" name="nombre_completo" id="bio_nombre" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Título Profesional</label>
                                        <input type="text" class="form-control" name="titulo" id="bio_titulo" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Descripción</label>
                                        <textarea class="form-control" name="descripcion" id="bio_descripcion" rows="4" required></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">URL del CV</label>
                                        <input type="txt" class="form-control" name="cv_url" id="bio_cv">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email de Contacto Público</label>
                                        <input type="email" class="form-control" name="email_contacto" id="bio_email">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">URL de GitHub</label>
                                        <input type="url" class="form-control" name="github_url" id="bio_github">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">URL de LinkedIn</label>
                                        <input type="url" class="form-control" name="linkedin_url" id="bio_linkedin">
                                    </div>
                                </div>
                                <div class="mt-4 text-end">
                                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Guardar Cambios</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- VISTA: HABILIDADES -->
                <!-- ========================================== -->
                <div id="view-habilidades" class="dashboard-view d-none">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="fw-bold mb-0">Habilidades</h5>
                                <button class="btn btn-primary" onclick="Habilidades.abrirModal()"><i class="bi bi-plus-lg me-2"></i>Nueva Habilidad</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Ícono</th>
                                            <th>Nombre</th>
                                            <th>Clase Color</th>
                                            <th class="text-end">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody-habilidades">
                                        <!-- Cargado por JS -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- VISTA: TECNOLOGIAS -->
                <!-- ========================================== -->
                <div id="view-tecnologias" class="dashboard-view d-none">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="fw-bold mb-0">Tecnologías</h5>
                                <button class="btn btn-primary" onclick="Tecnologias.abrirModal()"><i class="bi bi-plus-lg me-2"></i>Nueva Tecnología</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Porcentaje</th>
                                            <th>Clase Color</th>
                                            <th class="text-end">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody-tecnologias">
                                        <!-- Cargado por JS -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- VISTA: PROYECTOS -->
                <!-- ========================================== -->
                <div id="view-proyectos" class="dashboard-view d-none">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="fw-bold mb-0">Proyectos</h5>
                                <button class="btn btn-primary" onclick="Proyectos.abrirModal()"><i class="bi bi-plus-lg me-2"></i>Nuevo Proyecto</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Imagen (URL)</th>
                                            <th>Título</th>
                                            <th>Demo</th>
                                            <th>GitHub</th>
                                            <th class="text-end">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody-proyectos">
                                        <!-- Cargado por JS -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- VISTA: MENSAJES -->
                <!-- ========================================== -->
                <div id="view-mensajes" class="dashboard-view d-none">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4">Bandeja de Mensajes</h5>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Remitente</th>
                                            <th>Asunto</th>
                                            <th>Mensaje</th>
                                            <th class="text-end">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody-mensajes">
                                        <!-- Cargado por JS -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- VISTA: USUARIOS -->
                <!-- ========================================== -->
                <div id="view-usuarios" class="dashboard-view d-none">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="fw-bold mb-0">Usuarios Administradores</h5>
                                <button class="btn btn-primary" onclick="Usuarios.abrirModal()"><i class="bi bi-plus-lg me-2"></i>Nuevo Usuario</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Correo</th>
                                            <th class="text-end">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody-usuarios">
                                        <!-- Cargado por JS -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div> <!-- End P-4 -->
        </div> <!-- End Main Content -->
    </div> <!-- End Wrapper -->

    <!-- ========================================== -->
    <!-- MODALES GLOBALES -->
    <!-- ========================================== -->
    
    <!-- Modal Habilidades -->
    <div class="modal fade" id="modalHabilidad" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formHabilidad">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalHabilidadTitle">Nueva Habilidad</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="hab_id">
                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="nombre" id="hab_nombre" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ícono (Clase de Bootstrap, ej: bi-filetype-html)</label>
                            <input type="text" class="form-control" name="icono" id="hab_icono" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Clase Color (ej: text-danger)</label>
                            <input type="text" class="form-control" name="color_clase" id="hab_color" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Tecnologías -->
    <div class="modal fade" id="modalTecnologia" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formTecnologia">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTecnologiaTitle">Nueva Tecnología</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="tec_id">
                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="nombre" id="tec_nombre" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Porcentaje (0-100)</label>
                            <input type="number" class="form-control" name="porcentaje" id="tec_porcentaje" min="0" max="100" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Clase Color (ej: bg-primary)</label>
                            <input type="text" class="form-control" name="color_clase" id="tec_color" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Proyectos -->
    <div class="modal fade" id="modalProyecto" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formProyecto">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalProyectoTitle">Nuevo Proyecto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="pro_id">
                        <div class="mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" class="form-control" name="titulo" id="pro_titulo" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" name="descripcion" id="pro_descripcion" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Imagen (URL absoluta o relativa)</label>
                            <input type="text" class="form-control" name="imagen" id="pro_imagen" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">URL Demo</label>
                            <input type="url" class="form-control" name="url_demo" id="pro_demo">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">URL GitHub</label>
                            <input type="url" class="form-control" name="url_github" id="pro_github">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Usuarios -->
    <div class="modal fade" id="modalUsuario" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formUsuario">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalUsuarioTitle">Nuevo Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="usu_id">
                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="nombre" id="usu_nombre" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" name="email" id="usu_email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña <small class="text-muted" id="usu_pass_help">(Déjala vacía para no cambiarla al editar)</small></label>
                            <input type="password" class="form-control" name="password" id="usu_password">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Scripts Modulares -->
    <script src="assets/js/admin/app.js"></script>
    <script src="assets/js/admin/biografia.js"></script>
    <script src="assets/js/admin/habilidades.js"></script>
    <script src="assets/js/admin/tecnologias.js"></script>
    <script src="assets/js/admin/proyectos.js"></script>
    <script src="assets/js/admin/mensajes.js"></script>
    <script src="assets/js/admin/usuarios.js"></script>

    <!-- Init -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            App.init();
        });
    </script>
</body>
</html>
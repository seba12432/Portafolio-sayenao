/**
 * main.js
 * Lógica principal del Portafolio y Panel de Administración
 */
document.addEventListener("DOMContentLoaded", function() {
    
    // ==========================================
    // 1. Envío de Formulario AJAX (VISTA PÚBLICA)
    // ==========================================
    const contactForm = document.getElementById('contactForm');
    const formAlert = document.getElementById('formAlert');
    const btnSubmit = document.getElementById('btnSubmit');

    if (contactForm && formAlert && btnSubmit) {
        const btnText = btnSubmit.querySelector('span');
        const btnIcon = btnSubmit.querySelector('i');

        contactForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Evita recargar la página
            
            // Resetear la alerta
            formAlert.classList.add('d-none');
            formAlert.className = 'alert mt-3 d-none fw-medium';
            formAlert.innerHTML = '';

            // Recolectar datos del formulario
            const payload = {
                nombre: document.getElementById('contactNombre').value,
                correo: document.getElementById('contactCorreo').value,
                asunto: document.getElementById('contactAsunto').value,
                mensaje: document.getElementById('contactMensaje').value
            };

            // Estado de carga en el botón
            btnSubmit.disabled = true;
            btnText.textContent = 'Enviando...';
            btnIcon.className = 'spinner-border spinner-border-sm ms-2';

            // Petición AJAX (Fetch) al endpoint PHP
            fetch('enviar_mensaje.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(response => response.json())
            .then(data => {
                // Mostrar alerta de respuesta
                formAlert.classList.remove('d-none');
                if(data.success) {
                    formAlert.classList.add('alert-success');
                    formAlert.innerHTML = `<i class="bi bi-check-circle-fill me-2"></i> ${data.message}`;
                    contactForm.reset(); // Limpiar inputs si fue exitoso
                } else {
                    formAlert.classList.add('alert-warning');
                    formAlert.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-2"></i> ${data.message}`;
                }
            })
            .catch(error => {
                console.error('Error AJAX:', error);
                formAlert.classList.remove('d-none');
                formAlert.classList.add('alert-danger');
                formAlert.innerHTML = '<i class="bi bi-x-circle-fill me-2"></i> Hubo un problema de conexión. Inténtalo de nuevo.';
            })
            .finally(() => {
                // Restaurar el botón a su estado normal
                btnSubmit.disabled = false;
                btnText.textContent = 'Enviar Mensaje';
                btnIcon.className = 'bi bi-send ms-2';
            });
        });
    }

    // ==========================================
    // 2. Toggle de Sidebar Móvil (VISTA ADMIN)
    // ==========================================
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    if (sidebarToggle && sidebar && overlay) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.add('show');
            overlay.classList.add('show');
        });

        overlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    }

});

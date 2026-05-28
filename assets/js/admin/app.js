const App = {
    init() {
        this.bindNavigation();
        // Load default view if hash exists, else dashboard
        const hash = window.location.hash.substring(1) || 'dashboard';
        this.loadView(hash);
    },

    bindNavigation() {
        document.querySelectorAll('.sidebar-link[data-view]').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const view = e.currentTarget.getAttribute('data-view');
                this.loadView(view);
                // Cerrar sidebar en móviles si está abierto
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                if (sidebar && sidebar.classList.contains('show')) {
                    sidebar.classList.remove('show');
                    if (overlay) overlay.classList.remove('show');
                }
            });
        });
    },

    loadView(viewName) {
        // Update URL hash without jumping
        window.history.pushState(null, null, `#${viewName}`);

        // Ocultar todas las vistas
        document.querySelectorAll('.dashboard-view').forEach(v => v.classList.add('d-none'));
        
        // Desmarcar todos los enlaces
        document.querySelectorAll('.sidebar-link').forEach(l => l.classList.remove('active'));

        // Mostrar la vista seleccionada
        const targetView = document.getElementById(`view-${viewName}`);
        if (targetView) {
            targetView.classList.remove('d-none');
            // Marcar el enlace activo
            const activeLink = document.querySelector(`.sidebar-link[data-view="${viewName}"]`);
            if (activeLink) activeLink.classList.add('active');
            
            // Actualizar el título de la página
            const title = document.getElementById('topbar-title');
            if (title && activeLink) {
                title.innerText = activeLink.innerText.trim();
            }

            // Ejecutar la función de carga específica si existe
            this.triggerViewLoad(viewName);
        } else {
            // Fallback a dashboard
            this.loadView('dashboard');
        }
    },

    triggerViewLoad(viewName) {
        switch(viewName) {
            case 'biografia':
                if (typeof Biografia !== 'undefined') Biografia.load();
                break;
            case 'habilidades':
                if (typeof Habilidades !== 'undefined') Habilidades.load();
                break;
            case 'tecnologias':
                if (typeof Tecnologias !== 'undefined') Tecnologias.load();
                break;
            case 'proyectos':
                if (typeof Proyectos !== 'undefined') Proyectos.load();
                break;
            case 'mensajes':
                if (typeof Mensajes !== 'undefined') Mensajes.load();
                break;
            case 'usuarios':
                if (typeof Usuarios !== 'undefined') Usuarios.load();
                break;
        }
    },

    showAlert(message, type = 'success') {
        const alertBox = document.getElementById('globalAlert');
        const alertMsg = document.getElementById('globalAlertMsg');
        
        alertBox.className = `alert alert-${type} alert-dismissible fade show`;
        alertMsg.innerText = message;
        alertBox.classList.remove('d-none');
        
        // Auto hide after 3 seconds
        setTimeout(() => {
            alertBox.classList.add('d-none');
        }, 3000);
    }
};

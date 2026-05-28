const Usuarios = {
    modal: null,

    load() {
        this.fetchData();
        this.bindEvents();
    },

    fetchData() {
        fetch('api/usuarios.php')
            .then(res => res.json())
            .then(response => {
                if(response.success) {
                    this.renderTable(response.data);
                }
            })
            .catch(err => console.error(err));
    },

    renderTable(data) {
        const tbody = document.getElementById('tbody-usuarios');
        tbody.innerHTML = '';
        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-4">No hay usuarios registrados.</td></tr>`;
            return;
        }

        data.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${item.id}</td>
                <td class="fw-medium">${item.nombre}</td>
                <td><a href="mailto:${item.email}" class="text-decoration-none">${item.email}</a></td>
                <td class="text-end">
                    <button class="btn btn-sm btn-light text-primary border me-1" title="Editar" onclick='Usuarios.abrirModal(${JSON.stringify(item)})'><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-sm btn-light text-danger border" title="Eliminar" onclick="Usuarios.eliminar(${item.id})"><i class="bi bi-trash"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    },

    abrirModal(data = null) {
        if (!this.modal) {
            this.modal = new bootstrap.Modal(document.getElementById('modalUsuario'));
        }
        
        const form = document.getElementById('formUsuario');
        form.reset();
        
        const helpText = document.getElementById('usu_pass_help');
        
        if (data) {
            document.getElementById('modalUsuarioTitle').innerText = 'Editar Usuario';
            document.getElementById('usu_id').value = data.id;
            document.getElementById('usu_nombre').value = data.nombre;
            document.getElementById('usu_email').value = data.email;
            document.getElementById('usu_password').required = false;
            helpText.classList.remove('d-none');
        } else {
            document.getElementById('modalUsuarioTitle').innerText = 'Nuevo Usuario';
            document.getElementById('usu_id').value = '';
            document.getElementById('usu_password').required = true;
            helpText.classList.add('d-none');
        }
        
        this.modal.show();
    },

    bindEvents() {
        const form = document.getElementById('formUsuario');
        if (!form.hasAttribute('data-bound')) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());
                
                data.action = data.id ? 'update' : 'create';

                fetch('api/usuarios.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                })
                .then(res => res.json())
                .then(response => {
                    if (response.success) {
                        App.showAlert(response.message, 'success');
                        this.modal.hide();
                        this.fetchData();
                    } else {
                        App.showAlert(response.message, 'danger');
                    }
                })
                .catch(err => App.showAlert('Error de conexión', 'danger'));
            });
            form.setAttribute('data-bound', 'true');
        }
    },

    eliminar(id) {
        if (confirm('¿Estás seguro de eliminar este usuario?')) {
            fetch('api/usuarios.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete', id: id })
            })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    App.showAlert(response.message, 'success');
                    this.fetchData();
                } else {
                    App.showAlert(response.message, 'danger');
                }
            })
            .catch(err => App.showAlert('Error de conexión', 'danger'));
        }
    }
};

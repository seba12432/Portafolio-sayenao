const Habilidades = {
    modal: null,

    load() {
        this.fetchData();
        this.bindEvents();
    },

    fetchData() {
        fetch('api/habilidades.php')
            .then(res => res.json())
            .then(response => {
                if(response.success) {
                    this.renderTable(response.data);
                }
            })
            .catch(err => console.error(err));
    },

    renderTable(data) {
        const tbody = document.getElementById('tbody-habilidades');
        tbody.innerHTML = '';
        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-4">No hay habilidades registradas.</td></tr>`;
            return;
        }

        data.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${item.id}</td>
                <td><i class="bi ${item.icono} fs-4 ${item.color_clase}"></i></td>
                <td class="fw-medium">${item.nombre}</td>
                <td><span class="badge bg-light text-dark border">${item.color_clase}</span></td>
                <td class="text-end">
                    <button class="btn btn-sm btn-light text-primary border me-1" title="Editar" onclick='Habilidades.abrirModal(${JSON.stringify(item)})'><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-sm btn-light text-danger border" title="Eliminar" onclick="Habilidades.eliminar(${item.id})"><i class="bi bi-trash"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    },

    abrirModal(data = null) {
        if (!this.modal) {
            this.modal = new bootstrap.Modal(document.getElementById('modalHabilidad'));
        }
        
        const form = document.getElementById('formHabilidad');
        form.reset();
        
        if (data) {
            document.getElementById('modalHabilidadTitle').innerText = 'Editar Habilidad';
            document.getElementById('hab_id').value = data.id;
            document.getElementById('hab_nombre').value = data.nombre;
            document.getElementById('hab_icono').value = data.icono;
            document.getElementById('hab_color').value = data.color_clase;
        } else {
            document.getElementById('modalHabilidadTitle').innerText = 'Nueva Habilidad';
            document.getElementById('hab_id').value = '';
        }
        
        this.modal.show();
    },

    bindEvents() {
        const form = document.getElementById('formHabilidad');
        if (!form.hasAttribute('data-bound')) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());
                
                data.action = data.id ? 'update' : 'create';

                fetch('api/habilidades.php', {
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
        if (confirm('¿Estás seguro de eliminar esta habilidad?')) {
            fetch('api/habilidades.php', {
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

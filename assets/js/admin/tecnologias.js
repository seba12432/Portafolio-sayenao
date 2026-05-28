const Tecnologias = {
    modal: null,

    load() {
        this.fetchData();
        this.bindEvents();
    },

    fetchData() {
        fetch('api/tecnologias.php')
            .then(res => res.json())
            .then(response => {
                if(response.success) {
                    this.renderTable(response.data);
                }
            })
            .catch(err => console.error(err));
    },

    renderTable(data) {
        const tbody = document.getElementById('tbody-tecnologias');
        tbody.innerHTML = '';
        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-4">No hay tecnologías registradas.</td></tr>`;
            return;
        }

        data.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${item.id}</td>
                <td class="fw-medium">${item.nombre}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <span class="me-2 small">${item.porcentaje}%</span>
                        <div class="progress flex-grow-1" style="height: 6px;">
                            <div class="progress-bar ${item.color_clase}" role="progressbar" style="width: ${item.porcentaje}%"></div>
                        </div>
                    </div>
                </td>
                <td><span class="badge bg-light text-dark border">${item.color_clase}</span></td>
                <td class="text-end">
                    <button class="btn btn-sm btn-light text-primary border me-1" title="Editar" onclick='Tecnologias.abrirModal(${JSON.stringify(item)})'><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-sm btn-light text-danger border" title="Eliminar" onclick="Tecnologias.eliminar(${item.id})"><i class="bi bi-trash"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    },

    abrirModal(data = null) {
        if (!this.modal) {
            this.modal = new bootstrap.Modal(document.getElementById('modalTecnologia'));
        }
        
        const form = document.getElementById('formTecnologia');
        form.reset();
        
        if (data) {
            document.getElementById('modalTecnologiaTitle').innerText = 'Editar Tecnología';
            document.getElementById('tec_id').value = data.id;
            document.getElementById('tec_nombre').value = data.nombre;
            document.getElementById('tec_porcentaje').value = data.porcentaje;
            document.getElementById('tec_color').value = data.color_clase;
        } else {
            document.getElementById('modalTecnologiaTitle').innerText = 'Nueva Tecnología';
            document.getElementById('tec_id').value = '';
        }
        
        this.modal.show();
    },

    bindEvents() {
        const form = document.getElementById('formTecnologia');
        if (!form.hasAttribute('data-bound')) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());
                
                data.action = data.id ? 'update' : 'create';

                fetch('api/tecnologias.php', {
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
        if (confirm('¿Estás seguro de eliminar esta tecnología?')) {
            fetch('api/tecnologias.php', {
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

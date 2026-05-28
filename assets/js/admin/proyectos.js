const Proyectos = {
    modal: null,

    load() {
        this.fetchData();
        this.bindEvents();
    },

    fetchData() {
        fetch('api/proyectos.php')
            .then(res => res.json())
            .then(response => {
                if(response.success) {
                    this.renderTable(response.data);
                }
            })
            .catch(err => console.error(err));
    },

    renderTable(data) {
        const tbody = document.getElementById('tbody-proyectos');
        tbody.innerHTML = '';
        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-4">No hay proyectos registrados.</td></tr>`;
            return;
        }

        data.forEach(item => {
            // Si la imagen no parece una URL, mostramos el ícono
            const imgHtml = (item.imagen.startsWith('http') || item.imagen.includes('/')) 
                ? `<img src="${item.imagen}" alt="Proyecto" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">`
                : `<i class="bi ${item.imagen} fs-2 text-primary"></i>`;

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${imgHtml}</td>
                <td class="fw-medium">${item.titulo}</td>
                <td><a href="${item.url_demo}" target="_blank" class="btn btn-sm btn-link text-decoration-none">Demo <i class="bi bi-box-arrow-up-right"></i></a></td>
                <td><a href="${item.url_github}" target="_blank" class="btn btn-sm btn-link text-decoration-none text-dark"><i class="bi bi-github"></i> GitHub</a></td>
                <td class="text-end">
                    <button class="btn btn-sm btn-light text-primary border me-1" title="Editar" onclick='Proyectos.abrirModal(${JSON.stringify(item)})'><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-sm btn-light text-danger border" title="Eliminar" onclick="Proyectos.eliminar(${item.id})"><i class="bi bi-trash"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    },

    abrirModal(data = null) {
        if (!this.modal) {
            this.modal = new bootstrap.Modal(document.getElementById('modalProyecto'));
        }
        
        const form = document.getElementById('formProyecto');
        form.reset();
        
        if (data) {
            document.getElementById('modalProyectoTitle').innerText = 'Editar Proyecto';
            document.getElementById('pro_id').value = data.id;
            document.getElementById('pro_titulo').value = data.titulo;
            document.getElementById('pro_descripcion').value = data.descripcion;
            document.getElementById('pro_imagen').value = data.imagen;
            document.getElementById('pro_demo').value = data.url_demo;
            document.getElementById('pro_github').value = data.url_github;
        } else {
            document.getElementById('modalProyectoTitle').innerText = 'Nuevo Proyecto';
            document.getElementById('pro_id').value = '';
        }
        
        this.modal.show();
    },

    bindEvents() {
        const form = document.getElementById('formProyecto');
        if (!form.hasAttribute('data-bound')) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());
                
                data.action = data.id ? 'update' : 'create';

                fetch('api/proyectos.php', {
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
        if (confirm('¿Estás seguro de eliminar este proyecto?')) {
            fetch('api/proyectos.php', {
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

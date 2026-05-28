const Mensajes = {
    load() {
        this.fetchData();
    },

    fetchData() {
        fetch('api/mensajes.php')
            .then(res => res.json())
            .then(response => {
                if(response.success) {
                    this.renderTable(response.data);
                }
            })
            .catch(err => console.error(err));
    },

    renderTable(data) {
        const tbody = document.getElementById('tbody-mensajes');
        tbody.innerHTML = '';
        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-4">No hay mensajes.</td></tr>`;
            return;
        }

        data.forEach(item => {
            const date = new Date(item.fecha);
            const formattedDate = `${date.getDate().toString().padStart(2, '0')}/${(date.getMonth()+1).toString().padStart(2, '0')}/${date.getFullYear()} ${date.getHours().toString().padStart(2,'0')}:${date.getMinutes().toString().padStart(2,'0')}`;
            
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="text-muted small">${formattedDate}</td>
                <td>
                    <div class="fw-bold">${item.nombre}</div>
                    <a href="mailto:${item.correo}" class="small text-decoration-none">${item.correo}</a>
                </td>
                <td class="fw-medium">${item.asunto}</td>
                <td><button class="btn btn-sm btn-link text-decoration-none" onclick='Mensajes.verMensaje(${JSON.stringify(item.mensaje)})'>Ver Mensaje</button></td>
                <td class="text-end">
                    <button class="btn btn-sm btn-light text-danger border" title="Eliminar" onclick="Mensajes.eliminar(${item.id})"><i class="bi bi-trash"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    },

    verMensaje(mensaje) {
        // Podríamos usar un modal, pero un alert sirve para algo rápido, o un modal si lo agregamos luego.
        alert(mensaje);
    },

    eliminar(id) {
        if (confirm('¿Estás seguro de eliminar este mensaje?')) {
            fetch('api/mensajes.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete', id: id })
            })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    App.showAlert(response.message, 'success');
                    this.fetchData();
                    // Refrescar los contadores del dashboard
                    if (document.getElementById('view-dashboard')) {
                        // Idealmente podríamos hacer un fetch al dashboard para actualizar el conteo
                        // Por simplicidad en este paso recargamos, o solo mostramos el mensaje.
                    }
                } else {
                    App.showAlert(response.message, 'danger');
                }
            })
            .catch(err => App.showAlert('Error de conexión', 'danger'));
        }
    }
};

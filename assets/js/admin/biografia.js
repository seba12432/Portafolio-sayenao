const Biografia = {
    load() {
        fetch('api/biografia.php')
            .then(res => res.json())
            .then(response => {
                if(response.success && response.data) {
                    const data = response.data;
                    document.getElementById('bio_saludo').value = data.saludo || '';
                    document.getElementById('bio_nombre').value = data.nombre_completo || '';
                    document.getElementById('bio_titulo').value = data.titulo || '';
                    document.getElementById('bio_descripcion').value = data.descripcion || '';
                    document.getElementById('bio_cv').value = data.cv_url || '';
                    document.getElementById('bio_email').value = data.email_contacto || '';
                    document.getElementById('bio_github').value = data.github_url || '';
                    document.getElementById('bio_linkedin').value = data.linkedin_url || '';
                }
            })
            .catch(err => console.error(err));
            
        this.bindEvents();
    },

    bindEvents() {
        const form = document.getElementById('formBiografia');
        // Remover event listeners previos clonando el nodo si es necesario, 
        // pero como 'load' puede llamarse múltiples veces, usamos una variable para saber si ya se binded.
        if (!form.hasAttribute('data-bound')) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());

                fetch('api/biografia.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(res => res.json())
                .then(response => {
                    if (response.success) {
                        App.showAlert(response.message, 'success');
                    } else {
                        App.showAlert(response.message, 'danger');
                    }
                })
                .catch(err => App.showAlert('Error al conectar con el servidor', 'danger'));
            });
            form.setAttribute('data-bound', 'true');
        }
    }
};

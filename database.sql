-- Crear la base de datos (Si estás en XAMPP)
USE portafolio_db;

CREATE TABLE usuarios_admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE biografia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    saludo VARCHAR(50),
    nombre_completo VARCHAR(100) NOT NULL,
    titulo VARCHAR(100),
    descripcion TEXT,
    cv_url VARCHAR(255),
    github_url VARCHAR(255),
    linkedin_url VARCHAR(255),
    email_contacto VARCHAR(100)
);

CREATE TABLE habilidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    icono VARCHAR(50),      -- Ej: bi-filetype-html
    color_clase VARCHAR(50) -- Ej: text-danger
);

CREATE TABLE tecnologias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    porcentaje INT,         -- Número de 0 a 100
    color_clase VARCHAR(50) -- Ej: bg-primary
);

CREATE TABLE proyectos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    descripcion TEXT,
    imagen VARCHAR(255),    -- URL de la imagen
    url_demo VARCHAR(255),
    url_github VARCHAR(255)
);

CREATE TABLE mensajes_contacto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL,
    asunto VARCHAR(150),
    mensaje TEXT NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertar el usuario administrador por defecto
-- Correo: admin@correo.com | Contraseña: password
INSERT INTO usuarios_admin (nombre, email, password) 
VALUES ('Administrador', 'admin@correo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insertar un registro inicial vacío para la biografía (Obligatorio para que no falle el UPDATE)
INSERT INTO biografia (id, saludo, nombre_completo, titulo, descripcion, cv_url, github_url, linkedin_url, email_contacto) 
VALUES (1, 'Hola, Soy', 'Sebastian Ayenao', 'Desarrollador Web', '#', '#', '#', '#', 'admin@correo.com');

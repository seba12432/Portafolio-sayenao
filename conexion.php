<?php
/**
 * Archivo de conexión a la base de datos usando PDO
 */

$host = 'localhost';
$dbname = 'portafolio_db';
$username = 'root';
$password = '';

try {
    // Data Source Name
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

    // Opciones para PDO
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Manejo de errores por excepciones
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Resultados como arrays asociativos
        PDO::ATTR_EMULATE_PREPARES => false,                  // Desactiva la emulación para mayor seguridad
    ];

    // Crear la instancia de PDO
    $pdo = new PDO($dsn, $username, $password, $options);
   // echo "¡Conexión exitosa a la base de datos!";
} catch (PDOException $e) {
    // Si hay un error, lo atrapamos y terminamos la ejecución
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
?>
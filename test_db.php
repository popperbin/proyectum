<?php
$host = "localhost";
$port = "3307";
$dbname = "proyectum_mvp";
$user = "root";
$pass = "";

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass);
    echo "✅ Conexión exitosa a la BD $dbname en el puerto $port";
} catch (PDOException $e) {
    die("❌ Error de conexión: " . $e->getMessage());
}

<?php
require_once __DIR__ . "/config/db.php";

$db = Database::getInstance();
$hash = password_hash("admin123", PASSWORD_DEFAULT);

$db->execute(
    "UPDATE usuarios SET password = ? WHERE email = ?",
    [$hash, "admin@proyectum.com"]
);

echo "Contraseña del admin reseteada a admin123 ✅";

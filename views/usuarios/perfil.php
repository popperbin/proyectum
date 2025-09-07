<?php

// Validar que haya sesión activa
if (!isset($_SESSION['usuario'])) {
    header("Location: ../usuarios/login.php");
    exit();
}

$usuario = $_SESSION['usuario'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil de Usuario</title>
    <link rel="stylesheet" href="../assets/css/styles.css"> <!-- Opcional -->
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f4f4f9; }
        .perfil-container { max-width: 800px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0px 2px 8px rgba(0,0,0,0.1); }
        h2 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 10px; border-bottom: 1px solid #ddd; }
        td:first-child { font-weight: bold; width: 200px; }
        .acciones { text-align: center; margin-top: 20px; }
        .btn { padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; }
        .btn-editar { background-color: #007bff; color: #fff; }
        .btn-logout { background-color: #dc3545; color: #fff; }
    </style>
</head>
<body>

<div class="perfil-container">
    <h2>Perfil de Usuario</h2>
    <table>
        <tr>
            <td>ID</td>
            <td><?= htmlspecialchars($usuario['id']) ?></td>
        </tr>
        <tr>
            <td>Nombres</td>
            <td><?= htmlspecialchars($usuario['nombres']) ?></td>
        </tr>
        <tr>
            <td>Apellidos</td>
            <td><?= htmlspecialchars($usuario['apellidos']) ?></td>
        </tr>
        <tr>
            <td>Cédula</td>
            <td><?= htmlspecialchars($usuario['cedula']) ?></td>
        </tr>
        <tr>
            <td>Email</td>
            <td><?= htmlspecialchars($usuario['email']) ?></td>
        </tr>
        <tr>
            <td>Dirección</td>
            <td><?= htmlspecialchars($usuario['direccion'] ?? 'No registrada') ?></td>
        </tr>
        <tr>
            <td>Celular</td>
            <td><?= htmlspecialchars($usuario['celular'] ?? 'No registrado') ?></td>
        </tr>
        <tr>
            <td>Cargo</td>
            <td><?= htmlspecialchars($usuario['cargo'] ?? 'No asignado') ?></td>
        </tr>
        <tr>
            <td>Rol</td>
            <td><?= htmlspecialchars($usuario['rol']) ?></td>
        </tr>
        <tr>
            <td>Estado</td>
            <td><?= htmlspecialchars($usuario['estado']) ?></td>
        </tr>
        <tr>
            <td>Fecha de creación</td>
            <td><?= htmlspecialchars($usuario['fecha_creacion']) ?></td>

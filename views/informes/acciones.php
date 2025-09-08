<?php
require_once __DIR__ . "/../../controllers/InformeController.php";
$controller = new InformeController();

// --- CREAR INFORME ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_GET['accion'] ?? '') === 'crear') {
    $result = $controller->crear($_POST);
    ?>
    <div class="container mt-4">
        <?php if (!empty($result['ok'])): ?>
            <div class="alert alert-success text-center">
                ✅ Informe generado correctamente. Descargando PDF…
            </div>
            <script>
                // Abrir descarga SIN pasar por router.php (directo al controlador de acciones)
                window.open("router.php?page=informes/acciones&accion=descargar&id=<?= $result['informe_id'] ?>", "_blank");

                // Redirigir al listado
                setTimeout(() => {
                    window.location.href = "<?= $result['url'] ?>";
                }, 2000);
            </script>
        <?php else: ?>
            <div class="alert alert-danger text-center">
                ❌ Error al generar informe: <?= $result['error'] ?? 'Error desconocido' ?>
            </div>
        <?php endif; ?>
    </div>
    <?php
    exit;
}

// --- ELIMINAR INFORME ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && ($_GET['accion'] ?? '') === 'eliminar') {
    $result = $controller->eliminar($_GET['id'], $_GET['proyecto_id']);
    ?>
    <div class="container mt-4">
        <?php if (!empty($result['ok'])): ?>
            <div class="alert alert-success text-center">
                🗑️ Informe eliminado correctamente. Redirigiendo…
            </div>
            <script>
                setTimeout(() => {
                    window.location.href = "<?= $result['url'] ?>";
                }, 1500);
            </script>
        <?php else: ?>
            <div class="alert alert-danger text-center">
                ❌ Error al eliminar el informe.
            </div>
        <?php endif; ?>
    </div>
    <?php
    exit;
}

// --- DESCARGAR INFORME ---
if (($_GET['accion'] ?? '') === 'descargar' && isset($_GET['id'])) {
    // 👇 Esto corta cualquier output de router/header antes de mandar PDF
    if (ob_get_length()) {
        ob_end_clean();
    }
    $controller->descargar($_GET['id']);
    exit;
}

// --- SI LLEGASTE AQUÍ SIN ACCIÓN ---
?>
<div class="container mt-4">
    <div class="alert alert-warning text-center">
        ⚠️ Acción no reconocida en <strong>acciones.php</strong>
    </div>
</div>
<?php
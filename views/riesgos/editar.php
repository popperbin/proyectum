<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">
            <!-- Header -->
            <div class="d-flex align-items-center mb-4">
                <div class="me-3">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                        <i class="fas fa-edit text-primary fs-4"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <h2 class="mb-1">Editar Riesgo del Proyecto</h2>
                    <p class="text-muted mb-0">Actualiza la información de este riesgo identificado</p>
                </div>
                <div>
                    <!-- Indicador de estado actual -->
                    <div class="d-flex gap-2">
                        <?php
                        $impactoBadge = '';
                        $probBadge = '';
                        switch(strtolower($riesgo['impacto'])) {
                            case 'bajo': $impactoBadge = 'bg-success'; break;
                            case 'medio': $impactoBadge = 'bg-warning'; break;
                            case 'alto': $impactoBadge = 'bg-danger'; break;
                        }
                        switch(strtolower($riesgo['probabilidad'])) {
                            case 'baja': $probBadge = 'bg-success'; break;
                            case 'media': $probBadge = 'bg-warning'; break;
                            case 'alta': $probBadge = 'bg-danger'; break;
                        }
                        ?>
                        <span class="badge <?php echo $impactoBadge; ?> px-2 py-1">
                            Impacto: <?php echo $riesgo['impacto']; ?>
                        </span>
                        <span class="badge <?php echo $probBadge; ?> px-2 py-1">
                            Prob: <?php echo $riesgo['probabilidad']; ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Formulario -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" id="editarRiesgoForm">
                        <input type="hidden" name="proyecto_id" value="<?php echo $riesgo['proyecto_id']; ?>">
                        
                        <!-- Vista previa del riesgo actual -->
                        <div class="alert alert-light border mb-4">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h6 class="text-muted mb-2">
                                        <i class="fas fa-eye me-2"></i>Riesgo actual:
                                    </h6>
                                    <p class="mb-0 small text-truncate">
                                        "<?php echo htmlspecialchars(substr($riesgo['descripcion'], 0, 100)); ?>..."
                                    </p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        Última actualización
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Descripción del Riesgo -->
                        <div class="mb-4">
                            <label for="descripcion" class="form-label fw-semibold">
                                <i class="fas fa-file-alt text-primary me-2"></i>
                                Descripción del Riesgo <span class="text-danger">*</span>
                            </label>
                            <textarea 
                                class="form-control form-control-lg" 
                                id="descripcion" 
                                name="descripcion" 
                                rows="4" 
                                placeholder="Describe detalladamente el riesgo identificado..."
                                required><?php echo htmlspecialchars($riesgo['descripcion']); ?></textarea>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Actualiza la descripción si es necesario para mayor claridad
                            </div>
                            <div class="text-end mt-1">
                                <small class="text-muted">
                                    <span id="desc-count"><?php echo strlen($riesgo['descripcion']); ?></span> caracteres
                                </small>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Impacto -->
                            <div class="col-md-6 mb-4">
                                <label for="impacto" class="form-label fw-semibold">
                                    <i class="fas fa-impact text-warning me-2"></i>
                                    Nivel de Impacto <span class="text-danger">*</span>
                                </label>
                                <select class="form-select form-select-lg" id="impacto" name="impacto" required>
                                    <option value="Bajo" <?= ($riesgo['impacto']=="Bajo") ? "selected" : "" ?> data-color="success">
                                        🟢 Bajo - Impacto mínimo en el proyecto
                                    </option>
                                    <option value="Medio" <?= ($riesgo['impacto']=="Medio") ? "selected" : "" ?> data-color="warning">
                                        🟡 Medio - Impacto moderado en el proyecto
                                    </option>
                                    <option value="Alto" <?= ($riesgo['impacto']=="Alto") ? "selected" : "" ?> data-color="danger">
                                        🔴 Alto - Impacto significativo en el proyecto
                                    </option>
                                </select>
                                <div class="form-text">
                                    <span id="impacto-anterior" class="badge badge-sm bg-secondary">
                                        Actual: <?php echo $riesgo['impacto']; ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Probabilidad -->
                            <div class="col-md-6 mb-4">
                                <label for="probabilidad" class="form-label fw-semibold">
                                    <i class="fas fa-percentage text-info me-2"></i>
                                    Probabilidad de Ocurrencia <span class="text-danger">*</span>
                                </label>
                                <select class="form-select form-select-lg" id="probabilidad" name="probabilidad" required>
                                    <option value="Baja" <?= ($riesgo['probabilidad']=="Baja") ? "selected" : "" ?> data-color="success">
                                        🟢 Baja - Poco probable que ocurra
                                    </option>
                                    <option value="Media" <?= ($riesgo['probabilidad']=="Media") ? "selected" : "" ?> data-color="warning">
                                        🟡 Media - Posibilidad moderada
                                    </option>
                                    <option value="Alta" <?= ($riesgo['probabilidad']=="Alta") ? "selected" : "" ?> data-color="danger">
                                        🔴 Alta - Muy probable que ocurra
                                    </option>
                                </select>
                                <div class="form-text">
                                    <span id="prob-anterior" class="badge badge-sm bg-secondary">
                                        Actual: <?php echo $riesgo['probabilidad']; ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Medidas de Mitigación -->
                        <div class="mb-4">
                            <label for="medidas_mitigacion" class="form-label fw-semibold">
                                <i class="fas fa-shield-alt text-success me-2"></i>
                                Medidas de Mitigación <span class="text-danger">*</span>
                            </label>
                            <textarea 
                                class="form-control form-control-lg" 
                                id="medidas_mitigacion" 
                                name="medidas_mitigacion" 
                                rows="4" 
                                placeholder="Describe las acciones preventivas o correctivas para minimizar este riesgo..."
                                required><?php echo htmlspecialchars($riesgo['medidas_mitigacion']); ?></textarea>
                            <div class="form-text">
                                <i class="fas fa-lightbulb me-1"></i>
                                Actualiza o mejora las estrategias de mitigación según nuevos aprendizajes
                            </div>
                            <div class="text-end mt-1">
                                <small class="text-muted">
                                    <span id="mit-count"><?php echo strlen($riesgo['medidas_mitigacion']); ?></span> caracteres
                                </small>
                            </div>
                        </div>

                        <!-- Indicador de cambios -->
                        <div id="cambios-detectados" class="alert alert-info d-none mb-4">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <div>
                                    <strong>Cambios detectados</strong><br>
                                    <small id="lista-cambios"></small>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="row">
                            <div class="col-md-6">
                                <a href="/proyectum/controllers/RiesgoController.php?accion=listar&id_proyecto=<?= $riesgo['proyecto_id'] ?>" 
                                   class="btn btn-light btn-lg w-100 border">
                                    <i class="fas fa-arrow-left me-2"></i>Cancelar y Volver
                                </a>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary btn-lg w-100" id="updateBtn">
                                    <i class="fas fa-save me-2"></i>Actualizar Riesgo
                                </button>
                            </div>
                        </div>

                        <!-- Confirmación de cambios -->
                        <div class="mt-3 text-center">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Los cambios se guardarán inmediatamente al hacer clic en "Actualizar"
                            </small>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Panel de ayuda -->
            <div class="card border-0 bg-light mt-4">
                <div class="card-body">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-lightbulb me-2"></i>Consejos para Actualizar Riesgos
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">¿Cuándo actualizar?</h6>
                            <ul class="small text-muted mb-3">
                                <li>Cuando la situación del proyecto cambia</li>
                                <li>Si se obtiene nueva información</li>
                                <li>Después de implementar mitigaciones</li>
                                <li>En revisiones periódicas del proyecto</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Mejores prácticas:</h6>
                            <ul class="small text-muted mb-0">
                                <li>Sé específico en los cambios</li>
                                <li>Documenta el motivo del cambio</li>
                                <li>Actualiza medidas según experiencia</li>
                                <li>Comunica cambios importantes al equipo</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.form-control:focus, .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
}

.form-control-lg, .form-select-lg {
    font-size: 1rem;
    padding: 0.75rem 1rem;
}

.card {
    transition: all 0.3s ease;
}

.btn-lg {
    padding: 0.75rem 1.5rem;
    font-size: 1.1rem;
}

.form-text {
    font-size: 0.875rem;
    margin-top: 0.5rem;
}

.badge-sm {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
}

/* Animaciones para el botón */
#updateBtn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3);
}

/* Estilos para las opciones de select */
option[data-color="success"] {
    background-color: rgba(25, 135, 84, 0.1);
}
option[data-color="warning"] {
    background-color: rgba(255, 193, 7, 0.1);
}
option[data-color="danger"] {
    background-color: rgba(220, 53, 69, 0.1);
}

/* Indicador de cambios */
.alert-info {
    border-left: 4px solid #0dcaf0;
}

/* Responsive */
@media (max-width: 768px) {
    .btn-lg {
        font-size: 1rem;
        padding: 0.65rem 1.25rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editarRiesgoForm');
    const updateBtn = document.getElementById('updateBtn');
    const cambiosAlert = document.getElementById('cambios-detectados');
    const listaCambios = document.getElementById('lista-cambios');
    
    // Valores originales
    const valoresOriginales = {
        descripcion: `<?php echo addslashes($riesgo['descripcion']); ?>`,
        impacto: '<?php echo $riesgo['impacto']; ?>',
        probabilidad: '<?php echo $riesgo['probabilidad']; ?>',
        medidas_mitigacion: `<?php echo addslashes($riesgo['medidas_mitigacion']); ?>`
    };
    
    // Contadores de caracteres
    const descTextarea = document.getElementById('descripcion');
    const mitTextarea = document.getElementById('medidas_mitigacion');
    const descCount = document.getElementById('desc-count');
    const mitCount = document.getElementById('mit-count');
    
    descTextarea.addEventListener('input', function() {
        descCount.textContent = this.value.length;
    });
    
    mitTextarea.addEventListener('input', function() {
        mitCount.textContent = this.value.length;
    });
    
    // Detectar cambios
    function detectarCambios() {
        const valoresActuales = {
            descripcion: document.getElementById('descripcion').value,
            impacto: document.getElementById('impacto').value,
            probabilidad: document.getElementById('probabilidad').value,
            medidas_mitigacion: document.getElementById('medidas_mitigacion').value
        };
        
        const cambios = [];
        
        // Comparar cada campo
        if (valoresActuales.descripcion !== valoresOriginales.descripcion) {
            cambios.push('Descripción');
        }
        if (valoresActuales.impacto !== valoresOriginales.impacto) {
            cambios.push(`Impacto (${valoresOriginales.impacto} → ${valoresActuales.impacto})`);
        }
        if (valoresActuales.probabilidad !== valoresOriginales.probabilidad) {
            cambios.push(`Probabilidad (${valoresOriginales.probabilidad} → ${valoresActuales.probabilidad})`);
        }
        if (valoresActuales.medidas_mitigacion !== valoresOriginales.medidas_mitigacion) {
            cambios.push('Medidas de mitigación');
        }
        
        // Mostrar u ocultar alerta de cambios
        if (cambios.length > 0) {
            cambiosAlert.classList.remove('d-none');
            listaCambios.textContent = cambios.join(', ');
            updateBtn.classList.remove('btn-secondary');
            updateBtn.classList.add('btn-primary');
            updateBtn.disabled = false;
        } else {
            cambiosAlert.classList.add('d-none');
            updateBtn.classList.remove('btn-primary');
            updateBtn.classList.add('btn-secondary');
            updateBtn.disabled = true;
        }
        
        return cambios.length > 0;
    }
    
    // Escuchar cambios en todos los campos
    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('input', detectarCambios);
        input.addEventListener('change', detectarCambios);
    });
    
    // Validación inicial
    detectarCambios();
    
    // Confirmación antes de enviar
    form.addEventListener('submit', function(e) {
        if (!detectarCambios()) {
            e.preventDefault();
            alert('No se han detectado cambios para actualizar.');
            return false;
        }
        
        // Confirmar actualización
        if (!confirm('¿Estás seguro de que deseas actualizar este riesgo?\n\nLos cambios se guardarán permanentemente.')) {
            e.preventDefault();
            return false;
        }
        
        // Cambiar texto del botón durante envío
        updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Actualizando...';
        updateBtn.disabled = true;
    });
    
    // Advertencia al salir con cambios sin guardar
    window.addEventListener('beforeunload', function(e) {
        if (detectarCambios()) {
            e.preventDefault();
            e.returnValue = '¿Estás seguro de que quieres salir? Los cambios no guardados se perderán.';
        }
    });
    
    // Remover advertencia al enviar el formulario
    form.addEventListener('submit', function() {
        window.removeEventListener('beforeunload', arguments.callee);
    });
});
</script>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">
            <!-- Header -->
            <div class="d-flex align-items-center mb-4">
                <div class="me-3">
                    <div class="bg-danger bg-opacity-10 p-3 rounded-circle">
                        <i class="fas fa-plus-circle text-danger fs-4"></i>
                    </div>
                </div>
                <div>
                    <h2 class="mb-1">Registrar Nuevo Riesgo</h2>
                    <p class="text-muted mb-0">Identifica y documenta un nuevo riesgo del proyecto</p>
                </div>
            </div>

            <!-- Formulario -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="RiesgoController.php?accion=crear&id_proyecto=<?= $idProyecto ?>" id="riesgoForm">
                        <input type="hidden" name="proyecto_id" value="<?php echo $_GET['id_proyecto']; ?>">
                        
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
                                required></textarea>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Sé específico sobre qué puede salir mal y bajo qué circunstancias
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
                                    <option value="">-- Selecciona el impacto --</option>
                                    <option value="Bajo" data-color="success">
                                        🟢 Bajo - Impacto mínimo en el proyecto
                                    </option>
                                    <option value="Medio" data-color="warning">
                                        🟡 Medio - Impacto moderado en el proyecto
                                    </option>
                                    <option value="Alto" data-color="danger">
                                        🔴 Alto - Impacto significativo en el proyecto
                                    </option>
                                </select>
                                <div class="form-text">
                                    Evalúa qué tan grave sería si este riesgo se materializa
                                </div>
                            </div>

                            <!-- Probabilidad -->
                            <div class="col-md-6 mb-4">
                                <label for="probabilidad" class="form-label fw-semibold">
                                    <i class="fas fa-percentage text-info me-2"></i>
                                    Probabilidad de Ocurrencia <span class="text-danger">*</span>
                                </label>
                                <select class="form-select form-select-lg" id="probabilidad" name="probabilidad" required>
                                    <option value="">-- Selecciona la probabilidad --</option>
                                    <option value="Baja" data-color="success">
                                        🟢 Baja - Poco probable que ocurra
                                    </option>
                                    <option value="Media" data-color="warning">
                                        🟡 Media - Posibilidad moderada
                                    </option>
                                    <option value="Alta" data-color="danger">
                                        🔴 Alta - Muy probable que ocurra
                                    </option>
                                </select>
                                <div class="form-text">
                                    ¿Qué tan probable es que este riesgo se materialice?
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
                                required></textarea>
                            <div class="form-text">
                                <i class="fas fa-lightbulb me-1"></i>
                                Incluye estrategias de prevención, contingencia y recuperación
                            </div>
                        </div>

                        <!-- Responsable -->
                        <div class="mb-5">
                            <label for="responsable_id" class="form-label fw-semibold">
                                <i class="fas fa-user-shield text-primary me-2"></i>
                                Responsable del Riesgo <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-lg" id="responsable_id" name="responsable_id" required>
                                <option value="">-- Selecciona un responsable --</option>
                                <?php foreach ($usuarios as $u): ?>
                                    <option value="<?= $u['id'] ?>">
                                        👤 <?= htmlspecialchars($u['nombres'] . " " . $u['apellidos']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">
                                Persona encargada de monitorear y gestionar este riesgo
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="row">
                            <div class="col-md-6">
                                <a href="../../listar.php" class="btn btn-light btn-lg w-100 border">
                                    <i class="fas fa-arrow-left me-2"></i>Cancelar y Volver
                                </a>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-danger btn-lg w-100" id="submitBtn">
                                    <i class="fas fa-save me-2"></i>Guardar Riesgo
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Panel de ayuda -->
            <div class="card border-0 bg-light mt-4">
                <div class="card-body">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-question-circle me-2"></i>Guía para Identificar Riesgos
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Tipos de Riesgos Comunes:</h6>
                            <ul class="small text-muted mb-3">
                                <li>Técnicos (tecnología, integración)</li>
                                <li>Recursos (personal, presupuesto)</li>
                                <li>Cronograma (retrasos, dependencias)</li>
                                <li>Externos (proveedores, regulaciones)</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Ejemplos de Mitigación:</h6>
                            <ul class="small text-muted mb-0">
                                <li>Planes de contingencia</li>
                                <li>Respaldos y alternativas</li>
                                <li>Monitoreo continuo</li>
                                <li>Comunicación temprana</li>
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
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.15);
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

/* Animación para el botón de submit */
#submitBtn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
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

/* Responsive adjustments */
@media (max-width: 768px) {
    .btn-lg {
        font-size: 1rem;
        padding: 0.65rem 1.25rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validación en tiempo real
    const form = document.getElementById('riesgoForm');
    const submitBtn = document.getElementById('submitBtn');
    
    // Función para validar formulario
    function validateForm() {
        const descripcion = document.getElementById('descripcion').value.trim();
        const impacto = document.getElementById('impacto').value;
        const probabilidad = document.getElementById('probabilidad').value;
        const medidas = document.getElementById('medidas_mitigacion').value.trim();
        const responsable = document.getElementById('responsable_id').value;
        
        const isValid = descripcion && impacto && probabilidad && medidas && responsable;
        
        submitBtn.disabled = !isValid;
        submitBtn.classList.toggle('btn-danger', isValid);
        submitBtn.classList.toggle('btn-secondary', !isValid);
        
        return isValid;
    }
    
    // Validar en cada cambio
    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('input', validateForm);
        input.addEventListener('change', validateForm);
    });
    
    // Validación inicial
    validateForm();
    
    // Confirmación antes de enviar
    form.addEventListener('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
            alert('Por favor, completa todos los campos requeridos.');
            return false;
        }
        
        // Cambiar texto del botón durante envío
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';
        submitBtn.disabled = true;
    });
    
    // Contador de caracteres para textareas
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            const length = this.value.length;
            const maxLength = 500; // Límite sugerido
            
            // Cambiar color si se acerca al límite
            if (length > maxLength * 0.8) {
                this.classList.add('border-warning');
            } else {
                this.classList.remove('border-warning');
            }
        });
    });
});
</script>
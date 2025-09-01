function allowDrop(ev) {
    ev.preventDefault();
}

function drag(ev) {
    ev.dataTransfer.setData("tarea_id", ev.target.dataset.id);
}

function drop(ev) {
    ev.preventDefault();
    let tareaId = ev.dataTransfer.getData("tarea_id");
    let listaId = ev.currentTarget.parentElement.dataset.listaId;

    // Mover en frontend
    ev.currentTarget.appendChild(document.querySelector('[data-id="' + tareaId + '"]'));

    // AJAX para actualizar en DB
    fetch("../../controllers/TareaController.php?accion=mover", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "id=" + tareaId + "&lista_id=" + listaId
    }).then(res => res.text())
      .then(data => console.log("Respuesta:", data))
      .catch(err => console.error("Error:", err));
}

function mostrarFormulario(listaId) {
    // Ocultar todos los formularios
    document.querySelectorAll('.form-tarea').forEach(form => {
        form.style.display = 'none';
    });
    // Mostrar el formulario específico
    document.getElementById('form-tarea-' + listaId).style.display = 'block';
}

function ocultarFormulario(listaId) {
    document.getElementById('form-tarea-' + listaId).style.display = 'none';
}
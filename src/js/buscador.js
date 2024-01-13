document.addEventListener('DOMContentLoaded', function() {
    iniciarApp();
});

function iniciarApp() {
    buscarPorFecha();
}

function buscarPorFecha() {
    const fechaInput = document.querySelector('#fecha');
    fechaInput.addEventListener('input', function(e) {
        const fechaSeleccionada = e.target.value; // Contiene el valor de la fecha seleccionada

        window.location = `?fecha=${fechaSeleccionada}`; // Redirecciona al usuario y añade la fecha a la url
    });
}
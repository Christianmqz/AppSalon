let paso = 1;
const pasoInicial = 1;
const pasoFinal = 3;

// cita = Objeto 
const cita = {
    id: '',
    nombre: '',
    fecha: '',
    hora: '',
    servicios: []
}

document.addEventListener('DOMContentLoaded', function() {
    iniciarApp();
});

function iniciarApp() {
    mostrarSeccion(); // Muestra y oculta las secciones
    tabs(); // Cambia la sección cuando se presionen los tabs
    botonesPaginador(); // Agrega o quita los botones 'Anterior' y 'Siguiente' del paginador
    paginaSiguiente(); 
    paginaAnterior();

    consultarAPI(); // Consulta la API en el backend de PHP

    idCliente();
    nombreCliente(); // Añade el nombre del cliente al objeto de cita
    seleccionarFecha(); // Añade la fecha de la cita en el objeto
    seleccionarHora(); // Añade la hora de la cita en el objeto

    mostrarResumen(); // Muestra el resumen de la cita
}

function mostrarSeccion() {

    // Ocultar la sección que tenga la clase de mostrar
    const seccionAnterior = document.querySelector('.mostrar');
    if(seccionAnterior) {
        seccionAnterior.classList.remove('mostrar');
    }

    // Seleccionar la sección con el paso...
    const pasoSelector = `#paso-${paso}`;
    const seccion = document.querySelector(pasoSelector);
    seccion.classList.add('mostrar');

    // Quita la clase de actual al tab anterior
    const tabAnterior = document.querySelector('.actual');
    if(tabAnterior) {
        tabAnterior.classList.remove('actual');
    }

    // Resalta el tab actual
    const tab = document.querySelector(`[data-paso="${paso}"]`);
    tab.classList.add('actual');
}

function tabs() {

    // Agrega y cambia la variable de paso según el tab seleccionado
    const botones = document.querySelectorAll('.tabs button');
	// No es posible utilizar 'addEventListener' dentro de un 'querySelectorAll', solo dentro de 'querySelector'
    botones.forEach( boton => {
        boton.addEventListener('click', function(e) {
            e.preventDefault();

            paso = parseInt( e.target.dataset.paso ); // target.dataset = Atributo en consola del elemento boton que retrona el numero de boton presionado
            mostrarSeccion();

            botonesPaginador(); 

            if(paso === 3) {
                mostrarResumen();
            } 
        });
    });
}

function botonesPaginador() {
    const paginaAnterior = document.querySelector('#anterior'); // # porque se selecciona un id
    const paginaSiguiente = document.querySelector('#siguiente');

    if(paso === 1) {
        paginaAnterior.classList.add('ocultar');
        paginaSiguiente.classList.remove('ocultar');
    } else if (paso === 3) {
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.add('ocultar');

        mostrarResumen();
    } else {
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.remove('ocultar');
    }

    mostrarSeccion();
}

function paginaAnterior() {
    const paginaAnterior = document.querySelector('#anterior');
    paginaAnterior.addEventListener('click', function() {

        if(paso <= pasoInicial) return;
        paso--;
        
        botonesPaginador();
    })
}
function paginaSiguiente() {
    const paginaSiguiente = document.querySelector('#siguiente');
    paginaSiguiente.addEventListener('click', function() {

        if(paso >= pasoFinal) return;
        paso++;
        
        botonesPaginador();
    })
}

// 'async' ejecuta la funcion junto con el resto del código, o sea se que no bloque a la ejecucion de las demás funciones
async function consultarAPI() {

    try {
        const url = `${location.origin}/api/servicios`; // ${location.origin} lleva al enlace principal del dominio 
        const resultado = await fetch(url);
        const servicios = await resultado.json();
        mostrarServicios(servicios);
    
    } catch (error) {
        console.log(error);
    }
}

function mostrarServicios(servicios) {
    servicios.forEach( servicio => {
        const { id, nombre, precio } = servicio;

		// Scripting - Inyectar HTML por medio de JavaScript
        const nombreServicio = document.createElement('P');
        nombreServicio.classList.add('nombre-servicio');
        nombreServicio.textContent = nombre;

        const precioServicio = document.createElement('P');
        precioServicio.classList.add('precio-servicio');
        precioServicio.textContent = `$${precio}`;

        const servicioDiv = document.createElement('DIV');
        servicioDiv.classList.add('servicio');
        servicioDiv.dataset.idServicio = id;
        servicioDiv.onclick = function() { // Callback
            seleccionarServicio(servicio);
        }

        servicioDiv.appendChild(nombreServicio);
        servicioDiv.appendChild(precioServicio);

		// Inyecta el div 'servicioDiv' a el id 'servicios'
        document.querySelector('#servicios').appendChild(servicioDiv);

    });
}

function seleccionarServicio(servicio) {
    const { id } = servicio;
    const { servicios } = cita;

    // Identificar el elemento al que se le da click
    const divServicio = document.querySelector(`[data-id-servicio="${id}"]`);

    // Comprobar si un servicio ya fue agregado 
    if( servicios.some( agregado => agregado.id === id ) ) {
        // .some() revisa si dentro de un arreglo, ya esta un elemento
        // Eliminarlo (Elemento ya agregado)
        cita.servicios = servicios.filter( agregado => agregado.id !== id );	
	    // .filter() crea un nuevo arreglo solo con los elementos que satisfacen 'agregado => agregado.id !== id'
        // En este caso filtra el servicio que sea coincida con cualquiera de los servicios ya seleccionados
        divServicio.classList.remove('seleccionado');
    } else {
        // Agregarlo (Elemento nuevo)
        cita.servicios = [...servicios, servicio];
        divServicio.classList.add('seleccionado');
    }
    
	// ... - spread operator. Crea un nuevo arreglo, tomando los elementos de 'servicios' 
    // y agrega el 'servicio' seleccionado al final 

    // console.log(servicio);
}

function idCliente() {
    cita.id = document.querySelector('#id').value;
}
function nombreCliente() {
    cita.nombre = document.querySelector('#nombre').value;
}

function seleccionarFecha() {
    const inputFecha = document.querySelector('#fecha');
    inputFecha.addEventListener('input', function(e) {

        const dia = new Date(e.target.value).getUTCDay();
		// .getUTCDay() retorna el dia de la semana en número 0 = Dom, 1 = Lun, etc.

        if( [6, 0].includes(dia) ) {
            e.target.value = '';
            ('Fines de semana no permitidos', 'error', '.formulario');
        } else {
            cita.fecha = e.target.value;
        }
        
    });
}

function seleccionarHora() {
    const inputHora = document.querySelector('#hora');
    inputHora.addEventListener('input', function(e) {

        const horaCita = e.target.value;
        const hora = horaCita.split(":")[0];	// .split() divide un string, utilizando como argumento el separador del string
        if(hora < 10 || hora > 18) {
		    // Horarios fuera de servicio
            e.target.value = '';
            mostrarAlerta('Hora No Válida', 'error', '.formulario');
        } else {
		    // Horario laboral 
            cita.hora = e.target.value;

            // console.log(cita);
        }
    })
}

function mostrarAlerta(mensaje, tipo, elemento, desaparece = true) {

    // Previene que se generen más de 1 alerta
    const alertaPrevia = document.querySelector('.alerta');
    if(alertaPrevia) {
        alertaPrevia.remove();
    }

    // Scripting para crear la alerta
    const alerta = document.createElement('DIV');
    alerta.textContent = mensaje;
    alerta.classList.add('alerta');
    alerta.classList.add(tipo);

    const referencia = document.querySelector(elemento);
    referencia.appendChild(alerta);

    if(desaparece) {
        // Eliminar la alerta
        setTimeout(() => {
            alerta.remove();
        }, 3000);
    }
  
}


function mostrarResumen() {
    const resumen = document.querySelector('.contenido-resumen');

    // console.log(cita);

    // Limpiar el Contenido de Resumen
    while(resumen.firstChild) {
        resumen.removeChild(resumen.firstChild);
    }
	
	// Verifica el horario de la cita
    if(Object.values(cita).includes('') || cita.servicios.length === 0 ) {
        mostrarAlerta('Faltan datos de Servicios, Fecha u Hora', 'error', '.contenido-resumen', false);

        return;
    } 
    
    // Formatear el div de resumen con destructuring
    // Destructuring - Extracción de elementos de su objeto u arreglo, para asignarlas a variables y que sean mas manipulables
    const { nombre, fecha, hora, servicios } = cita;


    // Heading para Servicios en Resumen
    const headingServicios = document.createElement('H3');
    headingServicios.textContent = 'Resumen de Servicios';
    resumen.appendChild(headingServicios);

    // Iterando y mostrando los servicios
    servicios.forEach(servicio => {
		// Destructuring del arreglo de servicios
        const { id, precio, nombre } = servicio;
        const contenedorServicio = document.createElement('DIV');
        contenedorServicio.classList.add('contenedor-servicio');

        const textoServicio = document.createElement('P');
        textoServicio.textContent = nombre;

        const precioServicio = document.createElement('P');
        precioServicio.innerHTML = `<span>Precio:</span> $${precio}`;

        contenedorServicio.appendChild(textoServicio);
        contenedorServicio.appendChild(precioServicio);

        resumen.appendChild(contenedorServicio);
    });

    // Heading para Cita en Resumen
    const headingCita = document.createElement('H3');
    headingCita.textContent = 'Resumen de Cita';
    resumen.appendChild(headingCita);

    const nombreCliente = document.createElement('P');
    nombreCliente.innerHTML = `<span>Nombre:</span> ${nombre}`;

    // Formatear la fecha en español
    const fechaObj = new Date(fecha);
    const mes = fechaObj.getMonth();
    const dia = fechaObj.getDate() + 2;
    const year = fechaObj.getFullYear();

    const fechaUTC = new Date( Date.UTC(year, mes, dia));
    
	// 'long', 'numeric', etc. son formatos en que se expresa la fecha y hora 
    const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'}
    const fechaFormateada = fechaUTC.toLocaleDateString('es-MX', opciones);
	// 'es-MX' es la fecha en Español Mexa.

    const fechaCita = document.createElement('P');
    fechaCita.innerHTML = `<span>Fecha:</span> ${fechaFormateada}`;

    const horaCita = document.createElement('P');
    horaCita.innerHTML = `<span>Hora:</span> ${hora} Horas`;

    // Boton para Crear una cita
    const botonReservar = document.createElement('BUTTON');
    botonReservar.classList.add('boton');
    botonReservar.textContent = 'Reservar Cita';
    botonReservar.onclick = reservarCita;
	// Cuando se tiene un evento como .onclick no se puede agregar la funcion
    // con parámetros, si contiene parámetros es mejor usar un callback

    resumen.appendChild(nombreCliente);
    resumen.appendChild(fechaCita);
    resumen.appendChild(horaCita);

    resumen.appendChild(botonReservar);
}

async function reservarCita() {
    const { nombre, fecha, hora, servicios, id } = cita; // Destructuring de cita
    
    const idServicios = servicios.map( servicio => servicio.id ); 
    // .map() a diferencia de .foreach() solo guarda las coincidencias en lugar de iterar
    
    // FormData() funciona como el submit de algun formulario, contiene la informacion a enviar.
    const datos  = new FormData();
    datos.append('fecha', fecha);
    datos.append('hora', hora);
    datos.append('usuarioId', id);
    datos.append('servicios', idServicios);
    
    // console.log([...datos]);

    // Petición hacia la API
    try {
        const url = `${location.origin}/api/citas`;

    const respuesta = await fetch(url, {
        method: 'POST',
        body: datos
    });

    const resultado = await respuesta.json();
    // console.log(resultado.resultado);

    if(resultado.resultado) {
          Swal.fire({
            title: "Estas seguro que quieres reservar tu cita?",
            text: "Tu cita no podrá ser revertida!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Reservar",
            cancelButtonText: "Modificar",
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: "success",
                    title: "Cita Creada",
                    text: "Tu cita fue creada correctamente.",
                    button:'OK'
                }).then( () => {
                    window.location.reload();    
                })
            }
        });
    }
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Algo salio mal al guardar la cita"
          });
    }
}


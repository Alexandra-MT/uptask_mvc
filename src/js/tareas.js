//iife, proteje las variables para no poder leerlo desde otro archivo y asi no haya interferencias 
(function(){

    obtenerTareas();
    //la variable tareas existe solo en la funcion obtenerTareas()
    //por lo tanto la funcion de agragar tarea no conoce el contenido de las tareas
    //se crea un variable global
    let tareas = [];
    let filtradas = [];
    //boton modal agragar tarea
    const nuevaTareaBtn = document.querySelector('#agregar-tarea');
    nuevaTareaBtn.addEventListener('click', function(){
        mostrarFormulario()});//para mandar a llamar el parametro por default 

    //Filtros de busqueda
    const filtros = document.querySelectorAll('#filtros input[type="radio"]');
    //console.log(filtros);
    filtros.forEach( radio => {
        radio.addEventListener('input', filtrarTareas);
    });

    function filtrarTareas(e){
        //console.log(e); muestra cada input
        const filtro = e.target.value;// 0 1 ""

        if(filtro !== ""){
            //pendientes o completas
            filtradas = tareas.filter( tarea => tarea.estado === filtro);
        }else{
            filtradas = [];
        }
        //console.log(filtradas);

        mostrarTareas();
    }

    async function obtenerTareas(){
       try {
            const urlProyecto = obtenerProyecto(); //para obtener solo la url
            const url = `/api/tareas?url=${urlProyecto}` ;
            const respuesta = await fetch(url); //como no es POST, no hace falta aportar datos
            const resultado = await respuesta.json();

            //console.log(resultado.tareas);
            //reemplazamos con la variable global
            //const {tareas} = resultado;

            tareas = resultado.tareas;// asi esta global de tareas estara disponible para todas las funciones
            mostrarTareas();
            //console.log(tareas);
            //mostrarTareas(tareas); ya no hace falta mostrar las tareas porque ya estan en la global
       } catch (error) {
            console.log(error);
       }
    }

    function mostrarTareas(){ //(tareas) pero como ya es global no se necesita
        //para que no se dupliquen las tareas, debemos limpiar el html
        limpiarTareas();

        totalPendientes();
        totalCompletas();

        //para poder filtrar, si el arreglo filtradas tiene algo entonces se muestra si no tiene se muestran las tareas
        const arrayTareas = filtradas.length ? filtradas : tareas;

        if(arrayTareas.length === 0){
            //si no hay tareas
            const contenedorTareas = document.querySelector('#listado-tareas');
            const textoNoTareas = document.createElement('LI');
            textoNoTareas.textContent = 'No existen tareas';
            textoNoTareas.classList.add('no-tareas');

            contenedorTareas.appendChild(textoNoTareas);
            return;//para no poner un else
        }
        //crear un objeto estados
        const estados = {
            0 : 'Pendiente',
            1 : 'Completa'
        }

        arrayTareas.forEach(tarea => {
            const contenedorTarea = document.createElement('LI');
            contenedorTarea.dataset.tareaId = tarea.id;// lo hemos sacado del objeto
            contenedorTarea.classList.add('tarea');

            const nombreTarea = document.createElement('P');
            nombreTarea.textContent = tarea.nombre;
            nombreTarea.ondblclick = function(){
                mostrarFormulario(true, {...tarea});
            }

            const opcionesDiv = document.createElement('DIV');
            opcionesDiv.classList.add('opciones');//estilos Css

            //Botones
            const btnEstadoTarea = document.createElement('BUTTON');
            btnEstadoTarea.classList.add('estado-tarea');
            btnEstadoTarea.classList.add(`${estados[tarea.estado].toLowerCase()}`);// para que la clase sea en minuscula
            btnEstadoTarea.textContent = estados[tarea.estado];
            btnEstadoTarea.dataset.estadoTarea = tarea.estado;// atributo personalizado
            //marcar y desmarcar las tareas como completas
            btnEstadoTarea.ondblclick = function(){
                //es una mala practica que js modifique el estado automaticamente
                //para impedir eso hacemos una copia del objeto

                cambiarEstadoTarea({...tarea}); //tarea actual
            }
            //boton eliminar tareas
            const btnEliminarTarea = document.createElement('BUTTON');
            btnEliminarTarea.classList.add('eliminar-tarea');
            btnEliminarTarea.dataset.idTarea = tarea.id;
            btnEliminarTarea.textContent = 'Eliminar';
            btnEliminarTarea.ondblclick = function(){
                confirmarEliminarTarea({...tarea});
            };

            //mostrar por pantalla
            opcionesDiv.appendChild(btnEstadoTarea);
            opcionesDiv.appendChild(btnEliminarTarea);
            contenedorTarea.appendChild(nombreTarea);
            contenedorTarea.appendChild(opcionesDiv);

           const listadoTareas = document.querySelector('#listado-tareas'); // que es el ul
           listadoTareas.appendChild(contenedorTarea); // que es el li
        });
    }

    function totalPendientes(){
        const totalPendientes = tareas.filter(tarea => tarea.estado === '0');
        const pendientesRadio = document.querySelector('#pendientes');
        
        if(totalPendientes.length === 0){
            pendientesRadio.disabled = true;
        }else{
            pendientesRadio.disabled = false;
        }
    }

    function totalCompletas(){
        const totalCompletas = tareas.filter(tarea => tarea.estado === '1');
        const completasRadio = document.querySelector('#completadas');
        
        if(totalCompletas.length === 0){
            completasRadio.disabled = true;
        }else{
            completasRadio.disabled = false;
        }
    }


    
    function filtroActivo() {
     
        // Revisa si hay un filtro activo
        const filtroActivo = document.querySelector('input[name="filtro"]:checked').value;
     
        if(filtroActivo) {
     
            // Filtra nuevamente
            filtradas = tareas.filter(tarea => tarea.estado === filtroActivo);
     
            // Si 'completas' o 'pendientes' es igual a 0 tareas, pasa a el filtro 'todas'
            if(!filtradas.length) {
                radiobtn = document.getElementById("todas");
                radiobtn.checked = true;
            }
        }
    }

    function mostrarFormulario(editar = false, tarea = {}){
        const modal = document.createElement('DIV');
        modal.classList.add('modal');
        modal.innerHTML= 
        `
        <form class="formulario nueva-tarea">
            <legend>${editar ? 'Editar Tarea' : 'Añade una nueva tarea'}</legend>
            <div class="campo">
                <label>Tarea</label>
                <input type="text" name="tarea" placeholder="${editar ? 'Modificar Nombre Tarea' : 'Añadir Tarea al Proyecto Actual'}" 
                id="tarea" value="${tarea.nombre ? tarea.nombre : ''}" />
            </div>
            <div class="opciones">
                <input type="submit" class="submit-nueva-tarea" value="${editar ? 'Guardar Cambios' : 'Añadir Nueva Tarea'}" />
                <button type="button" class="cerrar-modal">Cancelar</button>
            </div>
        </form>
        `;

        setTimeout(() => {
            const formulario = document.querySelector('.formulario');
            formulario.classList.add('animar');
        },0);

        //delegation en js, cuando el texto ha sido generado con js(innerhtml) no se puede asociar addeventlistener por lo tanto con delegation se puede seleciconar un boton y darle funcionalidades
        //cuando utilizas scripting, mas event handlers .onclik
        modal.addEventListener('click', function(e){
            e.preventDefault(); //para que no envie el formulario con el boton añadir tarea que es un submit
            if(e.target.classList.contains('cerrar-modal') || e.target.classList.contains('modal')){//no requiere el punto no es un selector
                const formulario = document.querySelector('.formulario');
                formulario.classList.add('cerrar');
                setTimeout(() => {
                    modal.remove(); //elimina el nodo, cuando pulsamos el boton Cancelar
                },0);
               
            }
            if(e.target.classList.contains('submit-nueva-tarea')){
                const nombreTarea = document.querySelector('#tarea').value.trim();//trim para espacios, eliminarlos
                
                if(nombreTarea === ''){ //esto incluye los espacios
                    //mostrar alerta de error
                    mostrarAlerta('El nombre de la tarea es obligatorio', 'error', document.querySelector('.formulario legend'));
                    return; //para que no se ejecute las siguientes lineas de codigo
                }

                if(editar){
                    //reescribimos el nombre de la tarea
                    tarea.nombre = nombreTarea;
                    actualizarTarea(tarea); //necesita el objeto
                }else{
                    agregarTarea(nombreTarea);//solo necesita el nombre de la tarea
                }
            }
        })

        document.querySelector('.dashboard').appendChild(modal);

        function ponleFocus(){
            document.getElementById('tarea').focus();
        }
        ponleFocus();
    }
    //muestra un mensaje en la interfaz
    function mostrarAlerta(mensaje, tipo, referencia){ //referencia apunta a en que parte del documento se añade la alerta
        //previene la creacion de multiples alertas
        const alertaPrevia = document.querySelector('.alerta');
        if(alertaPrevia){
            alertaPrevia.remove();
        }
        const alerta = document.createElement('DIV');
        alerta.classList.add('alerta',tipo);
        alerta.textContent = mensaje;
        //despues de legend, no se puede usar appendChild porque introducira el div dentro del legend y es incorrecto
        //referencia.appendChild(alerta);
        //se usa insertbefore no existe insertafter

        //console.log(referencia);
        //console.log(referencia.parentElement);
        //inserta la alerta antes del legend no dento del legend como append child
        //referencia.parentElement.insertBefore(alerta, referencia); //entre padre y label
        referencia.parentElement.insertBefore(alerta, referencia.nextElementSibling); //entre padre y el siguiente hermano de legend que es la referencia
    
        //eliminar la alerta
        setTimeout(() => {
            alerta.remove();
        },5000);
    
    }

    //consultar el servidor para añadir tarea
    async function agregarTarea(tarea){
        //formdata, construir la peticion
        const datos = new FormData();
        datos.append('nombre' ,tarea); //nombre que introducimos
        datos.append('proyectoId' , obtenerProyecto()); //la url del proyecto e6cac8450429ccc314ad083e827cae3b

        try {
            //async/await
            const url = 'http://localhost:3000/api/tarea';
            const respuesta = await fetch(url, {
                method: 'POST',
                body:datos
            });
            //lo que estamos leyendo
            const resultado = await respuesta.json();
           
            mostrarAlerta(resultado.mensaje, resultado.tipo, document.querySelector('.formulario legend'));
            
            if(resultado.tipo === 'exito'){ // importante que sea exitoso en el momento de guardar los datos
                const modal = document.querySelector('.modal');
                setTimeout(() => {
                    modal.remove();
                    //una forma de recargar la pagina para que se muestren las nuevas tareas
                    //pero se hace otra consulta al servidor y eso se debe evitar utilizando el virtualDOM
                    //window.location.reload();
                }, 3000);

                //agregar el objeto de tarea al global de tareas
                //primero lo construimos
                const tareaObj = {
                    id: String(resultado.id),
                    nombre: tarea, // es la variable que se esta pasando hacia esta funcion
                    estado: "0",
                    proyectoId: resultado.proyectoId

                }
                //despues lo agragamos, toma una copia exacta del arreglo tareas y le añade el nuevo objeto
                tareas = [...tareas, tareaObj];
                //filtro
                filtroActivo();
                //volvemos a mostrar las tareas
                mostrarTareas();
            }

        } catch (error) {
            console.log(error);// solo para servidor
        }

    }

    function cambiarEstadoTarea(tarea){
        const nuevoEstado = tarea.estado === "1" ? "0" : "1";
        tarea.estado = nuevoEstado;
        //por hacer copia del objeto se modificara solo la tarea actual pero no en el arreglo de tareas (quedara intacto)
        //console.log(tarea); estado = 1;
        //console.log(tareas); estado = 0;
        actualizarTarea(tarea);
    }

    async function actualizarTarea(tarea){
        const { estado, id, nombre, proyectoId } = tarea;
        const datos = new FormData();
        datos.append('id' , id);
        datos.append('nombre' , nombre);
        datos.append('estado' , estado);
        datos.append('proyectoId' , obtenerProyecto()); //para comprobar la url

        //la unica forma para iterrar dentro del FORMDATA
        //for(let valor of datos.values()){
        //    console.log(valor);
        //}
        try {
            const url = 'http://localhost:3000/api/tarea/actualizar';
            const respuesta = await fetch(url, {
                method: "POST",
                body: datos
        });
        const resultado = await respuesta.json();
        
        if(resultado.tipo === 'exito'){
            //mostrarAlerta(resultado.mensaje, resultado.tipo, document.querySelector('.contenedor-nueva-tarea'));
            Swal.fire(resultado.mensaje,resultado.mensaje, 'success');

            //cerramos la ventana modal
            const modal = document.querySelector('.modal');
            //solo si esta la ventana modal
            if(modal){
                modal.remove();
            }

            tareas = tareas.map(tareaMemoria => {
                //tareaMemoria traera todas las tareas asi que comparamos si algun id coincide con el id de la tarea donde hacemos click
                if(tareaMemoria.id === id){
                    tareaMemoria.estado = estado;
                    tareaMemoria.nombre = nombre;
                }
                return tareaMemoria; //para que asigne al nuevo arreglo
            }); //crea un nuevo arreglo ya con la actualizacion 

            //filtro
            filtroActivo();
            //mostramos en pantalla
            mostrarTareas();
        }
        } catch (error) {
            console.log(error);
        }
    }

    function confirmarEliminarTarea(tarea){
        Swal.fire({
            title: "¿Eliminar Tarea?",
            showCancelButton: true,
            confirmButtonText: "Si",
            cancelButtonText: "No"
          }).then((result) => {
            if (result.isConfirmed) {
                eliminarTarea(tarea);
            } 
          });
    }

    async function eliminarTarea(tarea){
        const { estado, id, nombre} = tarea;
        const datos = new FormData();
        datos.append('id', id);
        datos.append('nombre', nombre);
        datos.append('estado', estado);
        datos.append('proyectoId', obtenerProyecto());

       try {
            const url = 'http://localhost:3000/api/tarea/eliminar';
            const respuesta = await fetch(url, {
                method : 'POST',
                body: datos
            });
            const resultado = await respuesta.json();
            if(resultado.tipo === 'exito'){
                //mostrarAlerta(resultado.mensaje, resultado.tipo, document.querySelector('.contenedor-nueva-tarea')); 
                Swal.fire('Eliminado!', resultado.mensaje, 'success');

                tareas = tareas.filter( tareaMemoria => tareaMemoria.id !== tarea.id);
                //filtro
                filtroActivo();
                mostrarTareas();
            }
       } catch (error) {
            console.log(error);
       }
    }

    function obtenerProyecto(){
        const proyectoParams = new URLSearchParams(window.location.search);// windows.location donde te encuentras
        //console.log(proyectoParams);

        const proyecto = Object.fromEntries(proyectoParams.entries());
        return proyecto.url;

    }

    function limpiarTareas(){
        const listadoTareas = document.querySelector('#listado-tareas');
        //listadoTareas.innerHTML = ''; es mas lento

        //es mas rapido, mientras haya tareas eliminalos una a una
        while(listadoTareas.firstChild){
            listadoTareas.removeChild(listadoTareas.firstChild);
        }
    }

    
})(); //cierre iife con ();



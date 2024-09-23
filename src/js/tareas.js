//iife, proteje las variables para no poder leerlo desde otro archivo y asi no haya interferencias 
(function(){
    //boton modal agragar tarea
    const nuevaTareaBtn = document.querySelector('#agregar-tarea');
    nuevaTareaBtn.addEventListener('click', mostrarFormulario);

    function mostrarFormulario(){
        const modal = document.createElement('DIV');
        modal.classList.add('modal');
        modal.innerHTML= 
        `
        <form class="formulario nueva-tarea">
            <legend>Añade una nueva tarea</legend>
            <div class="campo">
                <label>Tarea</label>
                <input type="text" name="tarea" placeholder="Añadir Tarea al Proyecto Actual" id="tarea" />
            </div>
            <div class="opciones">
                <input type="submit" class="submit-nueva-tarea" value="Añadir Nueva Tarea" />
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
                submitFormularioNuevaTarea();
            }
        });

        document.querySelector('.dashboard').appendChild(modal);
    }

    function submitFormularioNuevaTarea(){
        const tarea = document.querySelector('#tarea').value.trim();//trim para espacios, eliminarlos
        if(tarea === ''){ //esto incluye los espacios
            //mostrar alerta de error
            mostrarAlerta('El nombre de la tarea es obligatorio', 'error', document.querySelector('.formulario legend'));
            return; //para que no se ejecute las siguientes lineas de codigo
        }

        agregarTarea(tarea);
       
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
        datos.append('nombre' ,tarea);

        try {
            //async/await
            const url = 'http://localhost:3000/api/tarea';
            const respuesta = await fetch(url, {
                method: 'POST',
                body:datos
            });
            
            const resultado = await respuesta.json();
            console.log(resultado);

        } catch (error) {
            console.log(error);
        }

    }
})(); //cierre iife con ();



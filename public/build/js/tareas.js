!function(){!async function(){try{const a=`/api/tareas?url=${o()}`,n=await fetch(a),r=await n.json();e=r.tareas,t()}catch(e){console.log(e)}}();let e=[];function t(){if(function(){const e=document.querySelector("#listado-tareas");for(;e.firstChild;)e.removeChild(e.firstChild)}(),0===e.length){const e=document.querySelector("#listado-tareas"),t=document.createElement("LI");return t.textContent="No existen tareas",t.classList.add("no-tareas"),void e.appendChild(t)}const t={0:"Pendiente",1:"Completa"};e.forEach((e=>{const a=document.createElement("LI");a.dataset.tareaId=e.id,a.classList.add("tarea");const n=document.createElement("P");n.textContent=e.nombre;const r=document.createElement("DIV");r.classList.add("opciones");const c=document.createElement("BUTTON");c.classList.add("estado-tarea"),c.classList.add(`${t[e.estado].toLowerCase()}`),c.textContent=t[e.estado],c.dataset.estadoTarea=e.estado,c.ondblclick=function(){!function(e){const t="1"===e.estado?"0":"1";e.estado=t,async function(e){const{estado:t,id:a,nombre:n,proyectoId:r}=e,c=new FormData;c.append("id",a),c.append("nombre",n),c.append("estado",t),c.append("proyectoId",o());try{const e="http://localhost:3000/api/tarea/actualizar",t=await fetch(e,{method:"POST",body:c});await t.json();console.log(t)}catch(e){console.log(e)}}(e)}({...e})};const s=document.createElement("BUTTON");s.classList.add("eliminar-tarea"),s.dataset.idTarea=e.id,s.textContent="Eliminar",r.appendChild(c),r.appendChild(s),a.appendChild(n),a.appendChild(r);document.querySelector("#listado-tareas").appendChild(a)}))}function a(e,t,a){const o=document.querySelector(".alerta");o&&o.remove();const n=document.createElement("DIV");n.classList.add("alerta",t),n.textContent=e,a.parentElement.insertBefore(n,a.nextElementSibling),setTimeout((()=>{n.remove()}),5e3)}function o(){const e=new URLSearchParams(window.location.search);return Object.fromEntries(e.entries()).url}document.querySelector("#agregar-tarea").addEventListener("click",(function(){const n=document.createElement("DIV");n.classList.add("modal"),n.innerHTML='\n        <form class="formulario nueva-tarea">\n            <legend>Añade una nueva tarea</legend>\n            <div class="campo">\n                <label>Tarea</label>\n                <input type="text" name="tarea" placeholder="Añadir Tarea al Proyecto Actual" id="tarea" />\n            </div>\n            <div class="opciones">\n                <input type="submit" class="submit-nueva-tarea" value="Añadir Nueva Tarea" />\n                <button type="button" class="cerrar-modal">Cancelar</button>\n            </div>\n        </form>\n        ',setTimeout((()=>{document.querySelector(".formulario").classList.add("animar")}),0),n.addEventListener("click",(function(r){if(r.preventDefault(),r.target.classList.contains("cerrar-modal")||r.target.classList.contains("modal")){document.querySelector(".formulario").classList.add("cerrar"),setTimeout((()=>{n.remove()}),0)}r.target.classList.contains("submit-nueva-tarea")&&function(){const n=document.querySelector("#tarea").value.trim();if(""===n)return void a("El nombre de la tarea es obligatorio","error",document.querySelector(".formulario legend"));!async function(n){const r=new FormData;r.append("nombre",n),r.append("proyectoId",o());try{const o="http://localhost:3000/api/tarea",c=await fetch(o,{method:"POST",body:r}),s=await c.json();if(a(s.mensaje,s.tipo,document.querySelector(".formulario legend")),"exito"===s.tipo){const a=document.querySelector(".modal");setTimeout((()=>{a.remove()}),3e3);const o={id:String(s.id),nombre:n,estado:"0",proyectoId:s.proyectoId};e=[...e,o],t()}}catch(e){console.log(e)}}(n)}()})),document.querySelector(".dashboard").appendChild(n)}))}();
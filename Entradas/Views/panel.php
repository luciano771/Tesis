<!DOCTYPE html>
<html>
<head>
    <title>Panel de Administración</title>
    <!-- Incluye jQuery primero -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- A continuación, incluye Moment.js en español antes de Tempus Dominus Bootstrap 4 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment-with-locales.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/es.js"></script>

    <!-- A continuación, incluye Bootstrap 4 CSS y JavaScript -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js"></script>

    <!-- A continuación, incluye Tempus Dominus Bootstrap 4 CSS y JavaScript -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js"></script>

    <link rel="stylesheet" href="Estilos/panel.css">
</head>
<body>
<?php
    session_start();

    // Verificar si el usuario está autenticado
    if (!isset($_SESSION['autenticado'])) {
        // Si no está autenticado, redirigir al inicio de sesión
        header('Location: login.html');
        exit;
    }
?>
    <header>
        <div class="cabecera">
            <h1>Subir Nuevo Articulo</h1>
        </div>    
    </header>
     
     
    <div class="principal">
        <form   action="../Controllers/panelController.php" method="POST"  enctype="multipart/form-data">
                    <div class="contenedor" id="contenedor"> 
                        <div class="mb-3">
                            <label for="Titulo" class="form-label">Titulo:</label>
                            <input type="text" class="form-control" id="Titulo" name="Titulo" required>
                        </div>
                        <div class="mb-3">
                            <label for="Descripcion" class="form-label">Descripcion:</label>
                            <input type="text" class="form-control" id="Descripcion" name="Descripcion" required>
                        </div>
                        <div class="mb-3">
                            <label for="Precio" class="form-label">Precio:</label>
                            <input type="text" class="form-control" id="Precio" name="Precio" required>
                        </div>
                        <div class="mb-3">
                            <label for="Cantidad" class="form-label">Cantidad:</label>
                            <input type="text" class="form-control" id="Cantidad" name="Cantidad" required>
                        </div>
                        <div class="form-group">
                            <label for="file">Imagen:</label>
                            <input type="hidden" id="img"></label>
                            <input type="file" class="form-control-file" id="file" name="file">
                        </div>           
                        <input type="hidden" name="accion" id="accion" value="agregar">
                        <input type="hidden" name="pk_eventos" id="pk_eventos" value="">
                        <button type="submit" id ="boton" class="btn btn-primary">Cargar Articulo</button>
                    </div>               
            </form>

            <div id="administrador"></div>
            <br>
            <div class="paginacion">
                    <button id="btnAnterior" class="btn btn-primary boton">Anterior</button>
                    <button id="btnSiguiente" class="btn btn-primary boton">Siguiente</button>
            </div>      
    </div>


 
        






 



        <script>
            let Modificar = false;
            const eventosPorPagina = 5; // Número de eventos por página
            let paginaActual = 1;
            let eventos;
 

// Función para mostrar eventos en la página actual
async function mostrarEventosEnPagina() {
  const eventosContainer = document.getElementById('administrador');
  eventosContainer.innerHTML = ''; // Borra los eventos anteriores

  const inicio = (paginaActual - 1) * eventosPorPagina;
  const fin = inicio + eventosPorPagina;

    var urlCompleta = window.location.href;
    // Separar la URL en partes (dominio y resto)
    var partesURL = urlCompleta.split('/');
    // Obtener la parte del dominio
    var dominio = partesURL.slice(0, 4).join('/'); // Esto captura "http://localhost/meraki/entradas con slice(0,5)"
    // Concatenar el resto de la URL que desees
    

  for (let i = inicio; i < fin; i++) {
    if (eventos[i]) {
         

      const evento = eventos[i];
      var restoDeLaURL = '/Controllers/panelController.php?ListadoReservas=true&pkEvento='+evento.pk_eventos;
        // Construir la nueva URL
        var nuevaURL = dominio + restoDeLaURL;
      const divEvento = document.createElement('div');
      divEvento.className = 'eventoPanel';
        divEvento.innerHTML = `
            <p>${evento.pk_articulos}: ${evento.titulo} 
            <button class="btn btn-primary boton" accion="modificar" id="modificar" pkeventos="${evento.pk_articulos}">Modificar</button>
            <button class="btn btn-primary boton" accion="eliminar" id="eliminar" pkeventos="${evento.pk_articulos}">Eliminar</button>
            `;
        eventosContainer.appendChild(divEvento);
        const Btns = divEvento.querySelectorAll('.boton');
            Btns.forEach(async (Btn) => {
                    Btn.addEventListener("click", async function() {
                        const pkEventos = this.getAttribute('pkeventos');
                        const accion = this.getAttribute('accion');
                        console.log('pkeventos es:', pkEventos," y la accion es: ",accion);
                        //cargar popup del admin
                        if(accion=='modificar'){                            
                                   MandarGet(pkEventos, accion)
                                    .then(data => {
                                        if(data.length>0){
                                            const evento = data[0];
                                            console.log("Respuesta JSON:", evento);
                                            document.getElementById('Titulo').value = evento.titulo;
                                            document.getElementById('Descripcion').value = evento.descripcion;
                                            document.getElementById('Precio').value = evento.Precio;
                                            document.getElementById('Cantidad').value = evento.cantidad;
                                            var titulo = evento.titulo;
                                            var descripcion = evento.descripcion;
                                            var fecha_inicio = evento.precio;
                                            var fecha_fin = evento.cantidad;
                                            var img = evento.img;
                                           
                                            Cargar_A_Actualizar(pkEventos);
                                            Modificar = true;
                                        }
                                    })
                                .catch(error => {
                                    console.error("Error en la solicitud AJAX:", error);
                                });                                                                                                          
                        }else if(accion=='eliminar'){
                            var boton = document.getElementById("boton").innerText = "Cargar Articulo";
                            var accionpost = document.getElementById('accion').value = "agregar";
                            console.log(boton,accionpost);
                            Modificar = false;
                            MandarGet(pkEventos,accion);
                            alert("Se elimino el articulo: "+pkEventos);
                            location.reload();
                           
                        }
                          
             });
        });
    }
  }
}

            // Función para cambiar a la página anterior
            function paginaAnterior() {
            if (paginaActual > 1) {
                paginaActual--;
                mostrarEventosEnPagina();
            }
            }

            // Función para cambiar a la página siguiente
            function paginaSiguiente() {
            if (paginaActual < Math.ceil(eventos.length / eventosPorPagina)) {
                paginaActual++;
                mostrarEventosEnPagina();
            }
            }

            // Event listeners para los botones de paginación
            const btnAnterior = document.getElementById('btnAnterior');
            const btnSiguiente = document.getElementById('btnSiguiente');

            btnAnterior.addEventListener('click', paginaAnterior);
            btnSiguiente.addEventListener('click', paginaSiguiente);


            fetch('../Controllers/panelController.php?TraerEventos=true')
            .then(response => response.json())
            .then(data => {
                // Iterar sobre los datos y generar divs para cada evento
                const eventosContainer = document.getElementById('administrador');
                eventos = data; 
                mostrarEventosEnPagina();
                    
                
            })
            .catch(error => {
                console.error('Error al obtener los datos de eventos:', error);
            });




            function MandarGet(pkEventos, accion) {
                const url = `../Controllers/panelController.php?accion=${accion}&pkEvento=${pkEventos}`;
                return fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Error en la solicitud AJAX");
                    }
                    return response.json();
                });
            }

           
 

  

            
            

            

            function Cargar_A_Actualizar(pkevento) {
                var accion = document.getElementById('accion');
                accion.value = "actualizar";
                var boton = document.getElementById('boton'); 
                boton.innerText = "Actualizar";   
                var pk_eventos = document.getElementById('pk_eventos'); 
                pk_eventos.value = pkevento;    

                console.log(accion,boton,pk_eventos);
            }

            
            




        </script>
















 
</body>
</html>
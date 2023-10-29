<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sala de espera</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">   
    <link rel="stylesheet" href="Estilos/sala.css">
 
</head>
<body>
    <header>
        <h1>Sala de espera</h1>
    </header>
    <section>
    <div id="contenedor">
            <br><br>
            <div class="contenedor-loader">
                <div class="rueda"></div>
            </div>
            <div class="cargando"></div>  
        </div>
        <div class="principal">
            <p>La fila ya inicio, aguarde a ser redirigido al formulario de reserva.</p>  
            <p>Recuerde no cerrar ni actualizar la pagina ya que perdera su turno. </p>  
        </div>
        </div>
            <div id="sessiones-listado">
        </div>
    </section>

        <div id="modal" class="modal">
        <div class="modal-content">
            <p>Esta alerta se cerrará automáticamente después de 15 segundos. ¿Desea extender la sesión?</p>
            <button id="aceptar" >Aceptar</button>
            <button id="cancelar" >Cancelar</button>
        </div>
    </div>


    <script>
        

    
        function BorrarSessiones30Min() {
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "../cronjob.php?BorrarSessiones", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        console.log("Sesiones eliminadas con éxito");
                        // Aquí puedes mostrar un mensaje al usuario en la interfaz
                    } else {
                        console.error("Error al eliminar sesiones");
                        // Aquí puedes manejar el error y mostrar un mensaje de error al usuario
                    }
                }
            };
            xhr.send();
        }

        BorrarSessiones30Min();
       

        
        function AumentarSession() {
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "../cronjob.php?AumentarSession", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        console.log("Sesiones eliminadas con éxito");
                        // Aquí puedes mostrar un mensaje al usuario en la interfaz
                    } else {
                        console.error("Error al eliminar sesiones");
                        // Aquí puedes manejar el error y mostrar un mensaje de error al usuario
                    }
                }
            };
            xhr.send();
        }

       



        let  dataInicial = 0;

        var url = new URL(window.location.href);

        let redijir  = false;
        var pk_eventos = url.searchParams.get('pk_eventos');
        PrimeraEjecucion = true;
        console.log(pk_eventos)
        function SessionesLista(pk_eventos){
            const sessionesContainer = document.getElementById("sessiones-listado")
            
            fetch('../Controllers/salaController.php?pkevento='+url.searchParams.get('pk_eventos')+"&fila")
            .then(response => response.json())
            .then(data => {
                // Verifica que data sea un array

                // Verifica que la propiedad "sessiones" exista en el objeto
                if (Array.isArray(data)) {
                const divSessiones = document.createElement('div');
                divSessiones.className = 'divSessiones';
                divSessiones.innerHTML = `
                    <h4>Cantidad de usuarios en espera: ${data.length}
                `;
                
                if(!PrimeraEjecucion){
                    sessionesContainer.innerHTML = ''; // Limpia el contenido existente
                }
                
                // if(data.length != dataInicial){
                //     VerificarOrden();
                //     enviarHeartbeat(pk_eventos);
                //     dataInicial=data.length;

                // }
                //almacenar en una variable los nuevos data.length.
                // comparar data.length con el valor almacenado en la variable y ver si cambio
                // si cambio ejecutar verificarorden y borrarsession
                sessionesContainer.appendChild(divSessiones);
               }
       

                VerificarOrden();
                enviarHeartbeat(url.searchParams.get('pk_eventos'));

            })
            .catch(error => {
                console.error('Error al obtener los usuarios en espera:', error);
            });
            PrimeraEjecucion=false;

           
        }

        SessionesLista();
        setInterval(SessionesLista, 60000);

        //obtengo el orden por session para redijir por orden de llegada a reservar.php
        //se manda cada 15 seg al servidor para verificar el orden. 
         
        function enviarHeartbeat(pk_eventos) {
            fetch("../Controllers/salaController.php?ESTADOSESSION=ESTADO&pkeventos=" + pk_eventos)
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Error en la solicitud AJAX");
                    }

                    return response.text();
                })
                .then(data => {
                    if (data.trim() != 1) {
                         
                        window.location.href = '../Views/Eventos.html';
                    }
                    console.log(data.trim());
                })
                .catch(error => {
                    // Maneja errores en la solicitud AJAX
                    console.error("Error en la solicitud AJAX:", error);
                });
        }

        function VerificarOrden() {
        fetch("../Controllers/salaController.php?VerificarOrden=true&pk_eventos=" + pk_eventos)
            .then(response => {
                if (!response.ok) {
                    throw new Error("Error en la solicitud AJAX");
                }
                return response.text();
            })
            .then(data => {
                if (data.trim() === 'true') {
                    // No es necesario redirigir desde el cliente, ya que se hace desde el servidor
                    console.log('Debería redirigirme: ' + data);
                    redijir  = true;
                    window.location.href = '../Views/reservar.php?pk_eventos=' + pk_eventos;
                } else {
                    console.log('No debería redirigirme: ' + data);
                }
            })
            .catch(error => {
                // Maneja errores en la solicitud AJAX
                console.error("Error en la solicitud AJAX:", error);
            });
        }

       
        
     
       
         

        window.addEventListener("beforeunload", function (e) {
            console.log("Evento unload disparado"); // Agrega un mensaje de depuración en la consola
            if(!redijir){
                enviarSolicitudPOSTParaCerrarSesion();
            }
        });

        function enviarSolicitudPOSTParaCerrarSesion() {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "../Controllers/salaController.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    window.location.href ="../Views/Eventos.html";        

                }
            };
            xhr.send("activo=no");
        }

 
    //modal session
    
    
    
        var PrimerModal = false;


        var tiempoExpiracionCerrarSesion = 285000; // 299985
        var intervalId = null;
        var modal = document.getElementById("modal");
        var btnAceptar = document.getElementById("aceptar");
        var btnCancelar = document.getElementById("cancelar");

        // Función para mostrar la ventana modal
        function mostrarModal() {
            modal.style.display = "block";
        }

        // Función para ocultar la ventana modal
        function ocultarModal() {
            modal.style.display = "none";
        }

        // Evento para mostrar la ventana modal
        btnAceptar.onclick = function() {
            ocultarModal();
            // Reinicia el intervalo de cerrar sesión y vuelve a configurarlo
            clearInterval(intervalId);
            AumentarSession();
            intervalId = setInterval(() => enviarSolicitudPOSTParaCerrarSesion(pk_eventos), tiempoExpiracionCerrarSesion);
            PrimerModal = true;
        }

        // Evento para ocultar la ventana modal
        btnCancelar.onclick = function() {
            ocultarModal();
            enviarSolicitudPOSTParaCerrarSesion(pk_eventos);
        }

        // Configura un intervalo inicial para enviar la solicitud de cerrar sesión
        intervalId = setInterval(() => enviarSolicitudPOSTParaCerrarSesion(pk_eventos), tiempoExpiracionCerrarSesion);

        // Establece un intervalo para mostrar la ventana modal cada 2 minutos (120,000 milisegundos) 299970
        if(!PrimerModal){setInterval(mostrarModal, 270000);}
 
        
 

        
 


    </script>



</body>
</html>
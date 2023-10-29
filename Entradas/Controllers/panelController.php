<?php
require '../vendor/autoload.php'; // Carga la biblioteca Spout
 
 
include '../Models/EventosModel.php';
include '../Models/ActoresModel.php';
include '../Models/CompradoresModel.php';
include '../Models/SessionesModel.php';


$db = new conexion();
$instancia = new EventosModel($db);
$instancia2 = new ActoresModel($db);
$instancia3 = new CompradoresModel($db);
$instancia4 = new SessionesModel($db);


if($_SERVER["REQUEST_METHOD"] == "POST"){

    if (isset($_POST['Titulo']) && isset($_POST['Descripcion']) && isset($_POST['Fecha_inicio']) && isset($_POST['Fecha_fin']) && isset($_FILES["file"]) && isset($_FILES['archivo']) && isset($_POST['accion']) && $_POST['accion']==='agregar') {
        
        $Titulo = $_POST['Titulo'];
        $Descripcion = $_POST['Descripcion'];
        $Fecha_inicio = $_POST['Fecha_inicio'];
        $Fecha_fin = $_POST['Fecha_fin'];
        $Fecha_inicio_Guardarbase = $Fecha_inicio;
        $Fecha_fin_Guardarbase = $Fecha_fin;


        $file = $_FILES["file"]["name"];
        $url_temp= $_FILES["file"]["tmp_name"];
        



        if ($_FILES["file"]["error"] === 0) {
            $url_temp= $_FILES["file"]["tmp_name"];
    
            // Mueve el archivo del directorio temporal a la ubicación deseada
    
            $url_insert = dirname(__FILE__) . "/../imagenes";
            $url_target = str_replace('\\', '/', $url_insert) . '/' . $file;
    
            if (!file_exists($url_insert)) {
                mkdir($url_insert, 0777, true);
            };
    
            if (move_uploaded_file($url_temp, $url_target)) {

                try {
                    $instancia->setTitulo($Titulo);
                    $instancia->setDescripcion($Descripcion);
                    $instancia->setFechaInicio($Fecha_inicio_Guardarbase);
                    $instancia->setFechaFin($Fecha_fin_Guardarbase);
                    $instancia->setUrlBase('../imagenes/' . $file);
                    $instancia->insertarEvento();

    
                } catch (PDOException $e) {
                    echo "Error al insertar el evento: " . $e->getMessage();
                }

            } else {
                echo "Ha habido un error al cargar tu archivo.";
            }
        }
        else {
            echo "Error al cargar el archivo. Código de error: " . $_FILES["file"]['error'];
        }

        $archivo = $_FILES["archivo"]["tmp_name"]; 


        try {
            $instancia2->setarchivo($archivo);
            $instancia2->insertarListado();

        } catch (PDOException $e) {
            echo "Error al insertar el evento: " . $e->getMessage();
        }

        echo '<script>
        //alert("Se ha cargado el evento con éxito.");
        window.location.href = "../Views/panel.php"; // Redirige a panel.php
        </script>';



    } 



    if(isset($_POST['Titulo']) && isset($_POST['Descripcion']) && isset($_POST['Fecha_inicio']) && isset($_POST['Fecha_fin']) && isset($_POST['pk_eventos']) && isset($_POST['accion']) && $_POST['accion'] === 'actualizar') {
        // echo '<script>
        //     alert("ingreso.");
        //     </script>';
        
        $pk_eventos = $_POST['pk_eventos'];
        $TituloAc = $_POST['Titulo'];
        $DescripcionAc = $_POST['Descripcion'];
        $Fecha_inicioAc = $_POST['Fecha_inicio'];
        $Fecha_finAc = $_POST['Fecha_fin'];
        $Fecha_inicio_GuardarbaseAc = $Fecha_inicioAc;
        $Fecha_fin_GuardarbaseAc = $Fecha_finAc;
     
    
     
        
        $imagenCargada = isset($_FILES["file"]) && $_FILES["file"]["error"] === 0;
        $archivoListadoCargado = isset($_FILES["archivo"]) && $_FILES["archivo"]["error"] === 0;

       
       
        if ($imagenCargada && $archivoListadoCargado) {

            // echo '<script>
            // alert("Se quiere actualizar con imagen y listado.");
            // </script>';
            
        if ($_FILES["file"]["error"] === 0) {
            $file = $_FILES["file"]["name"];

            $url_temp= $_FILES["file"]["tmp_name"];
    
            // Mueve el archivo del directorio temporal a la ubicación deseada
    
            $url_insert = dirname(__FILE__) . "/../imagenes";
            $url_target = str_replace('\\', '/', $url_insert) . '/' . $file;
    
            if (!file_exists($url_insert)) {
                mkdir($url_insert, 0777, true);
            };
    
            if (move_uploaded_file($url_temp, $url_target)) {

                try {
         

                    $instancia->setTitulo($TituloAc);
                    $instancia->setDescripcion($DescripcionAc);
                    $instancia->setFechaInicio($Fecha_inicio_GuardarbaseAc);
                    $instancia->setFechaFin($Fecha_fin_GuardarbaseAc);
                    $instancia->setUrlBase('../imagenes/' . $file);
                    $instancia->ActualizarEvento($pk_eventos);

    
                } catch (PDOException $e) {
                    echo "Error al insertar el evento: " . $e->getMessage();
                }

            } else {
                echo "Ha habido un error al cargar tu archivo.";
            }
        }
        else {
            echo "Error al cargar el archivo. Código de error: " . $_FILES["file"]['error'];
        }

        $archivo = $_FILES["archivo"]["tmp_name"]; 


        try {
            $instancia2->setarchivo($archivo);
            $instancia2->BorrarListado($pk_eventos);
            $instancia2->insertarListadopk($pk_eventos);

        } catch (PDOException $e) {
            echo "Error al insertar el evento: " . $e->getMessage();
        }

        echo '<script>
        //alert("Se ha actualizado el evento con éxito.");
        window.location.href = "../Views/panel.php"; // Redirige a panel.php
        </script>';

        }
    
       
       
        if ($imagenCargada || $archivoListadoCargado) {
            // Al menos uno de los archivos está cargado
        
            // echo '<script>
            //     alert("Entro al bloque principal.");
            // </script>';
        
            // Procesar la imagen si está cargada
            if (isset($_FILES["file"]) && $_FILES["file"]["error"] === 0) {
                $file = $_FILES["file"]["name"];
                $url_temp = $_FILES["file"]["tmp_name"];
                $url_insert = dirname(__FILE__) . "/../imagenes";
                $url_target = str_replace('\\', '/', $url_insert) . '/' . $file;
        
                if (!file_exists($url_insert)) {
                    mkdir($url_insert, 0777, true);
                };
        
                if (move_uploaded_file($url_temp, $url_target)) {
                    try {
                        $instancia->setTitulo($TituloAc);
                        $instancia->setDescripcion($DescripcionAc);
                        $instancia->setFechaInicio($Fecha_inicio_GuardarbaseAc);
                        $instancia->setFechaFin($Fecha_fin_GuardarbaseAc);
                        $instancia->setUrlBase('../imagenes/' . $file);
                        $instancia->ActualizarEvento($pk_eventos);
        
                        echo '<script>
                            //alert("Se ha actualizado el evento con imagen actualizada y sin actualizar listado.");
                            window.location.href = "../Views/panel.php"; // Redirige a panel.php
                        </script>';
                    } catch (PDOException $e) {
                        echo "Error al actualizar el evento: " . $e->getMessage();
                    }
                } else {
                    echo "Error al cargar la imagen. Código de error: " . $_FILES["file"]['error'];
                }
            }
        
            // Procesar el archivo de listado si está cargado
            if ($archivoListadoCargado) {
                // Código para procesar el archivo de listado aquí
                // ...
                
                try {
                    $instancia->setTitulo($TituloAc);
                    $instancia->setDescripcion($DescripcionAc);
                    $instancia->setFechaInicio($Fecha_inicio_GuardarbaseAc);
                    $instancia->setFechaFin($Fecha_fin_GuardarbaseAc);
                    $instancia->ActualizarEvento($pk_eventos);
                } catch (PDOException $e) {
                    echo "Error al actualizar el evento: " . $e->getMessage();
                }
                $archivo = $_FILES["archivo"]["tmp_name"];
                try {
                    $instancia2->setarchivo($archivo);
                    $instancia2->BorrarListado($pk_eventos);
                    $instancia2->insertarListadopk($pk_eventos);
                    
                } catch (PDOException $e) {
                    echo "Error al insertar el evento: " . $e->getMessage();
                }

                echo '<script>
                    //alert("Se ha actualizado el evento sin  actualizar imagen y con listado actualizado.");
                    window.location.href = "../Views/panel.php"; // Redirige a panel.php
                    </script>';
            }
        } else {
            // Ninguno de los dos archivos está cargado
            // echo '<script>
            //     alert("Se ha actualizado el evento, no se actualizo imagen y listado.");
            //     </script>';
                try {
                    // Actualizar la información del evento en la base de datos
                 

                    $instancia->setTitulo($TituloAc);
                    $instancia->setDescripcion($DescripcionAc);
                    $instancia->setFechaInicio($Fecha_inicio_GuardarbaseAc);
                    $instancia->setFechaFin($Fecha_fin_GuardarbaseAc);
                    $instancia->ActualizarEvento($pk_eventos);
        
                    echo '<script>
                    //alert("Se ha cargado el evento con éxito.");
                    window.location.href = "../Views/panel.php"; // Redirige a panel.php
                    </script>';
                } catch (PDOException $e) {
                    echo "Error al actualizar el evento: " . $e->getMessage();
                }
        }

         
    }
    if(isset($_POST["EliminarEvento"]) && $_POST["EliminarEvento"]=="true" && isset($_POST["pkEvento"])){
        $instancia2->BorrarListado($_POST["pkEvento"]);
        $instancia3->BorrarCompradores($_POST["pkEvento"]);
        $instancia->BorrarEvento($_POST["pkEvento"]);
        $response = array('message' => 'Evento eliminado con éxito');
        header('Content-Type: application/json');
        echo json_encode($response);
    }  
     
    
     

     
}
 





// if(isset($_GET["EliminarEvento"]) && $_GET["EliminarEvento"]=="true" && isset($_GET["pkEvento"])){
//     $instancia2->BorrarListado($_GET["pkEvento"]);
//     $instancia3->BorrarCompradores($_GET["pkEvento"]);
//     $instancia->BorrarEvento($_GET["pkEvento"]);
//     $response = array('message' => 'Evento eliminado con éxito');
//     header('Content-Type: application/json');
//     echo json_encode($response);
// } 
       
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
  
    

 












 








if($_SERVER["REQUEST_METHOD"] == "GET"){
    if(isset($_GET["TraerEventos"]) && $_GET["TraerEventos"]=="true"){
        $instancia->ObtenerEventos();
    }
    if(isset($_GET["Listado"]) && $_GET["Listado"]=="true" && isset($_GET["pkEvento"])){
        $resultado = $instancia2->ObtenerActoresPorId($_GET["pkEvento"]);
        $bool = $instancia2->ConsultarListado($_GET["pkEvento"]);
        if($bool!=null){
            $jsonData = json_encode($resultado);
            generarExcelActores($jsonData);
        }
        else{
            echo '
            <!DOCTYPE html>
                <html>
                <head>
                    <title>Página Actual</title>
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
                </head>
                <body>
                    <h4>No hay un listado de actores asociado al evento seleccionado</h4>
                    <!-- Tu contenido de página actual aquí -->

                    <!-- Botón "Volver" que redirige al usuario a la página anterior -->
                    &nbsp; <a href="javascript:history.back()" class="btn btn-primary">Volver</a>
                </body>
            </html>';
        }
    }
    if(isset($_GET["ListadoReservas"]) && $_GET["ListadoReservas"]=="true" && isset($_GET["pkEvento"])){
            
        
        $resultado = $instancia3->ObtenerReservasPorId($_GET["pkEvento"]);
     
        
        $bool = $instancia3->ConsultarReservas($_GET["pkEvento"]);
        if($bool!=null){
            generarExcelReservas($resultado);
        }
        else{
            echo '
            <!DOCTYPE html>
                <html>
                <head>
                    <title>Página Actual</title>
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
                </head>
                <body>
                    <h4>No hay reservas asociadas a este evento.</h4>
                    <!-- Tu contenido de página actual aquí -->

                    <!-- Botón "Volver" que redirige al usuario a la página anterior -->
                    &nbsp; <a href="javascript:history.back()" class="btn btn-primary">Volver</a>
                </body>
            </html>';
        }
        
        
    }
    if(isset($_GET["accion"]) && $_GET["accion"]=="modificar" && isset($_GET["pkEvento"])){
        $instancia->ObtenerEventosPorId($_GET["pkEvento"]);
    }

    if(isset($_GET["accion"]) && $_GET["accion"]=="eliminar" && isset($_GET["pkEvento"])){
        $pkevento = $_GET["pkEvento"];
        $instancia2->BorrarListado($pkevento);
        $instancia->BorrarEvento($pkevento);

    }
    
 
}


if (isset($_GET['FilaBool']) && $_GET['FilaBool'] == 'true' && isset($_GET['pkEvento'])) {
    
    $sessionBool = $instancia4->FilaBool($_GET['pkEvento']);
    $bool='false';
    if($sessionBool){
        $bool='true';
            
    }
    echo trim($bool);   
} 

if (isset($_GET['ReservasBool']) && $_GET['ReservasBool'] == 'true' && isset($_GET['pkEvento'])) {
    
    $sessionBool = $instancia2->ConsultarListado($_GET['pkEvento']);
    $bool='false';
    if($sessionBool){
        $bool='true';
    }
    echo trim($bool);   
} 

function generarExcelReservas($jsonData) {
    $datos = json_decode($jsonData, true);

    $nombreArchivo = "datos.csv";
    $archivo = fopen($nombreArchivo, "w");

    if ($archivo) {
        // Obtén los nombres de las columnas (encabezados) a partir de la primera fila de datos
        $encabezados = array_keys(reset($datos));
        fputcsv($archivo, $encabezados, "\t");

        foreach ($datos as $dato) {
            // Agrega los valores de todas las columnas en el orden de los encabezados
            $fila = [];
            foreach ($encabezados as $columna) {
                $fila[] = $dato[$columna];
            }

            fputcsv($archivo, $fila, "\t");
        }

        fclose($archivo);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename=datos.csv');
        readfile($nombreArchivo);

        // Elimina el archivo después de enviarlo al cliente (opcional)
        unlink($nombreArchivo);
        exit;
    } else {
        echo "No se pudo abrir el archivo temporal para escritura.";
    }
}




function generarExcelActores($jsonData) {

    $datos = json_decode($jsonData, true);


    $nombreArchivo = "datos.csv";
    $archivo = fopen($nombreArchivo, "w");

    if ($archivo) {
         
        fputcsv($archivo,[],' '); // Usar un espacio como separador, SEGUNDO ARGUMENTO SON LOS ENCABEZADOS.

        // Escribe los datos en el archivo CSV
        foreach ($datos as $dato) {
            fputcsv($archivo, $dato, ' '); // Usar un espacio como separador
        }

        fclose($archivo);

      
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename=datos.csv');
        readfile($nombreArchivo);

        // Elimina el archivo después de enviarlo al cliente (opcional)
        unlink($nombreArchivo);
        exit;
    } else {
        echo "No se pudo abrir el archivo temporal para escritura.";
    }
}

    
    
unset($db);
unset($instancia);
unset($instancia2);
unset($instancia3);
unset($instancia4);

   
 
     

 


   
    
 
 

  


 





?>
<?php
require '../vendor/autoload.php'; // Carga la biblioteca Spout
 
 
include '../Models/ArticulosModel.php';
include '../Models/CompradoresModel.php';



$db = new conexion();
$instancia = new ArticulosModel($db);
 



if($_SERVER["REQUEST_METHOD"] == "POST"){

    if (isset($_POST['Titulo']) && isset($_POST['Descripcion']) && isset($_POST['Precio']) && isset($_POST['Cantidad']) && isset($_FILES["file"])  && isset($_POST['accion']) && $_POST['accion']=='agregar') {
        
        $Titulo = $_POST['Titulo'];
        $Descripcion = $_POST['Descripcion'];
        $Precio = $_POST['Precio'];
        $Cantidad = $_POST['Cantidad'];
 


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
                    $instancia->setPrecio($Precio);
                    $instancia->setCantidad($Cantidad);
                    $instancia->setImg('../imagenes/' . $file);
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

        

 
        echo '<script>
        //alert("Se ha cargado el articulo con éxito.");
        window.location.href = "../Views/panel.php"; // Redirige a panel.php
        </script>';



    } 



    if(isset($_POST['Titulo']) && isset($_POST['Descripcion']) && isset($_POST['Precio']) && isset($_POST['Cantidad']) && isset($_POST['pk_eventos']) && isset($_POST['accion']) && $_POST['accion'] === 'actualizar') {
      
        $pk_eventos = $_POST['pk_eventos'];
        $TituloAc = $_POST['Titulo'];
        $DescripcionAc = $_POST['Descripcion'];
        $PrecioAc = $_POST['Precio'];
        $CantidadAc = $_POST['Cantidad'];
   
     
    
     
        
        $imagenCargada = isset($_FILES["file"]) && $_FILES["file"]["error"] === 0;     
       
        
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
                    $instancia->setPrecio($PrecioAc);
                    $instancia->setCantidad($CantidadAc);
                    $instancia->setImg('../imagenes/' . $file);
                    $instancia->ActualizarEvento($pk_eventos);
    
                     
                } catch (PDOException $e) {
                    echo "Error al actualizar el evento: " . $e->getMessage();
                }
            } else {
                echo "Error al cargar la imagen. Código de error: " . $_FILES["file"]['error'];
            }
        }
        
        
           
         else {
            //Ninguno de los dos archivos está cargado
            echo '<script>
                alert("Se ha actualizado el evento, no se actualizo imagen y listado.");
                </script>';
                try {

                    $instancia->setTitulo($TituloAc);
                    $instancia->setDescripcion($DescripcionAc);
                    $instancia->setPrecio($PrecioAc);
                    $instancia->setCantidad($CantidadAc);
                    $instancia->ActualizarEvento($pk_eventos);
        
                    echo '<script>
                    //alert("Se ha cargado el evento con éxito.");
                    //window.location.href = "../Views/panel.php"; // Redirige a panel.php
                    </script>';
                } catch (PDOException $e) {
                    echo "Error al actualizar el evento: " . $e->getMessage();
                }
        }

         
    }
    if(isset($_POST["EliminarEvento"]) && $_POST["EliminarEvento"]=="true" && isset($_POST["pkEvento"])){
        $instancia2->BorrarListado($_POST["pkEvento"]);
        //$instancia3->BorrarCompradores($_POST["pkEvento"]);
        $instancia->BorrarEvento($_POST["pkEvento"]);
        $response = array('message' => 'Evento eliminado con éxito');
        header('Content-Type: application/json');
        echo json_encode($response);
    }  
     
    
     

     
}
 





if(isset($_GET["EliminarEvento"]) && $_GET["EliminarEvento"]=="true" && isset($_GET["pkEvento"])){
  
    $instancia->BorrarEvento($_GET["pkEvento"]);
    $response = array('message' => 'Evento eliminado con éxito');
    header('Content-Type: application/json');
    echo json_encode($response);
} 
       
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
  
    

 












 








if($_SERVER["REQUEST_METHOD"] == "GET"){
    if(isset($_GET["TraerEventos"]) && $_GET["TraerEventos"]=="true"){
        $instancia->ObtenerEventos();
    }
    if(isset($_GET["Listado"]) && $_GET["Listado"]=="true" && isset($_GET["pkEvento"])){
        $resultado = $instancia2->ObtenerActoresPorId($_GET["pkEvento"]);
        $bool = $instancia2->ConsultarListado($_GET["pkEvento"]);
       
    }
    
    if(isset($_GET["accion"]) && $_GET["accion"]=="modificar" && isset($_GET["pkEvento"])){
        $instancia->ObtenerEventosPorId($_GET["pkEvento"]);
    }

    if(isset($_GET["accion"]) && $_GET["accion"]=="eliminar" && isset($_GET["pkEvento"])){
        $pkevento = $_GET["pkEvento"];
        $instancia->BorrarEvento($pkevento);

    }
    
 
}


 
    
unset($db);
unset($instancia);
unset($instancia2);
 


   
 
     

 


   
    
 
 

  


 





?>
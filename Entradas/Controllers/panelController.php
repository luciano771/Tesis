<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");

require '../vendor/autoload.php'; // Carga la biblioteca Spout
include '../Models/ArticulosModel.php';
include '../Models/usuariosModel.php';



$db = new conexion();
$instancia = new ArticulosModel($db);
$instancia2 = new usuariosModel($db);
 


if($_SERVER["REQUEST_METHOD"] == "POST"){

    if (isset($_POST['Titulo']) && isset($_POST['img']) && isset($_POST['Descripcion']) && isset($_POST['Cantidad']) && isset($_POST['Precio'])  && isset($_POST['accion']) && $_POST['accion']==='agregar') {
        
        $Titulo= $_POST['Titulo'] ?? '';
        $Descripcion= $_POST['Descripcion'] ?? '';
        $Precio = $_POST['Precio'] ?? '';
        $Cantidad = $_POST['Cantidad'] ?? '';
        $img = $_POST['img'] ?? '';
 

            try {
                $instancia->setTitulo($Titulo);
                $instancia->setDescripcion($Descripcion);
                $instancia->setCantidad($Cantidad);
                $instancia->setPrecio($Precio);
                $instancia->setImg($img);
                $instancia->insertarArticulos();

            } catch (PDOException $e) {
                echo "Error al insertar el evento: " . $e->getMessage();
            }

          
       
 

        echo '<script>
        //alert("Se ha cargado el evento con éxito.");
        window.location.href = "../Views/panel.php"; // Redirige a panel.php
        </script>';



    } 



    
   if (isset($_POST['Titulo'], $_POST['img'], $_POST['Descripcion'], $_POST['Cantidad'], $_POST['Precio'], $_POST['accion']) && $_POST['accion'] === 'actualizar') {
    $TituloAc = $_POST['Titulo'] ?? '';
    $DescripcionAc = $_POST['Descripcion'] ?? '';
    $PrecioAc = $_POST['Precio'] ?? '';
    $CantidadAc = $_POST['Cantidad'] ?? '';
    $img = $_POST['img'] ?? '';
    $pk_eventos = $_POST['pk_articulos'] ?? '';
    
    echo $pk_eventos;

    try {
        $instancia->setTitulo($TituloAc);
        $instancia->setDescripcion($DescripcionAc);
        $instancia->setCantidad($CantidadAc);
        $instancia->setPrecio($PrecioAc);
        $instancia->setImg($img);
        $instancia->ActualizarArticulo($pk_eventos);

        // Posiblemente redirigir a una página de éxito después de la actualización
        echo '<script>window.location.href = "../Views/panel.php";</script>';
    } catch (PDOException $e) {
        echo "Error al actualizar el artículo: " . $e->getMessage();
    }
}

    



}


if($_SERVER["REQUEST_METHOD"] == "GET"){
    if(isset($_GET["TraerEventos"]) && $_GET["TraerEventos"]=="true"){
        $instancia->ObtenerArticulos();
    }
    
    if(isset($_GET["accion"]) && $_GET["accion"]=="modificar" && isset($_GET["pk_articulo"])){
        $pkevento = $_GET["pk_articulo"];
        $instancia->ObtenerArticulosPorId($pkevento);
    }

    if(isset($_GET["accion"]) && $_GET["accion"]=="eliminar" && isset($_GET["pk_articulo"])){
        $pkevento = $_GET["pk_articulo"];
        $instancia->BorrarArticulo($pkevento);
    }
    
 
}


 
    
unset($db);
unset($instancia);
unset($instancia2);
 


   
 
     

 


   
    
 
 

  


 





?>
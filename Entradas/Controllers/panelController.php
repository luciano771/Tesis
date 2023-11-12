<?php
require '../vendor/autoload.php'; // Carga la biblioteca Spout
 
 
include '../Models/ArticulosModel.php';
include '../Models/usuariosModel.php';



$db = new conexion();
$instancia = new ArticulosModel($db);
$instancia2 = new usuariosModel($db);
 
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

 
    if (isset($data['accion']) && $data['accion'] == 'agregar') {
        // Accede a la lista de artículos
        $articulos = $data['articulos'];


        foreach ($articulos as $articulo) {
            // Accede a los campos individuales del artículo
            $nombre = $articulo['nombre'];
            $descripcion = $articulo['descripcion'];
            $precio = $articulo['precio'];
            $cantidad = $articulo['cantidad'];
            $imagen = $articulo['imagen'];


            try {
                $instancia->setTitulo($nombre);
                $instancia->setDescripcion($descripcion);
                $instancia->setPrecio($precio);
                $instancia->setCantidad($cantidad);
                $instancia->setImg('../imagenes/' . $imagen);
                $instancia->insertarArticulos();
            } catch (PDOException $e) {
                echo "Error al insertar el evento: " . $e->getMessage();
            }
        }
        $respuesta = ['mensaje' => 'Articulos agregados con exito'];
        echo json_encode($respuesta);
    } else {
        // En caso de que la acción no sea "agregar"
        $respuesta = ['mensaje' => 'Accion no valida'];
        echo json_encode($respuesta);
    }




    if (isset($data['accion']) && $data['accion'] == 'actualizar') {
 
        $articulos = $data['articulos'];


        foreach ($articulos as $articulo) {
    
            $pk_articulo = $articulo['pk_articulo'];
            $nombre = $articulo['nombre'];
            $descripcion = $articulo['descripcion'];
            $precio = $articulo['precio'];
            $cantidad = $articulo['cantidad'];

            $imagen = isset($articulo['imagen']) ? $articulo['imagen'] : null;

            if($imagen !=null || $imagen!= ''){

                try {
                    $instancia->setTitulo($nombre);
                    $instancia->setDescripcion($descripcion);
                    $instancia->setPrecio($precio);
                    $instancia->setCantidad($cantidad);
                    $instancia->setImg('../imagenes/' . $imagen);
                    $instancia->ActualizarArticulo($pk_articulo);
                } catch (PDOException $e) {
                    echo "Error al insertar el evento: " . $e->getMessage();
                }
            }else{
                try {
                    $instancia->setTitulo($nombre);
                    $instancia->setDescripcion($descripcion);
                    $instancia->setPrecio($precio);
                    $instancia->setCantidad($cantidad);
                    $instancia->ActualizarArticulo($pk_articulo);
                } catch (PDOException $e) {
                    echo "Error al insertar el evento: " . $e->getMessage();
                }
            }
        }
        $respuesta = ['mensaje' => 'Articulos actualizada con exito'];
        echo json_encode($respuesta);
    } else {
        // En caso de que la acción no sea "agregar"
        $respuesta = ['mensaje' => 'Accion no valida'];
        echo json_encode($respuesta);
    }
    

    if (isset($data['accion']) && $data['accion'] == 'ofertas'){
        
        $ofertas = $data['ofertas'];


        foreach ($ofertas as $oferta) {
            // Accede a los campos individuales del artículo
            $titulo = $oferta['titulo'];
            $mensaje = $oferta['mensaje'];
            $instancia2->campaña($titulo,$mensaje);
 
        }
            

    }else{
        $respuesta = ['mensaje' => 'Accion no valida'];
        echo json_encode($respuesta);
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
<?php

session_start();
 
include '../Models/ArticulosModel.php';
$db = new conexion();
$instancia = new ArticulosModel($db);
 
 
     
    if (isset($_GET['consultarArticulos']) && $_GET['consultarArticulos'] == 'true') {
        $instancia->ObtenerEventos();
    } 
     
     
    if (isset($_GET['finalizarcompra']) && $_GET['finalizarcompra'] == 'true') {
        $instancia->ObtenerEventos();
    } 
    

 
 
 
 

unset($db);
unset($instancia);
 
 

?>

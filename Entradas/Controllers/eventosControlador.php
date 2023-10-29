<?php

include '../Models/SessionesModel.php';
include '../Models/EventosModel.php';
$db = new conexion();
$instancia = new EventosModel($db);
$instancia2 = new SessionesModel($db);
  
if (isset($_GET['consultarEventos']) && $_GET['consultarEventos'] == 'true') {
    $instancia->ObtenerEventos();
} 
 
 
unset($db);
unset($instancia);
unset($instancia2);
 

?>

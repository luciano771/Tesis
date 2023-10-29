<?php

include '../Models/SessionesModel.php';
$db = new conexion();
$instancia = new SessionesModel($db);

$listado = $instancia->SessionListado();

// Devuelve los datos como JSON
header('Content-Type: application/json');
echo json_encode($listado);
 
unset($db);
unset($instancia);

?>
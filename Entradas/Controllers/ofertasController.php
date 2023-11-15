<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");

session_start();
 
include '../Models/ArticulosModel.php';
include '../Models/usuariosModel.php';
include '../Models/pedidoModel.php';
$db = new conexion();
 
$instancia2 = new usuariosModel($db);
 
 
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 
   $titulo = $_POST['Titulo'];
   $descripcion = $_POST['Descripcion'];
    $instancia2->campaña($titulo,$descripcion);
    echo '<script>window.location.href = "../Views/panel.php";</script>';
}



 
unset($db);
 
unset($instancia2);
 

 

?>
v
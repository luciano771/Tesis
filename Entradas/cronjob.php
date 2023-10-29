<?php


include_once('Models/SessionesModel.php');
include_once('Models/Conexion.php');

$db = new conexion();
$instancia2 = new SessionesModel($db);

if(isset($_GET["BorrarSessiones"])){
    $instancia2->CronJob();
}

if(isset($_GET["AumentarSession"])){
    $instancia2->AumentarSession();
}





?>
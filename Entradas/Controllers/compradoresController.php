<?php

include_once('../Models/CompradoresModel.php');
include_once('../Models/SessionesModel.php');
include_once('../Models/Conexion.php');

$db = new conexion();
$instancia = new CompradoresModel($db);
$instancia2 = new SessionesModel($db);


 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    // $nombre = $_POST["nombre"];
    // $apellido = $_POST["apellido"];
    // $telefono = $_POST["Telefono"];
    // $dni = $_POST["dni"];
    $dni_actor = $_POST["dni_actor"];
    //$cantidad_entradas = $_POST["cantidad_entradas"];

    
    
    $instancia->setEmail($email);
    // $instancia->setNombre($nombre);
    // $instancia->setApellido($apellido);
    // $instancia->setTelefono($telefono);
    // $instancia->setDni($dni);
    $instancia->setDni_actor($dni_actor);
    //$instancia->setCantidadEntradas($cantidad_entradas);

    
 
    $id = $_POST['pk_eventos'];
    $instancia ->setFk_eventos($id);
    if($instancia->insertarComprador()){
        $instancia2->BorrarSession();
    }

    if (isset($_POST['activo']) && $_POST['activo'] == 'no') {
        $instancia2->BorrarSession();
    } 

    
     
   
}
 

// if(isset($_GET["actor"]) && $_GET["actor"] == "true") {
//     try {
//         $actores = $instancia->ApellidoNombre($dni_actor,$fk_evento);
//         echo json_encode($actores);
//         echo ($actores[0]["nombre"]." ".$actores[0]["apellido"]); // Supongo que $actores es un arreglo, así que lo convertimos a JSON
//     } catch (PDOException $e) {
//         // En caso de error en la conexión o consulta
//         echo 'Error: ' . $e->getMessage();
//     }
// }





 

 

unset($db);
unset($instancia);
unset($instancia2);
?>
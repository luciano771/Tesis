<?php


include '../Models/EventosModel.php';
include '../Models/ActoresModel.php';
include '../Models/SessionesModel.php';


$db = new conexion();
$instancia = new EventosModel($db);
$instancia2 = new SessionesModel($db);

date_default_timezone_set("America/Argentina/Buenos_Aires");


if(isset($_GET['ESTADOSESSION']) && $_GET['ESTADOSESSION'] == 'ESTADO' && isset($_GET['pkeventos'])) {
    
    
    $resultado=false;
    $bool = $instancia2->SessionTrue();
    if($bool==true){
        $resultado = true;
    }
    echo trim($resultado);

    
}     

if(isset($_GET['SESSION']) && $_GET['SESSION'] == 'ESTADO') {
    $estado ='true';
    if(!isset($_SESSION['estado']) || $_SESSION['estado']=='false') {
        $estado ='false';
    }
    echo trim($estado);
}     

    if(isset($_GET["pk_eventos"]) && isset($_GET['ingreso'])=='true'){
    $fechaActual = date("Y-m-d H:i:s");
    $fecha = $instancia->Obtenerfecha($_GET["pk_eventos"]);
    $fecha_inicio = $fecha["fecha_inicio"];
    $fecha_fin = $fecha["fecha_fin"];
    if ($fechaActual>=$fecha_inicio && $fechaActual<=$fecha_fin) {
        $instancia2->setPkevento($_GET["pk_eventos"]);
        $instancia2->InsertarSession();
        $sessionOrden = $instancia2->SessionFilas();
        if($sessionOrden) {
            header('Location: ../Views/reservar.php?pk_eventos=' . $_GET["pk_eventos"]);
        }
        else{
            header('Location: ../Views/Sala.php?pk_eventos=' . $_GET["pk_eventos"]); // en salas verifico el orden de las sessiones
        }   //o una vez el comprador halla efectuado su compra se llama d
    } else {
        // Haz algo si la fecha de inicio no es posterior a la fecha actual
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Página Actual</title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
                  integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
                  crossorigin="anonymous">
        </head>
        <body>
        <?php
        if ($fechaActual < $fecha_inicio ) {
            echo '<h4>El evento inicia el ' . $fecha_inicio . '</h4>';
        } elseif ($fecha_fin < $fechaActual) {
            echo '<h4>El evento finalizó el ' . $fecha_inicio . '</h4>';
        }
        ?>
        <!-- Tu contenido de página actual aquí -->

        <!-- Botón "Volver" que redirige al usuario a la página anterior -->
        &nbsp; <a href="javascript:history.back()" class="btn btn-primary">Volver</a>
        </body>
        </html>
        <?php
    }
}

if (isset($_GET['VerificarOrden']) && $_GET['VerificarOrden'] == 'true' && isset($_GET['pk_eventos'])) {
    $instancia2->setPkevento($_GET["pk_eventos"]);
    $sessionOrden = $instancia2->SessionFilas();
    if($sessionOrden) {
        echo'true';
    }else{echo'false';}
} 

 

if (isset($_POST['activo']) && $_POST['activo'] == 'no') {
    $_SESSION['estado'] = 'false'; 
    $instancia2->BorrarSession();
     
    
}




 
if(isset($_GET['pkevento']) && isset($_GET['fila'])){
    $instancia2->setPkevento($_GET['pkevento']);
    $listado = $instancia2->FilaDelante();
    header('Content-Type: application/json');
    echo json_encode($listado);
  
}
 
 
 


unset($db);
unset($instancia);
unset($instancia2);

?>
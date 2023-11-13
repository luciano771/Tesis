<?php

session_start();
 
include '../Models/ArticulosModel.php';
include '../Models/usuariosModel.php';
include '../Models/pedidoModel.php';
$db = new conexion();
$instancia = new ArticulosModel($db);
$instancia2 = new usuariosModel($db);
$instancia3 = new pedidoModel($db);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
     
    if (isset($_GET['consultarArticulos']) && $_GET['consultarArticulos'] == 'true') {
        $instancia->ObtenerArticulos();
    } 

}
 

 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 
    $json_data = file_get_contents('php://input');

 
    $data = json_decode($json_data, true);

    
    if ($data) {
    
        $name = isset($data['name']) ? $data['name'] : null;
        $email = isset($data['email']) ? $data['email'] : null;

        $carrito = isset($data['carrito']) ? $data['carrito'] : array();

        
        $respuesta = array(
            'mensaje' => 'Datos recibidos correctamente',
            'name' => $name,
            'email' => $email,
            'carrito' => $carrito
        );
        
        $instancia2->setNombre( $respuesta['name']);
        $instancia2->setEmail( $respuesta['email']);
        
        $instancia3->setCarrito($respuesta['carrito']);
        
        foreach ($respuesta['carrito'] as $item) {
            $stockDisponible = $instancia3->verificarStock($item['pk_articulo'], $item['cantidad']);
            if ($stockDisponible) {
                $instancia2->InsertarUsuario();
                $instancia3->InsertarPedido();
                $instancia3->actualizarStock($item['pk_articulo'], $item['cantidad']);
            } else {
                echo "No hay suficiente stock para el artículo con ID: " . $item['pk_articulo'];
                return false;
            }
        }
        $instancia3->MailDeCompra();
        echo json_encode($respuesta);
    } else {
        echo json_encode(array('mensaje' => 'Error al recibir datos'));
    }
}



 
unset($db);
unset($instancia);
unset($instancia2);
unset($instancia3);

 

?>

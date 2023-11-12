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
    // Obtener el contenido JSON de la solicitud
    $json_data = file_get_contents('php://input');

    // Decodificar el JSON en un array asociativo
    $data = json_decode($json_data, true);

    // Verificar si se han recibido datos
    if ($data) {
        // Acceder a los datos
        $name = isset($data['name']) ? $data['name'] : null;
        $email = isset($data['email']) ? $data['email'] : null;

        $carrito = isset($data['carrito']) ? $data['carrito'] : array();

        // Realizar las operaciones necesarias con los datos
        // Por ejemplo, puedes insertar los datos en una base de datos aquí

        // Devolver una respuesta al cliente en formato JSON
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

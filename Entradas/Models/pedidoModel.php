<?php
require_once 'Conexion.php';

 
 

class pedidoModel {
   
    private $db;
    private $carrito = [];
    private $fecha;
    private $total;
    private $cantidad;
    private $fk_articulos;
    private $fk_usuario;
    public function __construct($db) {
        $this->db = $db;
    }
    public function setInstancia2() { 
        $this->instancia2 = new usuariosModel($this->db);
    }
     
    public function setCarrito($carrito){
        $this->carrito = $carrito;
    }
    public function getCarrito(){
        return $this->carrito;
    }
    public function setFecha(){
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        $this->fecha = date('Y-m-d H:i:s');
    }

    public function getFecha(){
        return $this->fecha;
    }

    public function setTotal($total){
        $this->total = $total;
    }

    public function getTotal(){
        return $this->total;
    }
    public function setFk_articulos($fk_articulos){
        $this->fk_articulos = $fk_articulos;
    }

    public function  getFk_articulos(){
        return $this->fk_articulos;
    }

    public function setFk_usuario($fk_usuario){
        $this->fk_usuario = $fk_usuario;
    }

    public function  getFk_usuario(){
        return $this->fk_usuario;
    }

    public function setCantidad($cantidad){
        $this->cantidad = $cantidad;
    }

    public function  getCantidad(){
        return $this->cantidad;
    }


    public function InsertarPedido(){
        $this->setFecha();
        $this->setFk_usuario($this->ultimaPk_usuarios());
        foreach ($this->carrito as $producto) {

            $pk_articulo = $producto['pk_articulo'];
            $precio = $producto['precio'];
            $cantidad = $producto['cantidad'];
            
            $this->setFk_articulos($pk_articulo);
            $this->setTotal($precio);
            $this->setCantidad($cantidad);
            

            try {
                $sql = "INSERT INTO pedidos (fecha,total,cantidad,fk_articulos,fk_usuario) VALUES (?, ? ,? ,?, ?)";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$this->fecha, $this->total,$this->cantidad, $this->fk_articulos, $this->fk_usuario]);
                echo "pedidos insertados";
            } catch (PDOException $e) {
                echo "Error al insertar el articulo: " . $e->getMessage();
            }
        }        
    }


    public function ProcesarPedido(){
        foreach ($this->carrito as $item) {
            $stockDisponible = $this->verificarStock($item['pk_articulo'], $item['cantidad']);
            if ($stockDisponible) {
                return true;
            } else {
                echo "No hay suficiente stock para el artículo con ID: " . $item['pk_articulo'];
                return false;
            }
        }
    }

    private function ultimaPk_usuarios(){
        try {
            $consulta = "SELECT max(pk_usuario) FROM usuarios";
            $stmt = $this->db->prepare($consulta);
            $stmt->execute();
            $last_insert_id = $stmt->fetchColumn();            
        }
        catch (PDOException $e) {
            // En caso de error en la conexión o consulta
            echo 'Error: ' . $e->getMessage();
            $last_insert_id = null;
        }        
        return $last_insert_id;
    }

    public function verificarStock($pk_articulo, $cantidad) {
        try {
            // Consultar el stock disponible para el artículo en la base de datos
            $consulta = "SELECT cantidad FROM articulos WHERE pk_articulos = ?";
            $stmt = $this->db->prepare($consulta);
            $stmt->execute([$pk_articulo]);
            $stockDisponible = $stmt->fetchColumn();
    
            // Verificar si hay suficiente stock
            return $stockDisponible >= $cantidad;
    
        } catch (PDOException $e) {
            // En caso de error en la conexión o consulta
            echo 'Error: ' . $e->getMessage();
            return false; // Considera manejar el error de manera más adecuada en tu aplicación
        }
    }
    

    public function actualizarStock($pk_articulo, $cantidad) {
        try {
            $consulta = "SELECT cantidad FROM articulos WHERE pk_articulos = ?";
            $stmt = $this->db->prepare($consulta);
            $stmt->execute([$pk_articulo]);

            $stockDisponible = $stmt->fetchColumn();
            $cantidadModificada = $stockDisponible - $cantidad;

            try {
                $sql = "UPDATE articulos SET cantidad = ? WHERE pk_articulos = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$cantidadModificada, $pk_articulo]);
                echo "cantidad actualizada";
            } catch (PDOException $e) {
                echo "Error al actualizar cantidad: " . $e->getMessage();
            }
 
        } catch (PDOException $e) {
            // En caso de error en la conexión o consulta
            echo 'Error: ' . $e->getMessage();
            return false; // Considera manejar el error de manera más adecuada en tu aplicación
        }
    }

    
    public function MailDeCompra(){
        $pk_usuario = $this->ultimaPk_usuarios();
        try {
            // Seleccionar información necesaria de la base de datos
            $sql = "SELECT u.nombreapellido, u.email, p.fecha, p.total, p.cantidad, a.titulo FROM pedidos p, usuarios u, articulos a WHERE p.fk_usuario = u.pk_usuario AND a.pk_articulos = p.fk_articulos AND pk_usuario = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$pk_usuario]);
            
            // Obtener los resultados
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Verificar si hay resultados antes de enviar el correo
            if ($resultados) {
                // Procesar los resultados y construir el contenido del correo
                $contenidoCorreo = "Detalles de la compra realizada por el usuario:\n";
                $contenidoCorreo .= "Nombre del Usuario: " . $resultados[0]['nombreapellido'] . "\n";            
                foreach ($resultados as $resultado) {
                    $contenidoCorreo .= "Fecha: " . $resultado['fecha'] . "\n";
                    $contenidoCorreo .= "Producto: " . $resultado['titulo'] . "\n";
                    // Agregar más detalles según sea necesario
                    // ...
                    $contenidoCorreo .= "Unidades: " . $resultado['cantidad'] . "\n\n";
                    $contenidoCorreo .= "Total: " . $resultado['total'] . "\n\n";
                }
    
                // Configurar los encabezados del correo
                $para = 'pereyraluciano771@gmail.com';  // Cambiar al correo del administrador
                $titulo = 'Compra realizada por un usuario';
                $cabeceras = 'From: Peumayen@gmail.com' . "\r\n" .
                             'Reply-To: Peumayen2@gmail.com' . "\r\n" .
                             'X-Mailer: PHP/' . phpversion();
    
                // Enviar el correo al administrador
                $enviado = mail($para, $titulo, $contenidoCorreo, $cabeceras);
    
                // Verificar si el correo se envió correctamente
                if ($enviado) {
                    echo "Correo enviado exitosamente al administrador";
                } else {
                    echo "Error al enviar el correo al administrador";
                }
            } else {
                echo "No se encontraron resultados para el usuario con ID: " . $pk_usuario;
            }
    
        } catch (PDOException $e) {
            echo "Error al obtener detalles de compra: " . $e->getMessage();
        }
    }
    




    public function MailDeCompraCliente(){
        $pk_usuario = $this->ultimaPk_usuarios();
        try {
            // Seleccionar información necesaria de la base de datos
            $sql = "SELECT u.nombreapellido, u.email, p.fecha, p.total, p.cantidad, a.titulo FROM pedidos p, usuarios u, articulos a WHERE p.fk_usuario = u.pk_usuario AND a.pk_articulos = p.fk_articulos AND pk_usuario = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$pk_usuario]);
            
            // Obtener los resultados
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Verificar si hay resultados antes de enviar el correo
            if ($resultados) {
                // Procesar los resultados y construir el contenido del correo
                $contenidoCorreo = "Detalles de la compra:\n";
                $contenidoCorreo .= "Nombre del Usuario: " . $resultados[0]['nombreapellido'] . "\n";     
                $TotalGeneral = 0;       
                foreach ($resultados as $resultado) {
                    $contenidoCorreo .= "Fecha: " . $resultado['fecha'] . "\n";
                    $contenidoCorreo .= "Producto: " . $resultado['titulo'] . "\n";
                    // Agregar más detalles según sea necesario
                    // ...
                    $contenidoCorreo .= "Unidades: " . $resultado['cantidad'] . "\n\n";
                    $contenidoCorreo .= "Total: " . $resultado['total'] . "\n\n";
                    $TotalGeneral += $resultado['total'];
                }

                $contenidoCorreo .= "Total: " . $TotalGeneral . "\n\n";
                $contenidoCorreo .= "Detalles de Transferencia:". "\n\n"."CBU:017020466000000878652".  "\n\n"."ALIAS:peumayen.mp"."\n\n"."CUIL:20-45856987-8"."\n\n";
                $contenidoCorreo .= "Contacto: 1154458569";
    
                // Configurar los encabezados del correo
                $para = $resultados[0]['email'];  // Cambiar al correo del administrador
                $titulo = 'Detalle de compra';
                $cabeceras = 'From: Peumayen@gmail.com' . "\r\n" .
                             'Reply-To: Peumayen2@gmail.com' . "\r\n" .
                             'X-Mailer: PHP/' . phpversion();
    
                // Enviar el correo al administrador
                $enviado = mail($para, $titulo, $contenidoCorreo, $cabeceras);
    
                // Verificar si el correo se envió correctamente
                if ($enviado) {
                    echo "Correo enviado exitosamente al administrador";
                } else {
                    echo "Error al enviar el correo al administrador";
                }
            } else {
                echo "No se encontraron resultados para el usuario con ID: " . $pk_usuario;
            }
    
        } catch (PDOException $e) {
            echo "Error al obtener detalles de compra: " . $e->getMessage();
        }
    }
    



}
?>

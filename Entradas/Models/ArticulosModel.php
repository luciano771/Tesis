<?php
require_once 'Conexion.php'; 

class ArticulosModel  
{   
    private $db;
    private $pk_articulos;
    private $titulo;
    private $descripcion;
    private $Precio;
    private $cantidad;
 
    private $img;

    public function __construct($db) {
        $this->db = $db;
    }
    public function getPkArticulos() {
        return $this->pk_articulos;
    }

    public function setPkArticulos($pk_articulos) {
        $this->pk_articulos = $pk_articulos;
    }

    public function getTitulo() {
        return $this->titulo;
    }

    public function setTitulo($titulo) {
        $this->titulo = $titulo;
    }

    public function getDescripcion() {
        return $this->descripcion;
    }

    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    public function getPrecio() {
        return $this->Precio;
    }

    public function setPrecio($Precio) {
        $this->Precio = $Precio;
    }

    public function getCantidad() {
        return $this->cantidad;
    }

    public function setCantidad($cantidad) {
        $this->cantidad = $cantidad;
    }

  

    public function getImg() {
        return $this->img;
    }

    public function setImg($img) {
        $this->img = $img;
    }

 
    
    public function insertarArticulos()
    {
        try {
            $sql = "INSERT INTO articulos (titulo, descripcion, Precio, cantidad,img) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$this->titulo, $this->descripcion, $this->Precio, $this->cantidad, $this->img]);
            echo "articulos insertados";
        } catch (PDOException $e) {
            echo "Error al insertar el evento: " . $e->getMessage();
        }
    }
    
     
 
    public function ObtenerArticulos(){

        try{
        $consulta = "SELECT * FROM articulos";
        $stmt = $this->db->query($consulta);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($resultados);
        } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
        }
    }   

    public function ObtenerArticulosDescripcion($descripcion){

        try{
        $consulta = "SELECT * FROM articulos WHERE descripcion LIKE :descripcion";
        $stmt = $this->db->prepare($consulta);
        $descripcionParam = '%' . $descripcion . '%'; // Adding wildcards here
        $stmt->bindParam(':descripcion', $descripcionParam, PDO::PARAM_STR); 
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if($resultados ==null or $resultados == ""){
            $resultados = "No se encuentra el producto que busca, pruebe con otra palabra.";
        }
        header('Content-Type: application/json');
        echo json_encode($resultados);
        } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
        }
    }   

    public function FinalizarCompra(){

        try{
        $consulta = "SELECT * FROM articulos";
        $stmt = $this->db->query($consulta);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($resultados);
        } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
        }
    }   



    public function ObtenerArticulosPorId($pk_articulos){

        try{
        $consulta = "SELECT * FROM articulos where pk_articulos= :pk_articulos";
        $stmt = $this->db->prepare($consulta);
        $stmt->bindParam(':pk_articulos', $pk_articulos, PDO::PARAM_INT); 
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($resultados);
        } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
        }
    }   

 

    public function ActualizarArticulo($pk_articulo){
        try {
            $consulta =  "UPDATE articulos 
            SET 
                titulo = :titulo, 
                descripcion = :descripcion, 
                Precio = :precio, 
                cantidad = :cantidad";
    
            // Verificar si img no es null antes de incluirlo en la consulta
            if ($this->img !== null) {
                $consulta .= ", img = :img";
            }
    
            $consulta .= " WHERE pk_articulos = :pk_articulo";
    
            $stmt = $this->db->prepare($consulta);
            $stmt->bindParam(':pk_articulo', $pk_articulo, PDO::PARAM_INT); 
            $stmt->bindParam(':titulo', $this->titulo, PDO::PARAM_STR);
            $stmt->bindParam(':descripcion', $this->descripcion, PDO::PARAM_STR);
            $stmt->bindParam(':precio', $this->Precio, PDO::PARAM_STR);
            $stmt->bindParam(':cantidad', $this->cantidad, PDO::PARAM_STR);
    
            // Verificar si img no es null antes de ejecutar la consulta
            if ($this->img !== null) {
                $stmt->bindParam(':img', $this->img, PDO::PARAM_STR);
            }
    
            if ($stmt->execute()) {
                echo "articulo actualizado correctamente";
            } else {
                echo "Error al actualizar el articulo: " . $stmt->errorInfo()[2];
            }
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
    
    
    public function BorrarArticulo($pk_articulos){
        $bool = true;
        try {
            $consulta = "SELECT img FROM articulos WHERE pk_articulos= :pk_articulos";
            $stmt = $this->db->prepare($consulta);
            $stmt->bindParam(':pk_articulos', $pk_articulos, PDO::PARAM_INT); 
            $stmt->execute();
            $rutaImagen = $stmt->fetch();
            $rutaArchivo = $rutaImagen['img'];
            
            if (unlink($rutaArchivo)) {
                echo 'El archivo ha sido eliminado exitosamente.';
            } else {
                echo 'No se pudo eliminar el archivo.';
            }
             
        } catch (PDOException $e) {
            // En caso de error en la conexión o consulta
            echo 'Error: ' . $e->getMessage();
            $bool = false;
        }

        



        try {
            $consulta = "DELETE FROM articulos where pk_articulos= :pk_articulos";
            $stmt = $this->db->prepare($consulta);
            $stmt->bindParam(':pk_articulos', $pk_articulos, PDO::PARAM_INT); 
            $stmt->execute();
        } catch (PDOException $e) {
            // En caso de error en la conexión o consulta
            echo 'Error: ' . $e->getMessage();
            $bool = false;
        }

        return $bool;
    }



    
}

?>

 
<?php
 
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

 

    
    
    
    public function BorrarArticulo($pk_articulos){
        
         
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
 
    }



    
    public function ActualizarArticulo($pk_articulo) {
        try {
            $consulta =  "UPDATE articulos 
                          SET 
                              titulo = :titulo, 
                              descripcion = :descripcion, 
                              Precio = :precio, 
                              cantidad = :cantidad";
    
            // Verificar si img no es null antes de incluirlo en la consulta
            if (!empty($this->img)) {
                $consulta .= ", img = :img";
            }
    
            $consulta .= " WHERE pk_articulos = :pk_articulo";
    
            // Assuming $conexion is your database connection
            $stmt = $this->db->prepare($consulta);
    
            // Bind parameters
            $stmt->bindParam(':titulo', $this->titulo);
            $stmt->bindParam(':descripcion', $this->descripcion);
            $stmt->bindParam(':precio', $this->Precio);
            $stmt->bindParam(':cantidad', $this->cantidad);
            $stmt->bindParam(':pk_articulo', $pk_articulo);
    
            // Bind image if not empty
            if (!empty($this->img)) {
                $stmt->bindParam(':img', $this->img);
            }
    
            // Execute the query
            $stmt->execute();
            echo 'se pudo';
            // Rest of your code handling success or errors
        } catch (PDOException $e) {
            echo 'nose pudo';
        }
    }
    




    
    


    
}

?>

 
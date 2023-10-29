<?php
require_once 'Conexion.php'; 

class EventosModel  
{   

    private $Titulo;
    private $Descripcion;
    private $Fecha_inicio_Guardarbase;
    private $Fecha_fin_Guardarbase;
    private $url_base;
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }
    public function setTitulo($Titulo)
    {
        $this->Titulo = $Titulo;
    }

    public function setDescripcion($Descripcion)
    {
        $this->Descripcion = $Descripcion;
    }

    public function setFechaInicio($Fecha_inicio)
    {
        $this->Fecha_inicio_Guardarbase = $Fecha_inicio;
    }

    public function setFechaFin($Fecha_fin)
    {
        $this->Fecha_fin_Guardarbase = $Fecha_fin;
    }

    public function setUrlBase($url_base)
    {
        $this->url_base = $url_base;
    }

 
    
    public function insertarEvento()
    {
        try {
            $sql = "INSERT INTO eventos (titulo, descripcion, fecha_inicio, fecha_fin, img) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$this->Titulo, $this->Descripcion, $this->Fecha_inicio_Guardarbase, $this->Fecha_fin_Guardarbase, $this->url_base]);
            echo "ARCHIVO insertado";
        } catch (PDOException $e) {
            echo "Error al insertar el evento: " . $e->getMessage();
        }
    }
    
     
 
    public function ObtenerEventos(){

        try{
        $consulta = "SELECT * FROM eventos";
        $stmt = $this->db->query($consulta);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($resultados);
        } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
        }
    }   

    public function ObtenerEventosPorId($pkevento){

        try{
        $consulta = "SELECT * FROM eventos where pk_eventos= :pkevento";
        $stmt = $this->db->prepare($consulta);
        $stmt->bindParam(':pkevento', $pkevento, PDO::PARAM_INT); 
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($resultados);
        } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
        }
    }   

    public function Obtenerfecha($pkevento){

        try{
        $consulta = "SELECT fecha_inicio,fecha_fin FROM eventos where pk_eventos= :pkevento";
        $stmt = $this->db->prepare($consulta);
        $stmt->bindParam(':pkevento', $pkevento, PDO::PARAM_INT); 
        $stmt->execute();
        $resultados = $stmt->fetch();
        return $resultados;
        } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
        return null;
        }
    }  

    public function ActualizarEvento($pkevento){
        try {
            $consulta =  "UPDATE eventos 
            SET 
                titulo = :titulo, 
                descripcion = :descripcion, 
                fecha_inicio = :fecha_inicio, 
                fecha_fin = :fecha_fin";
    
            // Verificar si img no es null antes de incluirlo en la consulta
            if ($this->url_base !== null) {
                $consulta .= ", img = :img";
            }
    
            $consulta .= " WHERE pk_eventos = :pkevento";
    
            $stmt = $this->db->prepare($consulta);
            $stmt->bindParam(':pkevento', $pkevento, PDO::PARAM_INT); 
            $stmt->bindParam(':titulo', $this->Titulo, PDO::PARAM_STR);
            $stmt->bindParam(':descripcion', $this->Descripcion, PDO::PARAM_STR);
            $stmt->bindParam(':fecha_inicio', $this->Fecha_inicio_Guardarbase, PDO::PARAM_STR);
            $stmt->bindParam(':fecha_fin', $this->Fecha_fin_Guardarbase, PDO::PARAM_STR);
    
            // Verificar si img no es null antes de ejecutar la consulta
            if ($this->url_base !== null) {
                $stmt->bindParam(':img', $this->url_base, PDO::PARAM_STR);
            }
    
            if ($stmt->execute()) {
                echo "Evento actualizado correctamente";
            } else {
                echo "Error al actualizar el evento: " . $stmt->errorInfo()[2];
            }
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
    
    
    public function BorrarEvento($pkeventos){
        $bool = true;

        try {
            $consulta = "SELECT img FROM eventos WHERE pk_eventos= :pkevento";
            $stmt = $this->db->prepare($consulta);
            $stmt->bindParam(':pkevento', $pkeventos, PDO::PARAM_INT); 
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
            $consulta = "DELETE FROM eventos where pk_eventos= :pkevento";
            $stmt = $this->db->prepare($consulta);
            $stmt->bindParam(':pkevento', $pkeventos, PDO::PARAM_INT); 
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

 
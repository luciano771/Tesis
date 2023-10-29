<?php
require_once 'Conexion.php';
require '../vendor/autoload.php'; // Carga la biblioteca Spout
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;


class ActoresModel {


    private $xlsxFilePath;
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function setarchivo($xlsxFilePath)
    {
        $this->xlsxFilePath = $xlsxFilePath;
    }
    

    

    public function getpk_eventos(){
        try {
            // Crear una instancia de la conexión a la base de datos
             // Consulta SQL para obtener los valores de la columna "pk_eventos"
            $consulta = "SELECT pk_eventos FROM eventos ORDER BY pk_eventos DESC LIMIT 1";
            $stmt = $this->db->query($consulta);         
            // Obtener el resultado como un valor único (la última pk_eventos)
            $pk_eventos = $stmt->fetchColumn();          
            // Cerrar la conexión a la base de datos
            $db = null;            
            // Devolver los valores de pk_eventos
            return $pk_eventos;
        } catch (PDOException $e) {
            // En caso de error en la conexión o consulta
            echo 'Error: ' . $e->getMessage();
            return null; // Puedes manejar el error de alguna manera adecuada
        }
    }

    public function insertarListado(){

        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->open($this->xlsxFilePath);
        $pk_eventos = $this->getpk_eventos();
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {              
                    $nombres = $row->getCellAtIndex(0)->getValue(); // Columna A
                    $apellido = $row->getCellAtIndex(1)->getValue(); // Columna B
                    $dni = $row->getCellAtIndex(2)->getValue(); // Columna C
                    try {
                        $sql = "INSERT INTO actores (nombre,apellido,dni,fk_eventos) VALUES (?, ?, ?, ?)";
                        $stmt = $this->db->prepare($sql);
                        $stmt->execute([$nombres, $apellido,$dni,$pk_eventos]);                        
                    } catch (PDOException $e) {
                        echo "Error al insertar el evento: " . $e->getMessage();
                    }                         
            }
        } 
        $reader->close();       
    }

    public function CheckearCompra() {
        try {
            $sql = "INSERT INTO actores (compra) VALUES (?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error al insertar el comprador: " . $e->getMessage();
        }
    }

    public function ObtenerActoresPorId($pkevento){

        try{
        $consulta = "SELECT nombre,apellido,dni FROM actores where fk_eventos= :pkevento";
        $stmt = $this->db->prepare($consulta);
        $stmt->bindParam(':pkevento', $pkevento, PDO::PARAM_INT); 
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if($resultados==null){
            $resultados=null;
        }
        return $resultados;
        } catch (PDOException $e) {
        return 'Error: ' . $e->getMessage();
        }
    }   

    
    public function ConsultarListado($pkeventos){
        try {
            // Crear una instancia de la conexión a la base de datos
             // Consulta SQL para obtener los valores de la columna "pk_eventos"
            $consulta = "SELECT * FROM comprador where fk_eventos= :pkevento";
            $stmt = $this->db->prepare($consulta);
            $stmt->bindParam(':pkevento', $pkeventos, PDO::PARAM_INT); 
            $stmt->execute();
            $listado = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // Cerrar la conexión a la base de datos
            $db = null;            
            if($listado ==null || $listado==false){
                $listado=null;
            }
            return $listado;
        } catch (PDOException $e) {
            // En caso de error en la conexión o consulta
            echo 'Error: ' . $e->getMessage();
            return null; // Puedes manejar el error de alguna manera adecuada
        }
    }
    
     
    
    
    

    public function BorrarListado($pkeventos){
        $bool = true;
        try {
            $consulta = "DELETE FROM actores where fk_eventos= :pkevento";
            $stmt = $this->db->prepare($consulta);
            $stmt->bindParam(':pkevento', $pkeventos, PDO::PARAM_INT); 
            $stmt->execute();
             
            return $bool;
        } catch (PDOException $e) {
            // En caso de error en la conexión o consulta
            echo 'Error: ' . $e->getMessage();
            $bool = false;
        }

        return $bool;
    }



    public function insertarListadopk($pkevento){

        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->open($this->xlsxFilePath);
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {              
                    $nombres = $row->getCellAtIndex(0)->getValue(); // Columna A
                    $apellido = $row->getCellAtIndex(1)->getValue(); // Columna B
                    $dni = $row->getCellAtIndex(2)->getValue(); // Columna C
                    try {
                        $sql = "INSERT INTO actores (nombre,apellido,dni,fk_eventos) VALUES (?, ?, ?, ?)";
                        $stmt = $this->db->prepare($sql);
                        $stmt->execute([$nombres, $apellido,$dni,$pkevento]); 
                    } catch (PDOException $e) {
                        echo "Error al insertar el evento: " . $e->getMessage();
                    }                         
            }
        } 
        $reader->close();       
    }


    
     
}


?>
<?php
require_once 'Conexion.php';
class usuariosModel {
    private $db;
    private $email;
    private $contrasena;
    private $celular;
    public function __construct($db) {
        $this->db = $db;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getContrasena() {
        return $this->contrasena;
    }

    public function setContrasena($contrasena) {
        $this->contrasena = $contrasena;
    }

    public function getcelular() {
        return $this->celular;
    }

    public function setcelular($celular) {
        $this->celular = $celular;
    }
 

    public function InsertarUsuario()
    {
        try {
            $sql = "INSERT INTO usuarios (email, contrasena, celular) VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$this->email, $this->contrasena, $this->celular]);
            echo "articulos insertados";
        } catch (PDOException $e) {
            echo "Error al insertar el evento: " . $e->getMessage();
        }
    }
    
    public function ConsultarUsuario(){

        try{
            $consulta = "SELECT * FROM usuarios";
            $stmt = $this->db->query($consulta);
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            header('Content-Type: application/json');
            echo json_encode($resultados);

        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    
    }








}


?>
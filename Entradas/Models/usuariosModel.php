<?php
require_once 'Conexion.php';
class usuariosModel {
    private $db;
    private $email;
    private $nombre;
     
    public function __construct($db) {
        $this->db = $db;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    
 

    public function InsertarUsuario()
    {
        try {
            $sql = "INSERT INTO usuarios (email, nombreapellido) VALUES (?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$this->email, $this->nombre]);
            echo "usuario insertados";
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

    public function ConsultarUsuarioMails(){

        try{
            $consulta = "SELECT DISTINCT(email) FROM usuarios";
            $stmt = $this->db->query($consulta);
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $resultados;
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
            return null;             
        }
     
    }
    public function campaña($titulo,$mensaje){
        $emails = $this->ConsultarUsuarioMails();
        foreach ($emails as $email){
            $para      =  $email['email'];
            $cabeceras = 'From: Peumayen@gmail.com' . "\r\n" .
                'Reply-To: Peumayen2@gmail.com' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();
    
            $enviado = mail($para, $titulo, $mensaje, $cabeceras);
    
            if ($enviado) {
                echo 'Correo enviado a: ' . $email['email'];
            } else {
                echo 'Error al enviar correo a: ' . $email['email'];
            }
        }
    }
    
  
    






}


?>
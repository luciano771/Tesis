<?php
require_once 'Conexion.php';
// require_once 'ActoresModel.php';
 

class CompradoresModel {
    private $db;
    private $email;
    private $nombre;
    private $apellido;
    private $telefono;
    private $dni;
    private $dni_actor;
    private $cantidad_entradas;
    private $fk_eventos;
    private $TokenEntrada;

    public function __construct($db) {
        $this->db = $db;
    }
    public function setEmail($email) {
        $this->email = $email;
    }
    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }
    public function setApellido($apellido) {
        $this->apellido = $apellido;
    }
    public function setDni($dni) {
        $this->dni = $dni;
    }
    public function setTelefono($telefono) {
        $this->telefono = $telefono;
    }
    public function setDni_actor($dni_actor) {
        $this->dni_actor = $dni_actor;
    }
    public function setCantidadEntradas($cantidad_entradas) {
        $this->cantidad_entradas = $cantidad_entradas;
    }
    public function setTokenEntrada() {
        $this->TokenEntrada = $this->ConsultarToken();
    }
    public function setFk_eventos($fk_eventos) {
        $this->fk_eventos = $fk_eventos;
    }
    
    public function insertarComprador() {
        $resultado="";
        $bool=true;
        $this->setTokenEntrada();
        try {
            // CONFIGURAR E Iniciar una transacción con el nivel de aislamiento READ COMMITTED
            $this->db->exec("SET TRANSACTION ISOLATION LEVEL READ COMMITTED");
            $this->db->beginTransaction();
            // Bloquear la fila del actor con el DNI correspondiente
            $consulta = "SELECT compra FROM actores WHERE dni = :dni_actor AND fk_eventos = :fk_eventos";
            $stmt = $this->db->prepare($consulta);
            $stmt->bindParam(':dni_actor', $this->dni_actor, PDO::PARAM_STR);
            $stmt->bindParam(':fk_eventos', $this->fk_eventos, PDO::PARAM_INT); // Agregamos esto
            $stmt->execute();
            $this->db->commit();
            $compra = $stmt->fetchColumn();
            if ($compra === false || $compra === null) {
                // El DNI no existe en la tabla de actores, muestra un mensaje de error o toma medidas adecuadas
                echo '<script>
                alert("El dni no esta registrado","Feeling Danzas");
                window.location.href = "../Views/Eventos.html?pk_eventos=' . $this->fk_eventos . '";
                </script>';
                $resultado="El dni no esta registrado";
            } elseif ($compra == 1) {
                // Ya se ha realizado una compra para este actor, manejar esto según tus requerimientos
                echo '<script>
                alert("Ya se solicitó número para este alumno.","Feeling Danzas");
                window.location.href = "../Views/Eventos.html?pk_eventos=' . $this->fk_eventos . '";
                </script>';
                $resultado="Ya se compraron entradas para este actor";
            } else {
                $this->db->exec("SET TRANSACTION ISOLATION LEVEL READ COMMITTED");
                $this->db->beginTransaction();
                // No se ha realizado una compra, proceder con la inserción y actualización
                $sql = "INSERT INTO comprador (email, CodigoEntrada, fk_eventos) VALUES (:email, :TokenEntrada, :fk_eventos)";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':email', $this->email, PDO::PARAM_STR);
                $stmt->bindParam(':TokenEntrada', $this->TokenEntrada, PDO::PARAM_STR);
                $stmt->bindParam(':fk_eventos', $this->fk_eventos, PDO::PARAM_INT);
                $this->db->commit();
                $stmt->execute();
                $lastInsertId = $this->db->lastInsertId();
                echo "Comprador insertado con éxito.";
                $resultado="Comprador insertado con éxito";
                // Actualizar el campo "compra" en la tabla "actores" a 1


                $this->db->exec("SET TRANSACTION ISOLATION LEVEL READ COMMITTED");
                $this->db->beginTransaction();
                $sql = "UPDATE actores SET compra = 1, fk_comprador:= :lastInsertId WHERE dni = :dni_actor AND fk_eventos = :fk_eventos";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':dni_actor', $this->dni_actor, PDO::PARAM_STR);
                $stmt->bindParam(':fk_eventos', $this->fk_eventos, PDO::PARAM_INT); // Agregamos esto
                $stmt->bindParam(':lastInsertId', $lastInsertId, PDO::PARAM_INT);
                $this->db->commit();
                $stmt->execute();
                $this->enviarMail();
                 
            }
    
            // Confirmar la transacción
         } catch (PDOException $e) {
            // Si hay un error, deshacer la transacción
            $this->db->rollBack();
            // Manejar el error de alguna manera adecuada, por ejemplo, lanzando una excepción
            $bool = false;
            throw new Exception('Error al verificar la compra: ' . $e->getMessage());
        }
        $this->log($resultado);
        return $bool;
    }
    
    public function log($resultado){
        date_default_timezone_set("America/Argentina/Buenos_Aires");
        $TiempoInsercion = date('d-m-y H:i:s'); // Formato d-m-y H:i:s
        // Crea una cadena con los datos para el registro
        $registro = "Evento: ". $this->fk_eventos." Email: " . $this->email . " - DNI actor: " . $this->dni_actor . " - Fecha: " . $TiempoInsercion ." - Comentario:".$resultado;
        // Abre el archivo de registro en modo escritura
        $archivoLog = fopen("registro.log", "a"); // "a" para agregar datos al archivo
        if ($archivoLog) {
            // Escribe la cadena de registro en el archivo
            fwrite($archivoLog, $registro . "\n");

            // Cierra el archivo de registro
            fclose($archivoLog);
        }
    }

    public function ApellidoNombre(){
        try {
            // Crear una instancia de la conexión a la base de datos
             // Consulta SQL para obtener los valores de la columna "pk_eventos"
            $consulta = "SELECT apellido, nombre FROM actores where dni= :dni_actor and fk_eventos=:fk_eventos";
            $stmt = $this->db->prepare($consulta);
            $stmt->bindParam(':dni_actor', $this->dni_actor, PDO::PARAM_INT); 
            $stmt->bindParam(':fk_eventos', $this->fk_eventos, PDO::PARAM_INT); 
            $stmt->execute();
            $listado = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $listado;
        } catch (PDOException $e) {
            // En caso de error en la conexión o consulta
            echo 'Error: ' . $e->getMessage();
            return null; // Puedes manejar el error de alguna manera adecuada
        }
    }
    
    public function enviarMail(){
    

    $datosActor = $this->ApellidoNombre();
    $nombre = $datosActor[0]["nombre"];
    $apellido = $datosActor[0]["apellido"]; 
    $to = $this->email; // Cambia esto por la dirección de correo a la que quieres enviar el mensaje
    $subject = "Reserva de la entrada";
    $message = "Hola!!! Tu número es el ".$this->TokenEntrada." , asignado al ".$this->dni_actor.", a nombre de " .$nombre. " " .$apellido. "
                El número fue asignado en la fila virtual de Feeling Danzas para la venta de entradas del Show Artístico 2023.
                Te recordamos que la venta de entradas será el sábado 21 de octubre de 9 a 14 hs en nuestra sede de Viamonte 160, Ramos Mejía.
                Tené en cuenta lo siguiente:
                * El número no es transferible
                * Se permite el ingreso de UNA PERSONA por número
                * Tardamos aproximadamente 3 minutos con cada persona que elige y paga sus entradas. Según tu número podés calcular aproximadamente a qué hora venir al Estudio
                * El pago de las entradas ES ÚNICAMENTE EN EFECTIVO
                * Por favor te pedimos que sepas exactamente cuántas entradas necesitas para evitar demoras
                * Podrás comprar hasta 15 entradas
                
                Este es un mail de respuesta automático
                NO RESPONDER
                Si tenés alguna consulta, comunicate a nuestro WhatsApp 1132782933
                
                Te esperamos!!!
                Feeling Danzas";
         
            // Configura los parámetros de correo
                // Configura los parámetros de correo
                $headers = "From: team@merakicodelabs.com\r\n";
                $headers .= "Bcc: info@argentecno.com.ar\r\n";
            
        
            // Reemplaza con tu dirección de correo

    // Utiliza la función mail() con el servidor SMTP de Hostinger
    if (mail($to, $subject, $message, $headers)) {
        echo '<script>
        alert("Se envió un correo a su email con el número asignado. Por favor, revisa la carpeta de spam en caso de no encontrarlo en la bandeja de entrada.","Feeling Danzas");        window.location.href = "../Views/Eventos.html";
        </script>';
    } else {
        echo '<script>
        alert("Hubo un error al enviar el correo con el número asignado. Comunícate con el organizador del evento para obtenerlo.","Feeling Danzas");        window.location.href = "../Views/Eventos.html";
        </script>';
    }
}


    public function ConsultarToken(){
        try{
        $consulta = "SELECT MAX(CodigoEntrada) AS maxCodigo FROM comprador WHERE fk_eventos = :fk_eventos";
        $stmt = $this->db->prepare($consulta);
        $stmt->bindParam(':fk_eventos', $this->fk_eventos, PDO::PARAM_INT); 
        $stmt->execute();
        $token = $stmt->fetchColumn();

        if($token !== false && $token !== null){ 
            $token = $token + 1;
        }
        else{
            $token= 100000;
        }

        }
        
        catch(PDOException $e){
            echo 'no se pudo traer el valor de codigoenetrada'. $e;
        }
        
        return $token;
    }

    public function BorrarCompradores($pkeventos){
        $bool = true;
        try {
            $consulta = "DELETE FROM comprador where fk_eventos= :pkevento";
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



    public function ObtenerReservasPorId($pkevento) {
        try {
                    
            $consulta = "SELECT  a.nombre, a.apellido, a.dni, c.codigoentrada from actores a, comprador c where a.fk_eventos = c.fk_eventos AND a.fk_comprador = c.pk_comprador and c.fk_eventos = :pkevento and a.compra = 1 order by c.CodigoEntrada";
            $stmt = $this->db->prepare($consulta);
            $stmt->bindParam(':pkevento', $pkevento, PDO::PARAM_INT);
            $stmt->execute();
            $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return json_encode($reservas);
        } catch (PDOException $e) {
            error_log('Error en ObtenerReservasPorId: ' . $e->getMessage());
            return null;
        }
    }
    
    

    public function ConsultarReservas($pkeventos){
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
            if($listado == null || $listado == false){
                $listado=null;
            }
            return $listado;
        } catch (PDOException $e) {
            // En caso de error en la conexión o consulta
            echo 'Error: ' . $e->getMessage();
            return null; // Puedes manejar el error de alguna manera adecuada
        }
    }
    
    










}
?>

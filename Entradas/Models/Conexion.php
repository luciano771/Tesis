<?php

class conexion extends PDO
{
    private $hostBd = '154.56.48.154'; 
    private $nombreBd = 'u955829785_eventos';
    private $usuarioBd = 'u955829785_root';
    private $passwordBd = 'LUCIAno4226';

    public function __construct()
    {
        try{
            parent::__construct('mysql:host='. $this->hostBd
            . ';dbname=' . $this->nombreBd .
            ';charset=utf8',$this->usuarioBd,$this->passwordBd,
            array(PDO::ATTR_ERRMODE =>PDO::ERRMODE_EXCEPTION));

        }
        catch(PDOException $e)
        {
            echo 'Error: ' . $e->getMessage();
            exit;
        }
    }


}


?>
<?php
session_start();
$_SESSION['usuario'] = '';
header('Location:  Views/Articulos.html');
 
?>
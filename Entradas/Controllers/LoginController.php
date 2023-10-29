<?php
 
if($_POST['correo'] == 'admin@gmail.com' && $_POST['contraseña'] == 'contraseñaprueba'){
    session_set_cookie_params(60);
    session_start();
    $_SESSION['autenticado'] = true;
    header('Location: ../Views/panel.php');

}
else{echo'usuario o contraseña incorrectos';}


?>
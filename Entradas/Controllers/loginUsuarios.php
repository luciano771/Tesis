<?php 


if($_POST['correo'] == 'admin@gmail.com' && $_POST['contraseña'] == 'contraseñaprueba'){
    session_set_cookie_params(60);
    $_SESSION['usuario'] = 'usuario';
    header('Location: ../Views/Articulos.html');

}
else{echo'usuario o contraseña incorrectos';}


?>
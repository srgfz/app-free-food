<?php
    //Nos unimos a la sesión
    session_start();
    
    //Borramos el array de sesión y la destruimos
    $_SESSION = array();
    session_destroy();
    
    //Eliminamos las cookies
    setcookie(session_name(),123,time()-1000);
    
    //Le redirigimos al Login
    header("Location: ../index.php");
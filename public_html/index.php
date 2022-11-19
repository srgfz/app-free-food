<?php
//Iniciamos o nos unimos a la sesión
session_start();
//if(isset($_SESSION["usuario"])){//Si la sesión existe le redirijo directamente a home.php
//    header("Location: ./pages/home.php");
//}
//Añado la libreria de funciones
include "../resources/library/funciones.php";

//Comprobamos si existe la BD en el localhost con usuario root y contraseña en blanco, en caso de no existir se ejecuta el código sql para crearla
if (!checkBD("mysql:dbname=appcomida;host=127.0.0.1", "root", "")) {//Si la base de datos no existe la creo con sus tablas y datos por defecto
    $queryBD = file_get_contents("../BDappcomida.sql");
    createBD($queryBD, "mysql:;host=127.0.0.1", "root", "");
}

//Guardo el usuario y contraseña introducidos
if ($_SERVER["REQUEST_METHOD"] == "POST") {//Si recibe un método POST
    //Guardo el usuario y contraseña introducidos
    $userLogin = filtrarInput("userLogin", "POST");
    $passLogin = sha1(filtrarInput("passLogin", "POST"));
    echo $passLogin;
    //Compruebo si el usuario y la contraseña son correctos:
    $user = checkUser("mysql:dbname=appcomida;host=127.0.0.1", "root", "", $userLogin, $passLogin);
    if (!empty($user)) {//Si el usuario y la contraseña son correctas
        //Guardamos la sesión con el usuario que ha iniciado sesión y su rol
        $_SESSION["usuario"] = $user;
        //Guardamos dos cookies: una con la hora de login del usuario y otra con la de la última actividad (en el login serán ambas la misma hora)
        setcookie("horaLogin", date("Y-n-j H:i:s"), time() + 3600 * 24, "/");
        setcookie("horaUltimaActividad", date("Y-n-j H:i:s"), time() + 3600 * 24, "/");

        //Redirigimos a home.php:
        header("Location: ./pages/home.php");
    } else {//Si las credenciales no son correctas mostramos un error
        $errorLogin = true;
    }
}
?>

<!DOCTYPE html>
<!--
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Project/PHP/PHPProject.php to edit this template
-->
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>FOODY | Login</title>
        <link rel="stylesheet" href="css/forms.css">
    </head>
    <body>
        <div class="caja__login">
            <h1 class="login__titulo">FOODY</h1>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

                <div class="login__usuario">
                    <input type="text" name="userLogin" required>
                    <label>Usuario</label>
                </div>

                <div class="login__usuario">
                    <input type="password" name="passLogin" required>
                    <label>Contraseña</label>
                </div>
                <?php
                if (isset($errorLogin)) {//Si el usuario o la cotraseña no son correctas mostramos el error
                    echo "<p class='error'>Usuario y/o contraseña incorrecta</p>";
                }
                ?>
                <p class="registro">¿No tienes cuenta?<a class="registrate__link"  href="./pages/register.php"> Registrate</a></p>
                
                <div class="loginButtons">
                    <button class="enviar" type="submit">
                        <span class="linea"></span>
                        <span class="linea"></span>
                        <span class="linea"></span>
                        <span class="linea"></span>
                        Entrar
                    </button>
                </div>

            </form>
            
        </div>
    </body>
</html>

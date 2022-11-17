<?php
//Iniciamos o nos unimos a la sesión
session_start();
//if(isset($_SESSION["usuario"])){//Si la sesión existe le redirijo directamente a home.php
//    header("Location: ./pages/home.php");
//}
//Añado la libreria de funciones
include "../resources/library/funciones.php";
//Guardo el usuario y contraseña introducidos
if ($_SERVER["REQUEST_METHOD"] == "POST") {//Si recibe un método POST
    //Guardo el usuario y contraseña introducidos
    $userLogin = filtrarInput("userLogin", "POST");
    $passLogin = filtrarInput("passLogin", "POST");
    //Compruebo si el usuario y la contraseña son correctos:
    $user = checkUser($userLogin, $passLogin);
    if (!empty($user)) {//Si el usuario y la contraseña son correctas
        //Guardamos la sesión con el usuario que ha iniciado sesión y su rol
        $_SESSION["usuario"] = $user;
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
<html>
    <head>
        <meta charset="UTF-8">
        <title>FOODY</title>
        <link rel="stylesheet" href="css/forms.css">
    </head>
    <body>
        <div class="caja__login">

            <h2 class="login__titulo">FOODY</h2>

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
                <div class="loginButtons">
                    <button class="enviar" type="submit">
                        <span class="linea"></span>
                        <span class="linea"></span>
                        <span class="linea"></span>
                        <span class="linea"></span>
                        Entrar
                    </button>

                    <a class="enviar"  href="./pages/register.php">
                        <span class="linea-reg"></span>
                        <span class="linea-reg"></span>
                        <span class="linea-reg"></span>
                        <span class="linea-reg"></span>
                        Registrar
                    </a>
                </div>


            </form>
        </div>

    </body>
</html>

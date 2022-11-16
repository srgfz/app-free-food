<?php
include "../resources/library/funciones.php";
//Comprobar si la sesión está iniciada; si lo está accede directamente a home.php, sino al index
if ($_SERVER["REQUEST_METHOD"] == "GET") {//Si recibe un método GET: error de Login
    $errorLogin = filtrarInput("error", "GET");
}
    //Guardo el usuario y contraseña introducidos
if ($_SERVER["REQUEST_METHOD"] == "POST") {//Si recibe un método POST
    //Guardo el usuario y contraseña introducidos
    $userLogin = filtrarInput("userLogin", "POST");
    $passLogin = filtrarInput("passLogin", "POST");
    //Compruebo si el usuario y la contraseña son correctos:
    //Guardo el id user, la pass y el rol de la BD:
    try {
        $bd = BDconexion("mysql:dbname=appcomida;host=127.0.0.1", "root", "");
        $loginSQL = "SELECT userId, pass, rol FROM usuarios WHERE userId = :userId AND pass = :pass";
        $preparada_user = $bd->prepare($loginSQL);
        $preparada_user->execute(array(":userId" => $userLogin, ":pass" => $passLogin));
        $login = ($preparada_user->rowCount() === 0) ? false : true;
        if ($login) {//Si el usuario y la contraseña son correctas
            foreach ($preparada_user as $row) {//Guardamos el rol del usuario
                $rol = $row['rol'];
            }
            //Guardamos la sesión con el usuario que ha iniciado sesión y su rol
            
            //Redirigimos a home.php:
            header("Location: ./pages/home.php");
        }else{//Si las credenciales no son correctas mostramos un error
            header("Location: ./index.php?error=true");
        }
        //Se cierra la conexión
        $bd = null;
    } catch (Exception $ex) {
        echo "Error con la base de datos: " . $ex->getMessage();
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
        <link rel="stylesheet" href="css/login.css">
    </head>
    <body>
        <div class="caja__login">

            <h2 class="login__titulo">FOODY</h2>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

                <div class="login__usuario">
                    <input type="text" name="userLogin" required="">
                    <label>Usuario</label>
                </div>

                <div class="login__usuario">
                    <input type="password" name="passLogin" required="">
                    <label>Contraseña</label>
                </div>
                <?php
                if(isset($errorLogin)){//Si el usuario o la cotraseña no son correctas mostramos el error
                    echo "<p class='error'>Usuario y/o contraseña incorrecta</p>";
                }
                ?>
          
                <button class="enviar" type="submit">
                    <span class="linea"></span>
                    <span class="linea"></span>
                    <span class="linea"></span>
                    <span class="linea"></span>
                    Entrar
                </button>

                <button class="enviar" type="submit">
                    <span class="linea-reg"></span>
                    <span class="linea-reg"></span>
                    <span class="linea-reg"></span>
                    <span class="linea-reg"></span>
                    Registrar
                </button>

            </form>
        </div>

    </body>
</html>

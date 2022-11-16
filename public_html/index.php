<?php
include "../resources/library/funciones.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {//Si recibe un método POST
    //Guardo el usuario y contraseña introducidos
    $userLogin = filtrarInput("userLogin", "POST");
    $passLogin = filtrarInput("passLogin", "POST");
    //Compruebo si el usuario y la contraseña son correctos:
    //Guardo el id user, la pass y el rol de la BD:
    try {
        $bd = BDconexion("mysql:dbname=appcomida;host=127.0.0.1", "root", "");
        $loginSQL = "SELECT userId, pass FROM usuarios WHERE userId = :userId AND pass = :pass";
        $preparada_user = $bd->prepare($loginSQL);
        $preparada_user->execute(array(":userId" => $userLogin, ":pass" => $passLogin));
        echo "usuarios con ese id--> " . $preparada_user->rowCount() . "<br>";
        $login = ($preparada_user->rowCount() === 0) ? true : false;
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

                <button class="enviar" type="submit">
                    <span class="linea"></span>
                    <span class="linea"></span>
                    <span class="linea"></span>
                    <span class="linea"></span>
                    Entrar
                </button>

                <a class="enviar" href="#">
                    <span class="linea-reg"></span>
                    <span class="linea-reg"></span>
                    <span class="linea-reg"></span>
                    <span class="linea-reg"></span>
                    Registrar
                </a>

            </form>
        </div>

    </body>
</html>

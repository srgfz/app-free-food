<?php
//Si la sesión del usuario no existe le redirijo al Login; si existe me uno a dicha sesión
session_start();
if (!isset($_SESSION["token"]) || !isset($_SESSION["usuario"])) {
    header("Location: ../index.php");
} else {//Guardo el token de la sesión
    $tokenSession = $_SESSION["token"];
    //Guardo en variables la información de la sesión: el id del usuario y su rol
    $user = $_SESSION["usuario"][0];
    $rol = $_SESSION["usuario"][1];
}

//Añado la libreria de funciones
include "../../resources/library/funciones.php";

//Compruebo el tiempo de inactividad del usuario: si es más de 5 minutos hago logOut
$horaUltimaActividad = isset($_COOKIE["horaUltimaActividad"]) ? $_COOKIE["horaUltimaActividad"] : null;
if (logOutInactivity(date("Y-n-j H:i:s"), $horaUltimaActividad, 300)) {//Si el tiempo de inactividad supera los 5 minutos hago logOut
    header("Location: logOut.php");
} else {//Si la inactividad es menor o igual a los 5 minutos actualizo la cookie de la hora de la última acción
    setcookie("horaUltimaActividad", date("Y-n-j H:i:s"), time() + 3600 * 24, "/");
}

//Cookie de modo claro/oscuro: por defecto será modo claro
$tema = isset($_COOKIE["tema"]) ? $_COOKIE["tema"] : "Tema Claro";

if ($_SERVER["REQUEST_METHOD"] == "POST") {//Si recibe un método POST
    //Verifico el token de la sesión con el enviado
    $tokenPOST = filtrarInput("token", "POST");

    if ($tokenSession === $tokenPOST) {//Si los token coinciden
        $search = strtoupper(filtrarInput("items", "POST"));
        if (isset($_POST["tema"])) {//Si el POST es del tema oscuro o claro
            //El tema lo elegido lo guardo en una cookie
            $tema = filtrarInput("tema", "POST");
            setcookie("tema", $tema, time() + 3600 * 24, "/");
        }
        if (isset($_POST['email'])) {//Si manda el email
            $inputMailVacio = false;
            $email = filtrarArrayInput("email", ["mail", "asunto", "mensaje"], $inputMailVacio);
            if ($inputMailVacio) {//Si algún input está vacio
                $errorMail = true;
            } else {//Si los parámetros para el correo son correctos envío el mail
                mail($email["mail"], $email["asunto"], $email["mensaje"]);
            }
        }
    } else {//Si no coincide cierro la sesión
        header("Location: ./logOut.php");
    }
}
?>

<!DOCTYPE html>
<!--
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->
<html lang="es">
    <head>
        <meta charset="UTF-8">
    <head>
        <meta charset="UTF-8">
        <title>FOODY | Home</title>
        <link rel="stylesheet" href="../css/style.css">
        <link rel="stylesheet" href="../css/nav.css">
        <link rel="stylesheet" href="../css/item.css">
        <link rel="stylesheet" href="../css/sendEmail.css">
        <link rel="stylesheet" href="../css/footer.css">
        <link rel="stylesheet"
              href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
              <?php
              if ($tema === "Tema Oscuro") {//Si el tema es oscuro lo añado, en caso contrario por defecto el css es tema claro
                  echo "<link rel='stylesheet' href='../css/temaOscuro.css'>";
              }
              ?>
    </head>
    <body>
        <?php
        //Comienzo del header **********
        include '../../resources/templates/header.php';
        //Fin del header **********
        ?>
        <main class="main">
            <form  method="POST" action="sendEmail.php">
                <input type="hidden" name="email[mail]" value="foodeEmail@company.com">
                <div class="form__row"><label>*Asunto</label><input type="text" name="email[asunto]" placeholder="Asunto del mensaje"></div>
                <div class="form__row"><label>*Mensaje</label><textarea cols="50" rows="10" name="email[mensaje]" maxlength="500" placeholder="Mensaje a enviar"></textarea></div>
                <?php
                if (isset($errorMail) && $errorMail) {
                    echo "<p class='error'>* Debe completar todos los datos para enviar el mail</p>";
                }
                ?>
                <input type='hidden' name='token' value="<?php echo $tokenSession; ?>">
                <button type="submit" class="form__btn">Enviar</button>
            </form>
        </main>
        <!--********** Fin del main **********-->
        <!--********** Comienzo del footer **********-->
        <?php
        include '../../resources/templates/footer.php'
        ?>
        <!--********** Fin del footer **********-->



    </body>
</html>

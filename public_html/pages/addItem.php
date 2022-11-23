<?php
//Si la sesión del usuario no existe le redirijo al Login; si existe me uno a dicha sesión
session_start();
if (!isset($_SESSION["token"]) || !isset($_SESSION["usuario"])) {
    header("Location: ../index.php");
} else {
    //Si existe guardo el token de la sesión
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
    } else {//Si no coincide cierro la sesión
        header("Location: ./logOut.php");
    }
} else if ($_SERVER["REQUEST_METHOD"] == "GET") {//Si recibe un GET
    $errorStock = filtrarInput("errorStock", "GET");
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
        <title>FOODY | Home</title>
        <link rel="stylesheet" href="../css/style.css">
        <link rel="stylesheet" href="../css/nav.css">
        <link rel="stylesheet" href="../css/item.css">
        <link rel="stylesheet"
              href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
              <?php
              if ($tema === "Tema Oscuro") {//Si el tema es oscuro lo añado, en caso contrario por defecto el css es tema claro
                  echo "<link rel='stylesheet' href='../css/temaOscuro.css'>";
              }
              ?>
    </head>
    <body>
        <div class="container">
            <?php
            //Comienzo del header **********
            include '../../resources/templates/header.php';
            //Fin del header **********
            ?>
            <!--********** Inicio del main **********-->
            <main class="main">
                <div class='item'>
                    <div class="item__contTitle">
                        <h2 class='item__title'>Nombre: </h2><span class="item__titleName"> Haribo</span>
                    </div>
                    <ul>
                        <li class='item__li'><h3 class='li__title'>Cantidad disponible: </h3><p class='li__text'>15 unidades</li>
                        <li class='item__li'><h3 class='li__title'>Peso Kg/unidad: </h3><p class='li__text'>0.95 kg/ud</li>
                        <li class='item__li'><h3 class='li__title'>Fecha de Caducidad: </h3><p class='li__text'>02/01/2023</p></li>
                        <li class='item__li'><h3 class='li__title'>Descripclión: </h3><p class='li__text'>Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old.</p></li>
                    </ul>
                    <form class='item__li' method='POST' action='./addItem.php'><input type='hidden' name='idProducto' value=4><input type='hidden' name='idEmpresa' value=empresa2><input type='hidden' name='token' value='392fea4f3313596edb8ea069c52a6a106b916042b6b8fa4bc1e4735c1cef48a8'>
                        <button type='submit' class='item__btn'>Añadir Producto</button></form>
                </div>

            </main>
            <!--********** Fin del main **********-->
            <!--********** Comienzo del footer **********-->
            <footer class="footer">

            </footer>
            <!--********** Fin del footer **********-->

        </div>

    </body>
</html>


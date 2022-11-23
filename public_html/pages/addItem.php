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
        if (isset($_POST["tema"])) {//Si el POST es del tema
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
            <!--********** Comienzo del header **********-->
            <header class="header">
                <!--********** Inicio del nav **********-->
                <nav class="nav">
                    <ul class="nav__ul">
                        <li class="nav__li">
                            <h1 class="title"><a href="./home.php" class="">Logo de Foody</a></h1>
                        </li>
                        <li class="nav__li nav__btn">
                            <a href="<?php echo $_SERVER["PHP_SELF"]; ?>" class="nav__link">
                                <?php
                                $btnText = $rol === "empresa" ? "Mis Productos" : "Listar Productos";
                                echo $btnText; //Según el rol del usuario el contenido del botón cambia
                                ?>
                            </a>
                        </li>
                        <?php 
                        if($rol === "empresa"){//Botón de añadir un nuevo producto
                            echo "<li class='nav__li nav__btn'><a href='./addItem.php' class='nav__link'>Añadir Producto</a></li>";
                        }
                        ?>
                        <li class="nav__li">
                            <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                                <div class="nav__search">
                                    <input type="search" class="search__input" placeholder="Buscar" name="items">
                                    <input type="hidden" name="token" value="<?php echo $tokenSession; ?>">
                                    <button type="submit" class="search__btn">
                                        <span class="material-symbols-outlined">
                                            search
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </li>

                        <li class="nav__li nav__lastItem">
                            <form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
                                <select name="tema">
                                    <?php imprimirOptions(["Tema Claro", "Tema Oscuro"], $tema) ?>
                                </select> 
                                <input type="hidden" name="token" value="<?php echo $tokenSession; ?>">
                                <button type="submit">Cambiar</button>
                            </form>
                            <a href="./logOut.php" class="nav__a nav__logOut">Salir 
                                <span class="material-symbols-outlined">
                                    logout
                                </span>
                            </a>
                        </li>
                    </ul>
                </nav>
                <!--********** Fin del nav **********-->
            </header>
            <!--********** Fin del header **********-->
            <!--********** Inicio del main **********-->
            <main class="main">
                <?php
                
                ?>

            </main>
            <!--********** Fin del main **********-->
            <!--********** Comienzo del footer **********-->
            <footer class="footer">

            </footer>
            <!--********** Fin del footer **********-->

        </div>

    </body>
</html>


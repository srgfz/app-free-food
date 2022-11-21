<?php
//Si la sesión del usuario no existe le redirijo al Login; si existe me uno a dicha sesión
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: ../index.php");
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
//Guardo en variables la información de la sesión: el id del usuario y su rol
$user = $_SESSION["usuario"][0];
$rol = $_SESSION["usuario"][1];

if ($_SERVER["REQUEST_METHOD"] == "POST") {//Si recibe un método POST
}else if ($_SERVER["REQUEST_METHOD"] == "GET") {//Si recibe un método GET
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
    </head>
    <body>
        <div class="container">
            <!--********** Comienzo del header **********-->
            <header class="header">
                <!--********** Inicio del nav **********-->
                <nav class="nav">
                    <ul class="nav__ul">
                        <li class="nav__li">
                            <h1 class="title"><a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="">Logo de Foody</a></h1>
                        </li>
                        <li class="nav__li nav__btn"><a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="nav__link">Listar Productos</a></li>
                        <li class="nav__li">
                            <div class="nav__search">
                                <input type="search" class="search__input" placeholder="Buscar">
                                <button type="submit" class="search__btn">
                                    <span class="material-symbols-outlined">
                                        search
                                    </span>
                                </button>
                            </div>
                        </li>
                        <li class="nav__li">
                            <a href="./logOut.php" class="nav__a">Salir 
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
<?php
if (isset($errorStock) && $errorStock) {
    echo "<p class='errorStock'>*La cantidad solicitada debe ser un número entre 0 y la cantidad disponible el producto</p>";
}
?>
            <!--********** Inicio del main **********-->
            <main class="main">
                <?php
                $query = "SELECT idProducto as 'keyProducto', idEmpresa as 'keyEmpresa', productos.nombre as 'Nombre del Producto', stock as 'Cantidad disponible', fechaCaducidad as 'Fecha de Caducidad', usuarios.nombre as 'Nombre Vendedor', usuarios.direccion as 'Dirección', descripción as 'Descripclión'  FROM productos"
                        . " INNER JOIN usuarios ON usuarios.userId = productos.idEmpresa;";
                $productos = selectQuery("mysql:dbname=appcomida;host=127.0.0.1", "root", "", $query);
                if (isset($productos) && !empty($productos)) {//Si hay productos disponibles los muestro
                    listarProductos($productos, $rol);
                } else {//Si no hay ningún producto 
                    echo "<p class='noItems'>--- No hay ningún producto disponible ---</p>";
                }
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

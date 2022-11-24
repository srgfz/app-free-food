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

$resultados = true; //será false si no hay ningún resultado para la consulta a la BD sobre los productos
if ($_SERVER["REQUEST_METHOD"] == "POST") {//Si recibe un método POST
    //Verifico el token de la sesión con el enviado
    $tokenPOST = filtrarInput("token", "POST");
    if ($tokenSession === $tokenPOST) {//Si los token coinciden
        $search = strtoupper(filtrarInput("items", "POST"));
        if (isset($_POST["tema"])) {//Si el POST es del tema ocuro/claro
            //El tema lo elegido lo guardo en una cookie
            $tema = filtrarInput("tema", "POST");
            setcookie("tema", $tema, time() + 3600 * 24, "/");
        }
    } else {//Si no coincide cierro la sesión
        header("Location: ./logOut.php");
    }
} else if ($_SERVER["REQUEST_METHOD"] == "GET") {//Si recibe un GET
    $errorStock = filtrarInput("errorStock", "GET");
    $pedido = filtrarInput("pedido", "GET");
    $addItem = filtrarInput("addItem", "GET");
    $itemDeleted = filtrarInput("itemDeleted", "GET");
    $errorDelete = filtrarInput("errorDelete", "GET");
    $itemError = filtrarInput("itemError", "GET");
    $itemErrorUpdate = filtrarInput("itemErrorUpdate", "GET");
    $editItem = filtrarInput("editItem", "GET");
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
        <div class="container">
            <?php
            //Comienzo del header **********
            include '../../resources/templates/header.php';
            //Fin del header **********
            //Mensajes de error/información al usuario:
            if (isset($errorStock) && $errorStock) {
                echo "<p class='mensaje error'>* La cantidad solicitada debe ser un número entero entre 0 y la cantidad disponible el producto</p>";
            } else if (isset($pedido) && $pedido) {
                echo "<p class='mensaje'>* Pedido realizado correctamente</p>";
            } else if (isset($addItem) && $addItem) {
                echo "<p class='mensaje'>* Producto añadido</p>";
            } else if (isset($itemDeleted) && $itemDeleted) {
                echo "<p class='mensaje'>* Producto eliminado</p>";
            } else if (isset($errorDelete) && $errorDelete) {
                echo "<p class='mensaje error'>* El producto no se ha podido eliminar</p>";
            } else if (isset($itemError) && $itemError) {
                echo "<p class='mensaje error'>* El producto ya había sido eliminado previamente</p>";
            } else if (isset($itemErrorUpdate) && $itemErrorUpdate) {
                echo "<p class='mensaje error'>* El producto que ha intentado editar ya no existe</p>";
            } else if (isset($editItem) && $editItem) {
                echo "<p class='mensaje'>* Producto actualizado</p>";
            }
            ?>
            <!--********** Inicio del main **********-->
            <main class="main">
                <?php
                //Según el rol del usuario se mostrará distinta información mediante distintas consultas a la BD:
                if ($rol === "cliente" && !isset($search)) {//Listo todos los productos con stock si no ha usado el buscador y es cliente
                    $query = "SELECT idProducto as 'keyProducto', idEmpresa as 'keyEmpresa', productos.nombre as 'Nombre del Producto', stock as 'Cantidad disponible', kg_ud as 'Peso', fechaCaducidad as 'Fecha de Caducidad', usuarios.nombre as 'Nombre Vendedor', usuarios.direccion as 'Dirección', descripcion as 'Descripción', imgSRC  FROM productos"
                            . " INNER JOIN usuarios ON usuarios.userId = productos.idEmpresa WHERE productos.stock > 0;";
                } else if (($rol === "cliente" && isset($search))) {//Si es cliente y ha usado el buscador los filtro según su nombre mediante la consulta
                    $query = "SELECT idProducto as 'keyProducto', idEmpresa as 'keyEmpresa', productos.nombre as 'Nombre del Producto', stock as 'Cantidad disponible', kg_ud as 'Peso', fechaCaducidad as 'Fecha de Caducidad', usuarios.nombre as 'Nombre Vendedor', usuarios.direccion as 'Dirección', descripcion as 'Descripción', imgSRC  FROM productos"
                            . " INNER JOIN usuarios ON usuarios.userId = productos.idEmpresa"
                            . " WHERE productos.stock > 0 AND (UPPER(productos.nombre) LIKE '%$search%' OR UPPER(productos.nombre) LIKE '$search%' OR UPPER(productos.nombre) LIKE '%$search');";
                } else if ($rol === "empresa" && !isset($search)) {//Si es una empresa y no usa el buscador listo todos los productos que pertenecen a dicha empresa
                    $query = "SELECT idProducto as 'keyProducto', idEmpresa as 'keyEmpresa', productos.nombre as 'Nombre del Producto', stock as 'Cantidad disponible', kg_ud as 'Peso', fechaCaducidad as 'Fecha de Caducidad', usuarios.nombre as 'Nombre Vendedor', usuarios.direccion as 'Dirección', descripcion as 'Descripción', imgSRC  FROM productos"
                            . " INNER JOIN usuarios ON usuarios.userId = productos.idEmpresa WHERE productos.idEmpresa = '$user';";
                } else if ($rol === "empresa" && isset($search)) {
                    $query = "SELECT idProducto as 'keyProducto', idEmpresa as 'keyEmpresa', productos.nombre as 'Nombre del Producto', stock as 'Cantidad disponible', kg_ud as 'Peso', fechaCaducidad as 'Fecha de Caducidad', usuarios.nombre as 'Nombre Vendedor', usuarios.direccion as 'Dirección', descripcion as 'Descripción', imgSRC  FROM productos"
                            . " INNER JOIN usuarios ON usuarios.userId = productos.idEmpresa"
                            . " WHERE productos.idEmpresa = '$user' AND (UPPER(productos.nombre) LIKE '%$search%' OR UPPER(productos.nombre) LIKE '$search%' OR UPPER(productos.nombre) LIKE '%$search');";
                } else if ($rol === "admin" && !isset($search)) {//Si es admin  y no usa el buscador puede listar todos los pedidos y borrarlos
                    $query = "SELECT idProducto as 'keyProducto', idEmpresa as 'keyEmpresa', productos.nombre as 'Nombre del Producto', stock as 'Cantidad disponible', kg_ud as 'Peso', fechaCaducidad as 'Fecha de Caducidad', usuarios.nombre as 'Nombre Vendedor', usuarios.direccion as 'Dirección', descripcion as 'Descripción', imgSRC  FROM productos"
                            . " INNER JOIN usuarios ON usuarios.userId = productos.idEmpresa";
                } else if ($rol === "admin" && isset($search)) {//Si es admin y usa el buscador
                    $query = "SELECT idProducto as 'keyProducto', idEmpresa as 'keyEmpresa', productos.nombre as 'Nombre del Producto', stock as 'Cantidad disponible', kg_ud as 'Peso', fechaCaducidad as 'Fecha de Caducidad', usuarios.nombre as 'Nombre Vendedor', usuarios.direccion as 'Dirección', descripcion as 'Descripción', imgSRC  FROM productos"
                            . " INNER JOIN usuarios ON usuarios.userId = productos.idEmpresa"
                            . " WHERE (UPPER(productos.nombre) LIKE '%$search%' OR UPPER(productos.nombre) LIKE '$search%' OR UPPER(productos.nombre) LIKE '%$search');";
                } else {//Si no se cumple ninguna de estas opciones
                    $resultados = false;
                }
                $productos = selectQuery("mysql:dbname=appcomida;host=127.0.0.1", "root", "", $query, $resultados);
                if (isset($productos) && $resultados) {//Si hay productos disponibles los muestro
                    listarProductos($productos, $rol, $tokenSession);
                } else {//Si no hay ningún producto 
                    echo "<p class='noItems'>--- No hay ningún producto disponible ---</p>";
                }
                ?>

            </main>
            <!--********** Fin del main **********-->
            <!--********** Comienzo del footer **********-->
            <?php
            include '../../resources/templates/footer.php'
            ?>
            <!--********** Fin del footer **********-->

        </div>

    </body>
</html>

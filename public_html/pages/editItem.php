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
        $idProducto = filtrarInput("idProducto", "POST");
        //Guardo el producto que se quiera editar:
        $existeItem = true;
        $itemQuery = selectQuery("mysql:dbname=appcomida;host=127.0.0.1", "root", "", "SELECT * FROM productos WHERE idProducto = $idProducto", $existeItem);
        //Guardo el item en un array
        $item = [];
        foreach ($itemQuery as $row) {
            foreach ($row as $key => $value) {
                $item["$key"] = $value;
            }
        }
        if ($existeItem) {//Si el prodcuto a modificar existe
            if (isset($_POST["updateItem"])) {//Si actualiza el producto
                //Si no esta vacio lo insertamos en la base de datos
                $errorEditarProducto = false;
                $updateItem = filtrarArrayInput("updateItem", ["nombre", "stock", "kg_ud", "fechaCaducidad"], $errorEditarProducto);
                //Compruebo que la fecha se mayor a la fecha actual:
                $errorEditarProducto = strtotime(date("Y-n-j H:i:s")) > strtotime($updateItem["fechaCaducidad"]) ? true : false;
                if (!$errorEditarProducto) {//Si los campos están correctos actualizo el producto y le redirijo a home.php
                    updateInBDFromArray("mysql:dbname=appcomida;host=127.0.0.1", "root", "", "productos", "idProducto", $idProducto, $updateItem);
                    header("Location: home.php?editItem=true");
                }
            }
        } else {//Si el producto ya  no existe
            $itemErrorUpdate = true;
        }

        if (isset($_POST["tema"])) {//Si el POST es del tema oscuro o claro
            //El tema lo elegido lo guardo en una cookie
            $tema = filtrarInput("tema", "POST");
            setcookie("tema", $tema, time() + 3600 * 24, "/");
        }
    } else {//Si no coincide cierro la sesión
        header("Location: ./logOut.php");
    }
} else {//Solo puede acceder a esta página mediante POST
    header("Location: ./logOut.php");
}
//Si el producto y a no existe le aviso al usuario
if (isset($itemErrorUpdate) && $itemErrorUpdate) {
    header("Location: ./home.php?itemErrorUpdate=true");
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
        <link rel="stylesheet" href="../css/additem.css">
        <link rel="stylesheet" href="../css/footer.css">
        <link rel="shortcut icon" href="../assets/images/logo.png" type="image/x-icon">
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
            ?>
            <!--********** Inicio del main **********-->
            <main class="main">
                <div class='item'>
                    <form class='item__li' method='POST' action='./editItem.php'>
                        <div class="item__contTitle">
                            <h2 class='item__title'>*Nombre: </h2><input class="input__title" type="text" name="updateItem[nombre]" value="<?php echo $item["nombre"] ?>" placeholder="Nombre del Producto" required  maxlength="50">
                        </div>
                        <ul class="addItem__ul">
                            <li class='item__li'><h3 class='li__title'>*Cantidad disponible: </h3><input class="input__number" type="number" value="<?php echo $item["stock"] ?>" name="updateItem[stock]" min="0" max="999" placeholder="0" required></li>
                            <li class='item__li'><h3 class='li__title'>*Peso Kg/unidad: </h3><input class="input__number" type="number" value="<?php echo $item["kg_ud"] ?>" name="updateItem[kg_ud]" min="0" max="999" step='.01' placeholder="0" required></li>
                            <li class='item__li'><h3 class='li__title'>*Fecha de Caducidad: </h3><input type="date"  value="<?php echo $item["fechaCaducidad"] ?>" name="updateItem[fechaCaducidad]" required></li>
                            <li class='item__li'><h3 class='li__title'>Descripción: </h3><textarea class="input__textarea"  name="updateItem[descripcion]" rows="10" cols="50" maxlength="500"><?php echo $item["descripcion"] ?></textarea></li>
                            <?php
                            if (isset($errorEditarProducto) && $errorEditarProducto) {
                                echo "<li class='item__li error'> *Debe completar todos los campos obligatorios y la fecha de caducidad no puede ser anterior a la fecha actual";
                            }
                            ?>
                        </ul>
                        <input type='hidden' name='idProducto' value="<?php echo $idProducto; ?>">
                        <input type='hidden' name='token' value="<?php echo $tokenSession; ?>">
                        <button type='submit' class='item__btn'>Editar Producto</button>
                    </form>
                </div>

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


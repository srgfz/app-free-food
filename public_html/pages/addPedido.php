<?php

//Si la sesión del usuario no existe le redirijo al Login; si existe me uno a dicha sesión
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: ../index.php");
} else if ($_SESSION["usuario"][1] !== "cliente") {//Solo los clientes pueden realizar pedidos
    header("Location: ./home.php");
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
    //GENERO EL PEDIDO:    
    //Guardo las variables del pedido realizado en un array:
    $pedido["idProducto"] = filtrarInput("idProducto", "POST");
    $pedido["idEmpresa"] = filtrarInput("idEmpresa", "POST");
    $pedido["cantidad"] = filtrarInput("cantidadPedido", "POST");
    $pedido["idCliente"] = $user;

    if ($pedido["cantidad"] > 0) {//Si la cantidad solicitada es mayor que 0
        //Compruebo que la cantidad sea un número entre 0 y el stock disponible:
        //**$stockDisponible será -1 si no hay suficiente stock; si hay suficiente stock será el stock disponible tras descontar la cantidad solicitada
        $stockDisponible = checkStock("mysql:dbname=appcomida;host=127.0.0.1", "root", "", $pedido["idProducto"], $pedido["cantidad"]);
        if ($stockDisponible !== -1) {//Si hay suficiente stock
            //Creo el nuevo pedido
            insertInBD("mysql:dbname=appcomida;host=127.0.0.1", "root", "", "pedidos", $pedido);
            //Actualizo el stock
            updateInBD("mysql:dbname=appcomida;host=127.0.0.1", "root", "", "productos", "idProducto", $pedido["idProducto"], "stock", $stockDisponible);
        } else {//Si no muestro un error
            header("Location: ./home.php?errorStock=true");
        }
    } else {//Si no muestro un error
        header("Location: ./home.php?errorStock=true");
    }
}
//Después de crear el pedido y descontar la cantidad solicitada le redirijo de nuevo a home.php
header("Location: ./home.php");

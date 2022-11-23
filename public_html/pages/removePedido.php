<?php

//Si la sesión del usuario no existe le redirijo al Login; si existe me uno a dicha sesión
session_start();
if (!isset($_SESSION["token"]) || !isset($_SESSION["usuario"])) {
    header("Location: ../index.php");
} else if ($_SESSION["usuario"][1] === "cliente") {//Los clientes no pueden borrar productos
    header("Location: ./home.php");
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


if ($_SERVER["REQUEST_METHOD"] == "POST") {//Si recibe un método POST
    //Compruebo el token:
    $tokenPOST = filtrarInput("token", "POST");
    if ($tokenPOST === $tokenSession) {//Si los tokens coinciden
        //BORRO EL PEDIDO:    
        //Guardo las variables de producto a borrar en un array:
        $borrarPedido["idProducto"] = filtrarInput("idProducto", "POST");
        $borrarPedido["idEmpresa"] = filtrarInput("idEmpresa", "POST");
        $pedidos = filtrarInput("pedidos", "POST");

        if ($borrarPedido["idEmpresa"] === $user || $rol === "admin") {//Si el producto pertenece a la empresa o es un admin
            if (isset($pedidos) && $pedidos) {//Si el pedido se encuentra entre los pedidos
                deleteInBD("mysql:dbname=appcomida;host=127.0.0.1", "root", "", "pedidos", "idProducto", $borrarPedido["idProducto"]);
            }
            header("Location: ./home.php?564");

            //Borro el pedido
            deleteInBD("mysql:dbname=appcomida;host=127.0.0.1", "root", "", "productos", "idProducto", $borrarPedido["idProducto"]); //Si es true el producto estaba en la tabla pedidos
        }
    } else {//Si los token no coinciden cierro sesión
        header("Location: ./logOut.php");
    }
} else {//Si no recibe post cierro sesión, ya que a esta página solo se puede acceder desde el formulario de solicitar un producto
    header("Location: ./logOut.php");
}

//Después de borrar el producto y los pedidos asociados a él si fuera necesario, le redirijimos a home.php
header("Location: ./home.php");

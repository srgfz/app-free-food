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
        $borrarProducto["idProducto"] = filtrarInput("idProducto", "POST");
        $borrarProducto["idEmpresa"] = filtrarInput("idEmpresa", "POST");
        $pedidos = filtrarInput("pedidos", "POST");

        if ($borrarProducto["idEmpresa"] === $user || $rol === "admin") {//Si el producto pertenece a la empresa o es un admin
            //Guardo los productos borrados en items_delete.log
            $query = "SELECT * FROM productos WHERE idProducto = " . $borrarProducto["idProducto"];
            $resultadosProducto = true;
            $productoBorrado = selectQuery("mysql:dbname=appcomida;host=127.0.0.1", "root", "", $query, $resultadosProducto);
            //Compruebo que el registro a eliminar sigue existiendo:
            if ($resultadosProducto) {//Si el producto sigue existiendo
                //Guardo el producto que borro
                $mensajeProductoEliminado = date("F j, Y, g:i a") . " - El usuario $user ha eliminado el producto: ";
                foreach ($productoBorrado as $productoBorrado) {
                    foreach ($productoBorrado as $key => $value) {
                        if (is_string($key)) {
                            $mensajeProductoEliminado .= " $key = $value;";
                        }
                    }
                }
                //Añadimos el mensaje a orders_delete.log
                error_log($mensajeProductoEliminado . "\n", 3, "../../logs/items_delete.log");

                if (isset($pedidos) && $pedidos) {//Si el producto tiene pedidos asociados
                    //Guardo los pedidos borrados en order_delete.log
                    $query = "SELECT * FROM pedidos WHERE idProducto = " . $borrarProducto["idProducto"];
                    $resultadoPedidos = true;
                    $pedidosBorrados = selectQuery("mysql:dbname=appcomida;host=127.0.0.1", "root", "", $query, $resultadoPedidos);
                    if ($resultadoPedidos) {//Si sigue habiendo pedidos asociados al producto
                        //Guardamos el mensaje de los pedidos eliminados
                        $mensajePedidosEliminados = date("F j, Y, g:i a") . " - El usuario $user ha eliminado los siguientes pedidos:";
                        foreach ($pedidosBorrados as $pedidoBorrado) {
                            $mensajePedidosEliminados .= "\n \t - ";
                            foreach ($pedidoBorrado as $key => $value) {
                                if (is_string($key)) {
                                    $mensajePedidosEliminados .= " $key = $value;";
                                }
                            }
                        }
                        //Añadimos el mensaje a orders_delete.log
                        error_log($mensajePedidosEliminados . "\n \n", 3, "../../logs/orders_delete.log");

                        //Borro los pedidos relacionados
                        deleteInBD("mysql:dbname=appcomida;host=127.0.0.1", "root", "", "pedidos", "idProducto", $borrarProducto["idProducto"]);
                    }
                }
                //Borro el producto
                deleteInBD("mysql:dbname=appcomida;host=127.0.0.1", "root", "", "productos", "idProducto", $borrarProducto["idProducto"]); //Si es true el producto estaba en la tabla pedidos
                $itemDeleted = true;
            } else {//Si el producto no exite cuando lo intenta borrar
                $itemError = true;
            }
        }
    } else {//Si los token no coinciden cierro sesión
        header("Location: ./logOut.php");
    }
} else {//Si no recibe post cierro sesión, ya que a esta página solo se puede acceder desde el formulario de solicitar un producto
    header("Location: ./logOut.php");
}

//Después de borrar el producto y los pedidos asociados a él si fuera necesario, le redirijimos a home.php
if (isset($itemDeleted) && $itemDeleted) {
    header("Location: ./home.php?itemDeleted=true");
} else if (isset($itemError) && $itemError) {//Si al intentar borrar el producto ya no existía
    header("Location: ./home.php?itemError=true");
} else {
    header("Location: ./home.php?errorDelete=true");
}

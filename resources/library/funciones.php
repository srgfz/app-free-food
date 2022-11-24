<?php

/**
 * filtrarInput() --> función para filtrar un input mediante htmlspecialchars()
 * @param type string $input --> nombre de la variable input a filtrar
 * @param type string $metodo --> Para indicar el método utilizado ("GET" o "POST")
 * @return type string --> Devuelve la variable del input filtrada
 */
function filtrarInput($input, $metodo) {
    if ($metodo === "POST") {//Si el método es POST
        $variableFiltrada = isset(filter_input_array(INPUT_POST)[$input]) ? htmlspecialchars(filter_input_array(INPUT_POST)[$input]) : null;
    } else if ($metodo === "GET") {//Si el método es GET
        $variableFiltrada = isset(filter_input_array(INPUT_GET)[$input]) ? htmlspecialchars(filter_input_array(INPUT_GET)[$input]) : null;
    }
    return $variableFiltrada;
}

/**
 * filtrarArrayInput() --> función para filtrar un input POST tipo array. Filtra hasta dos niveles de array anidados con htmlspecialchars() y si las $clavesAComprobar están vacias (1 nivel)
 * @param type string $arrayInputName --> nombre de la variable array a filtrar
 * @param type array $clavesAComprobar --> array con las claves de los campos que se quiere comprobar si están vacíos
 * @param type boolean $errorInputVacio --> referencia a un booleano. Será false si alguno de las claves a comprobar está vacía
 * @return type array --> Devuelve el array POST filtrado y puede cambiar el valor del parámetro que pasemos como $errorInputVacio
 */
function filtrarArrayInput($arrayInputName, $clavesAComprobar, &$errorInputVacio) {
    $arrayInputs = isset(filter_input_array(INPUT_POST)[$arrayInputName]) ? filter_input_array(INPUT_POST)[$arrayInputName] : null;
    if (isset($arrayInputs)) {//Si el array existe
        //Filtro con htmlspecialchars todos los campos del array
        foreach ($arrayInputs as &$value) {
            $value = htmlspecialchars($value);
        }
        //Compruebo si los campos necesarios existen y si están vacios
        foreach ($clavesAComprobar as $inputs) {
            if (!isset($arrayInputs[$inputs]) || (isset($arrayInputs[$inputs]) && trim($arrayInputs[$inputs]) == "")) {//Si no existe o si existe y está vacio
                $errorInputVacio = true; //Cambio el valor del error a true
            }
        }
    }
    return $arrayInputs;
}

/**
 * imprimirOptions() --> Función para mostrar en código html las options de un select
 * @param type array $array --> Array con todas las options que se desean mostrar en el select
 * @param type string $select --> Opción que aparecerá selected de todas las options.
 */
function imprimirOptions($array, $select) {//Función para imprimir select y seleccionar una si ya la había elegido previamente
    foreach ($array as $value) {
        if ($value == $select) {//Si había seleccionado antes una opción
            echo "<option value = '$value' selected>$value</option>";
        } else {//Si no ha seleccionado nada
            echo "<option value = '$value'>$value</option>";
        }
    }
}

/**
 * checkBD() --> Comprueba la conexión (true) o no (false) de la base de datos indicada en los parámetros
 * @param type string $conexionDB --> cadena de conexión con la BD
 * @param type string $user --> usuario de la BD
 * @param type string $pass --> password de la BD
 * @param type string $rutaLog --> ruta hasta el archivo error_log donde se registran los errores conectar a la BD (por defecto es "../../logs/error_log.log")
 * @return boolean --> true si la conexión se realiza, false en caso contrario
 */
function checkBD($conexionDB, $user, $pass, $rutaLog = "../../logs/error_log.log") {
    try {
        $bd = new PDO($conexionDB, $user, $pass);
        //Se cierra la conexión
        $bd = null;
    } catch (Exception $ex) {
        error_log(date("F j, Y, g:i a") . " - Error con la base de datos: " . $ex->getMessage() . "\n", 3, $rutaLog);
        return false;
    }
    return true;
}

/**
 * createBD() --> función para crear la base de datos
 * @param type string $query --> query MySQL a ejecutar
 * @param type string $conexionDB --> cadena de conexión con la BD
 * @param type string $user --> usuario de la BD
 * @param type string $pass --> password de la BD
 * @param type string $rutaLog --> ruta hasta el archivo error_log donde se registran los errores conectar a la BD (por defecto es "../../logs/error_log.log")
 */
function createBD($query, $conexionDB, $user, $pass, $rutaLog = "../../logs/error_log.log") {
    try {
        $bd = new PDO($conexionDB, $user, $pass);
        //Ejecutamos la query
        $bd->query($query);
        //Se cierra la conexión
        $bd = null;
    } catch (Exception $ex) {
        error_log(date() . " - Error con la base de datos: " . $ex->getMessage() . "\n", 3, $rutaLog);
    }
}

/**
 * checkUser() --> Función para comprobar si los parámetros introducidos existen en la base de datos (si el usuario es correcto)
 * @param type string $conexionDB --> cadena de conexión con la BD
 * @param type string $user --> usuario de la BD
 * @param type string $pass --> password de la BD
 * @param type string $userLogin --> input login del usuario
 * @param type string $passLogin --> input login de la contraseña
 * @param type string $rutaLog --> ruta hasta el archivo error_log donde se registran los errores conectar a la BD (por defecto es "../../logs/error_log.log")
 * @return type array --> devuelve en un array el idUsuario y su rol en caso de que sea correcto, en caso contrario devuelve el un array vacio
 */
function checkUser($conexionDB, $user, $pass, $userLogin, $passLogin, $rutaLog = "../../logs/error_log.log") {
    $userChecked = [];
    try {
        $bd = new PDO($conexionDB, $user, $pass);
        $loginSQL = "SELECT userId, pass, rol FROM usuarios WHERE userId = :userId AND pass = :pass";
        $preparada_user = $bd->prepare($loginSQL);
        $preparada_user->execute(array(":userId" => $userLogin, ":pass" => $passLogin));
        $login = ($preparada_user->rowCount() === 0) ? false : true;
        if ($login) {//Si las credenciales son correctas
            foreach ($preparada_user as $row) {//Guardamos el usuario y su rol
                $userChecked[0] = $row['userId'];
                $userChecked[1] = $row['rol'];
            }
        }
        //Se cierra la conexión
        $bd = null;
    } catch (Exception $ex) {
        error_log(date("F j, Y, g:i a") . " - Error con la base de datos: " . $ex->getMessage() . "\n", 3, $rutaLog);
    }
    return $userChecked;
}

/**
 * insertInBD() --> Función para insertar un array (con valores numéricos o string) en la BD (*Las claves del array deben coincidir con el nombre de los campos de la BD)
 * @param type string $conexionDB --> cadena de conexión con la BD
 * @param type string $userDB --> usuario de la BD
 * @param type string $passDB --> password de la BD
 * @param type string $table --> tabla de la BD a la que hacer el insert
 * @param type array $arrayInsert --> array con los datos a insertar en $table (Sus claves deben coincidir con el nombre de los campos de la BD)
 * @param type string $rutaLog --> ruta hasta el archivo error_log donde se registran los errores conectar a la BD (por defecto es "../../logs/error_log.log")
 * @return boolean --> devolvera false en caso de que el usuario ya estuviera en la BD, true en caso contrario
 */
function insertInBD($conexionDB, $userDB, $passDB, $table, $arrayInsert, $rutaLog = "../../logs/error_log.log") {
    $errorAddUser = false;
    try {
        //Hacemos la conexión a la BD
        $bd = new PDO($conexionDB, $userDB, $passDB);
        //Concatenamos todas las variables del array a dos strings (uno con las claves y otro con los valores) para poder utilizar la función con cualquier insercción a la BD con strings o int
        $values = "";
        $keys = "";
        foreach ($arrayInsert as $key => $value) {//Concatenamos el nombre de los campos (su clave) y los valores de dichas variables
            if (is_string($value)) {
                $values .= "'$value'";
            } else if (is_numeric($value)) {
                $values .= "$value";
            }
            $keys .= "$key";
            if ($key !== array_key_last($arrayInsert)) {//Si no es el último valor del array pongo la coma
                $values .= ", ";
                $keys .= ", ";
            }
        }
        //Query MySQL de insercción: introduciremos la tabla de la que se trata ($table), los campos ($keys) y los valores a insertar ($values)
        $queryInsert = "INSERT INTO $table ($keys) values ($values);";
        //Ejecutamos la query
        $bd->query($queryInsert);
        //Se cierra la conexión
        $bd = null;
    } catch (Exception $ex) {
        if (str_contains($ex->getMessage(), "1062")) {//Si salta el mensaje de clave primaria repetida
            $errorAddUser = true;
        }
        error_log(date("F j, Y, g:i a") . " - Error con la base de datos: " . $ex->getMessage() . "\n", 3, $rutaLog);
    }
    return $errorAddUser;
}

/**
 * deleteInBD () --> Función para eliminar registros de la base de datos
 * @param type string $conexionDB --> cadena de conexión con la BD
 * @param type string $userDB --> usuario de la BD
 * @param type string $passDB --> password de la BD
 * @param type string $table --> tabla de la BD a la que hacer el insert
 * @param type string $pk --> nombre del campo que utilizaremos para identificar el registro a borrar 
 * @param type int $pkDelete --> valor que tendrá el campo $pk para identificar el registro a borrar
 * @param type string $rutaLog --> ruta hasta el archivo error_log donde se registran los errores conectar a la BD (por defecto es "../../logs/error_log.log")
 */
function deleteInBD($conexionDB, $userDB, $passDB, $table, $pk, $pkDelete, $rutaLog = "../../logs/error_log.log") {
    try {
        //Hacemos la conexión a la BD
        $bd = new PDO($conexionDB, $userDB, $passDB);
        //Query MySQL de borrado: introduciremos la tabla de la que se trata ($table), el campo de la PK ($pk) y el valor de dicho campo ($pkDelete)
        $queryInsert = "DELETE FROM $table WHERE $pk = $pkDelete;";
        //Ejecutamos la query
        $bd->query($queryInsert);
        //Se cierra la conexión
        $bd = null;
    } catch (Exception $ex) {
        error_log(date("F j, Y, g:i a") . " - Error con la base de datos: " . $ex->getMessage() . "\n", 3, $rutaLog);
    }
}

/**
 * updateInBD() --> función para actualizar un campo de ún registro de la BD
 * @param type string $conexionDB --> cadena de conexión con la BD
 * @param type string $userDB --> usuario de la BD
 * @param type string $passDB --> password de la BD
 * @param type string $table --> nombre de la tabla donde se realiza la modificación
 * @param type string $pk --> nombre identificador del registro a modificar
 * @param type int $pkUpdate --> valor del identificador del registro a modificar (valor de $pk)
 * @param type string $fieldName --> identificador del campo a actualizar 
 * @param type int $newValue --> nuevo valor que tendrá el campo $fieldName
 * @param type string $rutaLog --> ruta hasta el archivo error_log donde se registran los errores conectar a la BD (por defecto es "../../logs/error_log.log")
 */
function updateInBD($conexionDB, $userDB, $passDB, $table, $pk, $pkUpdate, $fieldName, $newValue, $rutaLog = "../../logs/error_log.log") {
    try {
        //Hacemos la conexión a la BD
        $bd = new PDO($conexionDB, $userDB, $passDB);
        //Query MySQL de actualización:
        $queryInsert = "UPDATE $table SET $fieldName = $newValue WHERE $pk = $pkUpdate;";
        //Ejecutamos la query
        $bd->query($queryInsert);
        //Se cierra la conexión
        $bd = null;
    } catch (Exception $ex) {
        error_log(date("F j, Y, g:i a") . " - Error con la base de datos: " . $ex->getMessage() . "\n", 3, $rutaLog);
    }
}

/**
 * logOutInactivity() --> función para comprobar si se supera un determinado tiempo de inactividad
 * @param type date $now --> fecha actual de la comprobación
 * @param type date $lastActivity --> última fecha de actividad
 * @param type int $secondsAllowed --> segundos de inactividad permitidos
 * @return boolean --> true si el tiempo de inactividad ha sido superado; false en caso contrario
 */
function logOutInactivity($now, $lastActivity, $secondsAllowed) {
    if ((strtotime($now) - strtotime($lastActivity)) > $secondsAllowed) {//Si el tiempo de inactividad es mayor al permitido
        return true;
    } else {//Si no ha excedido el tiempo de inactividad
        return false;
    }
}

/**
 * selectQuery() --> función que devuelve el objeto de la query que se la pasa como parámetro
 * @param type string $conexionDB --> cadena de conexión con la BD
 * @param type string $userDB --> usuario de la BD
 * @param type string $passDB --> password de la BD
 * @param type string $query --> consulta SELECT que se desea realizar
 * @param type boolean &$resultados --> parámetro que pasará a ser falso en caso de no encontrar ningún resultado en la query
 * @param type string $rutaLog --> ruta hasta el archivo error_log donde se registran los errores conectar a la BD (por defecto es "../../logs/error_log.log")
 * @return type devuelve el objeto resultante de la consulta $query realizada
 */
function selectQuery($conexionDB, $userDB, $passDB, $query, &$resultados, $rutaLog = "../../logs/error_log.log") {
    $select = null;
    try {
        $bd = new PDO($conexionDB, $userDB, $passDB);
        $select = $bd->query($query);
        if ($select->rowCount() === 0) {
            $resultados = false;
        }
        //Se cierra la conexión
        $bd = null;
    } catch (Exception $ex) {
        error_log(date("F j, Y, g:i a") . " - Error con la base de datos: " . $ex->getMessage() . "\n", 3, $rutaLog);
    }
    return $select;
}

/**
 * listarProductos() --> Función que listará los productos que se le pasen en $items en cajas y según el $rol mostrará una funcionalidad u otra
 * @param type PDO object $items --> todos los items (rows) a listar resultantes de una consulta a la BD
 * @param type string $rol --> rol del usuario que ha iniciado sesión
 * @param type string $token --> token de seguridad del usuario que ha iniciado sesión
 */
function listarProductos($items, $rol, $token) {
    foreach ($items as $item) {//Recorro todos los productos
        echo "<div class='item'>";
        echo "<div class='item__divImg'><img class='item__img' src='../assets/images/" . $item['imgSRC'] . "' alt='Imagen de " . $item['Nombre del Producto'] . "'></div>";
        echo "<div class='item__body'><h2 class='item__title'>" . $item['Nombre del Producto'] . "</h2>";
        echo "<ul class='item__ul'>";
        foreach ($item as $key => $value) {//Recorro cada campo de cada producto
            if (is_string($key) && $value !== "" && !str_contains($key, "key") && $key !== "Nombre del Producto" && $key !== "imgSRC") {//Si las claves no son string, el campo está vacío, se trata de algún identificador, o del src de la imagen, no lo muestro
                if ($key !== "Peso") {
                    echo "<li class='item__li'>";
                    echo "<h3 class='li__title'>$key</h3><p class='li__text'>$value";
                    if ($key === "Cantidad disponible") {//Si es la cantidad disponible indico que se trata de Kg
                        echo " unidades<span class='item__peso'> (" . $item['Peso'] . " kg/ud)</span>";
                    }
                    echo "</p></li>";
                }
            }
        }
        echo "</ul>";
        if ($rol === "cliente") {
            echo "<form class='item__li item__form' method='POST' action='./addPedido.php'>";
            echo "<div><label class='li__title'>Cantidad solicidatada</label> <input type='number' name='cantidadPedido' step='.01' placeholder='0' class='item__input' min='0' max='" . $item["Cantidad disponible"] . "'>"
            . "<input type='hidden' name='idProducto' value=" . $item["keyProducto"] . "><input type='hidden' name='idEmpresa' value=" . $item["keyEmpresa"] . ">"
            . "<input type='hidden' name='token' value='" . $token . "'></div>"
            . "<button type='submit' class='item__btn'>Solicitar</button>";
            echo "</form>";
        } else {
            //Compruebo si el producto tiene algún pedido y si es el caso lo incluyo en un input hidden:
            $query = "SELECT * FROM pedidos WHERE idProducto = '" . $item['keyProducto'] . "';";
            $resultados = true;
            selectQuery("mysql:dbname=appcomida;host=127.0.0.1", "root", "", $query, $resultados);
            echo "<form class='item__li item__form' method='POST' action='./removeItem.php'><div>";
            if ($resultados) {//Si el producto tiene pedidos en la BD
                echo "<input type='hidden' name='pedidos' value='true'>";
                echo "<p class = 'item__avisoProducto'>*Producto con pedidos activos (si se elimina también se eliminarán sus pedidos asociados)</p>";
            }
            echo "<input type='hidden' name='idProducto' value=" . $item["keyProducto"] . "><input type='hidden' name='idEmpresa' value=" . $item["keyEmpresa"] . ">"
            . "<input type='hidden' name='token' value='" . $token . "'></div>"
            . "<div class='item__btns'><button type='submit' class='item__btn' formaction='./editItem.php'>Editar producto</button><button type='submit' class='item__btn'>Eliminar</button></div>";
            echo "</form>";
        }

        echo '</div></div>';
    }
}

/**
 * checkStock () --> Función para comprobar si hay stock suficiente de un producto solicitado para crear el pedido
 * @param type string $conexionDB --> cadena de conexión con la BD
 * @param type string $user --> usuario de la BD
 * @param type string $pass --> password de la BD
 * @param type int $idProducto --> identificador del producto que se desea comprobar el stock
 * @param type int $cantidadPedido --> cantidad solicitada del producto
 * @param type string $rutaLog --> ruta hasta el archivo error_log donde se registran los errores conectar a la BD (por defecto es "../../logs/error_log.log")
 * @return type int $stock --> devolverá -1 en caso de no disponer de suficiente stock y el stock disponible tras el pedido en caso contrario
 */
function checkStock($conexionDB, $user, $pass, $idProducto, $cantidadPedido, $rutaLog = "../../logs/error_log.log") {
    $stock = -1;
    try {
        $bd = new PDO($conexionDB, $user, $pass);
        $stockSQL = "SELECT * FROM productos WHERE idProducto = $idProducto AND stock >= :cantidadSolicitada";
        $preparada_stock = $bd->prepare($stockSQL);
        $preparada_stock->execute(array(":cantidadSolicitada" => $cantidadPedido));
        $stockSuficiente = ($preparada_stock->rowCount() === 0) ? false : true;
        if ($stockSuficiente) {//Si hay stock suficiente compruebo si ha solicitado todo el stock restante o no
            foreach ($preparada_stock as $row) {//Comprobamos si el stock es todo el disponible
                $stock = $row['stock'] - $cantidadPedido;
            }
        }
        //Se cierra la conexión
        $bd = null;
    } catch (Exception $ex) {
        error_log(date("F j, Y, g:i a") . " - Error con la base de datos: " . $ex->getMessage() . "\n", 3, $rutaLog);
    }
    return $stock;
}


/**
 * updateInBDFromArray() --> Función para actualizar varios campos de un registro de la BD pasandole el array con los nuevos valores
 * @param type string $conexionDB --> cadena de conexión con la BD
 * @param type string $userDB --> usuario de la BD
 * @param type string $passDB --> password de la BD
 * @param type string $table --> nombre de la tabla donde se realiza la modificación
 * @param type string $pk --> nombre identificador del registro a modificar
 * @param type int $pkUpdate --> valor del identificador del registro a modificar (valor de $pk)
 * @param type array $arrayNewValues --> array con las claves que se desean actualizar de la bd y los valores (string o numéricos) nuevos (*Las claves deben ser iguales que los campos de la BD)
 * @param type string $rutaLog --> ruta hasta el archivo error_log donde se registran los errores conectar a la BD (por defecto es "../../logs/error_log.log")
 */
function updateInBDFromArray($conexionDB, $userDB, $passDB, $table, $pk, $pkUpdate, $arrayNewValues, $rutaLog = "../../logs/error_log.log") {
    try {
        //Hacemos la conexión a la BD
        $bd = new PDO($conexionDB, $userDB, $passDB);
        $values = "";
        foreach ($arrayNewValues as $key => $value) {//Concatenamos el nombre de los campos (su clave) y los valores de dichas variables
            if (is_string($value)) {
                $values .= "$key = '$value'";
            } else if (is_numeric($value)) {
                $values .= "$key = $value";
            }
            if ($key !== array_key_last($arrayNewValues)) {//Si no es el último valor del array pongo la coma
                $values .= ", ";
            }
        }
        //Query MySQL de actualización:
        $queryInsert = "UPDATE $table SET $values WHERE $pk = $pkUpdate;";
        //Ejecutamos la query
        $bd->query($queryInsert);
        //Se cierra la conexión
        $bd = null;
    } catch (Exception $ex) {
        error_log(date("F j, Y, g:i a") . " - Error con la base de datos: " . $ex->getMessage() . "\n", 3, $rutaLog);
    }
}

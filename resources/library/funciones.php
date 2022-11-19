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
 * checkBD() --> Comprueba la conexión (true) o no (false) de la base de datos indicada en los parámetros
 * @param type string $conexionDB --> cadena de conexión con la BD
 * @param type string $user --> usuario de la BD
 * @param type string $pass --> password de la BD
 * @return boolean --> true si la conexión se realiza, false en caso contrario
 */
function checkBD($conexionDB, $user, $pass) {
    try {
        $bd = new PDO($conexionDB, $user, $pass);
        //Se cierra la conexión
        $bd = null;
    } catch (Exception $ex) {
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
 */
function createBD($query, $conexionDB, $user, $pass) {
    try {
        $bd = new PDO($conexionDB, $user, $pass);
        //Ejecutamos la query
        $bd->query($query);
        //Se cierra la conexión
        $bd = null;
    } catch (Exception $ex) {
        echo "Error con la base de datos: " . $ex->getMessage();
    }
}

/**
 * checkUser() --> Función para comprobar si los parámetros introducidos existen en la base de datos (si el usuario es correcto)
 * @param type string $conexionDB --> cadena de conexión con la BD
 * @param type string $user --> usuario de la BD
 * @param type string $pass --> password de la BD
 * @param type string $userLogin --> input login del usuario
 * @param type string $passLogin --> input login de la contraseña
 * @return type array --> devuelve en un array el idUsuario y su rol en caso de que sea correcto, en caso contrario devuelve el un array vacio
 */
function checkUser($conexionDB, $user, $pass, $userLogin, $passLogin) {
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
        echo "Error con la base de datos: " . $ex->getMessage();
    }
    return $userChecked;
}

/**
 * insertInBD() --> Función para insertar un array (con valores numéricos o string) en la BD (*Las claves del array deben coincidir con el nombre de los campos de la BD)
 * @param type string $conexionDB --> cadena de conexión con la BD
 * @param type string $userDB --> usuario de la BD
 * @param type string $passDB --> password de la BD
 * @param type string $table --> tabla de la BD a la que hacer el insert
 * @param type array $arrayUser --> array con los datos a insertar en $table (Sus claves deben coincidir con el nombre de los campos de la BD)
 * @return boolean --> devolvera false en caso de que el usuario ya estuviera en la BD, true en caso contrario
 */
function insertInBD($conexionDB, $userDB, $passDB, $table, $arrayUser) {
    $errorAddUser = false;
    try {
        //Hacemos la conexión a la BD
        $bd = new PDO($conexionDB, $userDB, $passDB);
        //Concatenamos todas las variables del array a dos strings (uno con las claves y otro con los valores) para poder utilizar la función con cualquier insercción a la BD con strings o int
        $values = "";
        $keys = "";
        foreach ($arrayUser as $key => $value) {//Concatenamos el nombre de los campos (su clave) y los valores de dichas variables
            if (is_string($value)) {
                $values .= "'$value'";
            } else if (is_numeric($value)) {
                $values .= "$value";
            }
            $keys .= "$key";
            if ($key !== array_key_last($arrayUser)) {//Si no es el último valor del array pongo la coma
                $values .= ", ";
                $keys .= ", ";
            }
        }
        //Query MySQL de insercción: introduciremos la tabla de la que se trata ($tables), los campos ($keys) y los valores a insertar ($values)
        $queryInsert = "INSERT INTO $table ($keys) values ($values);";
        //Ejecutamos la query
        $bd->query($queryInsert);
        //Se cierra la conexión
        $bd = null;
    } catch (Exception $ex) {
        if (str_contains($ex->getMessage(), "1062")) {//Si salta el mensaje de clave primaria repetida
            $errorAddUser = true;
        }
    }
    return $errorAddUser;
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

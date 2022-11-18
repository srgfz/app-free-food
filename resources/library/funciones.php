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
 * checkUser() --> Función para comprobar si los parámetros introducidos existen en la base de datos (si el usuario es correcto)
 * @param type string $userLogin --> input login del usuario
 * @param type string $passLogin --> input login de la contraseña
 * @return type array --> devuelve en un array el idUsuario y su rol en caso de que sea correcto, en caso contrario devuelve el un array vacio
 */
function checkUser($userLogin, $passLogin) {
    $user = [];
    try {
        $bd = new PDO("mysql:dbname=appcomida;host=127.0.0.1", "root", "");
        $loginSQL = "SELECT userId, pass, rol FROM usuarios WHERE userId = :userId AND pass = :pass";
        $preparada_user = $bd->prepare($loginSQL);
        $preparada_user->execute(array(":userId" => $userLogin, ":pass" => $passLogin));
        $login = ($preparada_user->rowCount() === 0) ? false : true;
        if ($login) {//Si las credenciales son correctas
            foreach ($preparada_user as $row) {//Guardamos el usuario y su rol
                $user[0] = $row['userId'];
                $user[1] = $row['rol'];
            }
        }
        //Se cierra la conexión
        $bd = null;
    } catch (Exception $ex) {
        echo "Error con la base de datos: " . $ex->getMessage();
    }
    return $user;
}

/**
 * addUser --> 
 * @param type $userId
 * @param type $pass
 * @param type $nombre
 * @param type $apellidos
 * @param type $email
 * @param type $direccion
 * @param type $rol
 */
function addUser($userId, $pass, $nombre, $apellidos, $email, $direccion, $rol) {
    $errorAddUser = false;
    try {
        //Hacemos la conexión a la BD
        $bd = new PDO("mysql:dbname=appcomida;host=127.0.0.1", "root", "");
        //Query MySQL de insercción: 
        $queryInsert = "INSERT INTO usuarios (userId, pass, nombre, apellidos, email, direccion, rol)"
                . " values ('$userId', '$pass', '$nombre', '$apellidos', '$email', '$direccion', '$rol')";
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

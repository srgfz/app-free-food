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
            if (is_array($value)) {//Si dentro del array hay algún array
                foreach ($value as &$value2) {
                    $value2 = htmlspecialchars($value2);
                }
            } else {
                $value = htmlspecialchars($value);
            }
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

function BDconexion ($cadena_conexion,$usuario,$clave){
    return new PDO($cadena_conexion,$usuario,$clave);
}



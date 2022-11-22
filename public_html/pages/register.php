<?php
//Iniciamos o nos unimos a la sesión
session_start();
if (isset($_SESSION["token"]) && isset($_SESSION["usuario"])) {//Si la sesión existe le redirijo directamente a home.php
    header("Location: ./home.php");
}
//Añado la libreria de funciones
include "../../resources/library/funciones.php";

//Comprobamos si existe la BD en el localhost con usuario root y contraseña en blanco, en caso de no existir se ejecuta el código sql para crearla
if (!checkBD("mysql:dbname=appcomida;host=127.0.0.1", "root", "")) {//Si la base de datos no existe la creo con sus tablas y datos por defecto
    $queryBD = file_get_contents("../../BDappcomida.sql");
    createBD($queryBD, "mysql:;host=127.0.0.1", "root", "");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {//Si recibe un método POST
    $errorRegister = false;
    $datosRegistro = filtrarArrayInput("userRegister", ["userId", "nombre", "email", "direccion", "rol"], $errorRegister);
    //Añado al array de datos de registro la contraseña cifrada:    
    $datosRegistro["pass"] = sha1(filtrarInput("password", "POST"));

    if (!$errorRegister) {//Si los datos son correctos 
        //Intento añadir los datos a la BD y guardo un booleano si se ha realizado la inserción (false) o si el userId que actúa como PK está repetido (true).
        //Todos los demás datos sí podrán repetirse (email, dirección, etc)
        $errorRegisterUser = insertInBD("mysql:dbname=appcomida;host=127.0.0.1", "root", "", "usuarios", $datosRegistro);
        if (!$errorRegisterUser) {//Si el registro es correcto creo la sesión con su userId y su rol y le redirijo a home.php
            $user = [$datosRegistro["userId"], $datosRegistro["rol"]];
            $_SESSION["usuario"] = checkUser("mysql:dbname=appcomida;host=127.0.0.1", "root", "", $datosRegistro["userId"], $datosRegistro["pass"]);
            $_SESSION["token"] = hash("sha256", session_id() . date("Y-n-j H:i:s")); //Guardo el token de la sesión
            //Guardamos dos cookies: una con la hora de login del usuario y otra con la de la última actividad (en el login serán ambas la misma hora)
            setcookie("horaLogin", date("Y-n-j H:i:s"), time() + 3600 * 24, "/");
            setcookie("horaUltimaActividad", date("Y-n-j H:i:s"), time() + 3600 * 24, "/");
            //Le redirijo a home.php
            header("Location: ./home.php");
        }
    }
}
?>

<!DOCTYPE html>
<!--
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Project/PHP/PHPProject.php to edit this template
-->
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>FOODY | Registro</title>
        <link rel="stylesheet" href="../css/forms.css">
        <link rel="stylesheet" href="../css/register.css">

    </head>
    <body>
        <div class="caja__login caja__register">
            <h1 class="login__titulo">FOODY</h1>

            <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" class="form__register">
                <div class="login__row">
                    <div class="login__usuario register__usuario">
                        <input type="text" name="userRegister[userId]" required>
                        <label>Usuario*</label>
                    </div>
                    <div class="login__usuario register__usuario">
                        <input type="password" name="password" required>
                        <label>Contraseña*</label>
                    </div>
                </div>
                <div class="login__row">

                    <div class="login__usuario register__usuario">
                        <input type="text" name="userRegister[nombre]" required>
                        <label>Nombre*</label>
                    </div>
                    <div class="login__usuario register__usuario">
                        <input type="email" name="userRegister[email]" required>
                        <label>Email*</label>
                    </div>
                </div>
                <div class="login__row">
                    <div class="login__usuario register__usuario register__usuario--large">
                        <input type="text" name="userRegister[direccion]" required>
                        <label>Dirección*</label>
                    </div>
                </div>
                <div class="login__row">

                    <div class="login__usuario register__rol">
                        <div class="rol__radio">
                            <span class="register__rolLabel">Cliente</span>
                            <input type="radio" name="userRegister[rol]" required value="cliente">
                        </div>
                        <div class="rol__radio">
                            <span class="register__rolLabel">Empresa</span>
                            <input type="radio" name="userRegister[rol]" required value="empresa">
                        </div>
                    </div>
                </div>

                <?php
                if (isset($errorRegister) && $errorRegister) {
                    echo "<p class='error'>* Debe rellenar todos los campos obligatorios</p>";
                } else if (isset($errorRegisterUser) && $errorRegisterUser) {//Si el usuario estaba repetido
                    echo "<p class='error'>* Usuario en uso, por favor introduzca otro usuario</p>";
                }
                ?>

                <p class="registro">¿Ya tienes cuenta? <a class="registrate__link"  href="../index.php"> Iniciar sesión</a></p>


                <div class="loginButtons">
                    <button class="enviar"  type="submit">
                        <span class="linea"></span>
                        <span class="linea"></span>
                        <span class="linea"></span>
                        <span class="linea"></span>
                        Registrate
                    </button>
                </div>

            </form>

        </div>
    </body>
</html>

<?php
//Iniciamos o nos unimos a la sesión
session_start();
//if(isset($_SESSION["usuario"])){//Si la sesión existe le redirijo directamente a home.php
//    header("Location: ./pages/home.php");
//}
//Añado la libreria de funciones
include "../../resources/library/funciones.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {//Si recibe un método POST
    $errorRegister = false;
    $datosRegistro = filtrarArrayInput("userRegister", ["user", "pass", "name", "lastname", "email", "tel", "adress", "rol"], $errorRegister);
    if(!$errorRegister){//Si los datos son correctos 
        //Añado los datos a la BD:
        print_r($datosRegistro);
        addUser($datosRegistro["user"], $datosRegistro["pass"], $datosRegistro["name"], $datosRegistro["lastname"], $datosRegistro["email"], $datosRegistro["tel"], $datosRegistro["adress"], $datosRegistro["rol"]);
    }
    
}
?>

<!DOCTYPE html>
<!--
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Project/PHP/PHPProject.php to edit this template
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>FOODY</title>
        <link rel="stylesheet" href="../css/forms.css">
    </head>
    <body>
        <div class="caja__login">

            <h2 class="login__titulo">FOODY</h2>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

                <div class="login__usuario">
                    <input type="text" name="userRegister[user]" required>
                    <label>Usuario*</label>
                </div>
                <div class="login__usuario">
                    <input type="password" name="userRegister[pass]" required>
                    <label>Contraseña*</label>
                </div>
                <div class="login__usuario">
                    <input type="text" name="userRegister[name]" required>
                    <label>Nombre*</label>
                </div>
                <div class="login__usuario">
                    <input type="text" name="userRegister[lastname]" required>
                    <label>Apellidos*</label>
                </div>
                <div class="login__usuario">
                    <input type="email" name="userRegister[email]" required>
                    <label>Email*</label>
                </div>
                <div class="login__usuario">
                    <input type="tel" name="userRegister[tel]" required>
                    <label>Móvil*</label>
                </div>
                <div class="login__usuario">
                    <input type="text" name="userRegister[adress]" required>
                    <label>Dirección*</label>
                </div>
                <div class="login__usuario register__rol">
                    <span id="rolCliente" class="register__rolLabel">Cliente</span>
                    <input type="radio" name="userRegister[rol]" required id="rolCliente" value="cliente">
                    <span id="rolEmpresa" class="register__rolLabel">Empresa</span>
                    <input type="radio" name="userRegister[rol]" required id="rolEmpresa" value="empresa">
                </div>
                <?php
                if (isset($errorRegister) && $errorRegister) {//Si el usuario o la cotraseña no son correctas mostramos el error
                    echo "<p class='error'>Datos incompletos, por favor complete todos los datos obligatorios*</p>";
                }
                ?>
                <div class="loginButtons">
                    <button class="enviar"  type="submit">
                        <span class="linea-reg"></span>
                        <span class="linea-reg"></span>
                        <span class="linea-reg"></span>
                        <span class="linea-reg"></span>
                        Registrarse
                    </button>
                </div>


            </form>
        </div>

    </body>
</html>

 <header class="header">
                <!--********** Inicio del nav **********-->
                <nav class="nav">
                    <ul class="nav__ul">
                        <li class="nav__li">
                            <div class="nav__logo"><a href="./home.php" class=""><img class="nav--logo__img" src="../../public_html/assets/images/logo.png" alt="alt"/></a></div>
                        </li>
                        <li class="nav__li nav__btn">
                            <a href="./home.php" class="nav__link">
                                <?php
                                $btnText = $rol === "empresa" ? "Mis Productos" : "Listar Productos";
                                echo $btnText; //Según el rol del usuario el contenido del botón cambia
                                ?>
                            </a>
                        </li>
                        <?php 
                        if($rol === "empresa"){//Botón de añadir un nuevo producto
                            echo "<li class='nav__li nav__btn'><a href='./addItem.php' class='nav__link'>Añadir Producto</a></li>";
                        }
                        ?>
                        <li class="nav__li">
                            <form action="./home.php" method="POST">
                                <div class="nav__search">
                                    <input type="search" class="search__input" placeholder="Buscar" name="items">
                                    <input type="hidden" name="token" value="<?php echo $tokenSession; ?>">
                                    <button type="submit" class="search__btn">
                                        <span class="material-symbols-outlined">
                                            search
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </li>

                        <li class="nav__li nav__lastItem">
                            <form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
                                <select name="tema">
                                    <?php imprimirOptions(["Tema Claro", "Tema Oscuro"], $tema) ?>
                                </select> 
                                <input type="hidden" name="token" value="<?php echo $tokenSession; ?>">
                                <button type="submit">Cambiar</button>
                            </form>
                            <a href="./logOut.php" class="nav__a nav__logOut">Salir 
                                <span class="material-symbols-outlined">
                                    logout
                                </span>
                            </a>
                        </li>
                    </ul>
                </nav>
                <!--********** Fin del nav **********-->
            </header>
-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-11-2022 a las 13:09:57
-- Versión del servidor: 10.4.24-MariaDB
-- Versión de PHP: 8.0.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `appcomida`
--
CREATE DATABASE IF NOT EXISTS `appcomida` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `appcomida`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `codPedido` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `fechaPedido` date DEFAULT current_timestamp(),
  `idProducto` int(11) NOT NULL,
  `idEmpresa` varchar(10) NOT NULL,
  `idCliente` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `idProducto` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `stock` int(11) NOT NULL,
  `fechaCaducidad` varchar(20) NOT NULL,
  `descripción` varchar(500) DEFAULT NULL,
  `idEmpresa` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`idProducto`, `nombre`, `stock`, `fechaCaducidad`, `descripción`, `idEmpresa`) VALUES
(1, 'Barra de Pan', 15, '01/01/2023', '', 'empresa1'),
(2, 'Foskitos', 30, '10/01/2023', '', 'empresa1'),
(3, 'Weikis', 25, '28/12/2022', '', 'empresa1'),
(4, 'Haribo', 15, '02/01/2023', '', 'empresa2'),
(5, 'Acelgas', 15, '27/01/2023', '', 'empresa2');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `userId` varchar(20) NOT NULL,
  `pass` varchar(200) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `email` varchar(50) NOT NULL,
  `direccion` varchar(50) NOT NULL,
  `rol` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`userId`, `pass`, `nombre`, `email`, `direccion`, `rol`) VALUES
('admin1', '6c7ca345f63f835cb353ff15bd6c5e052ec08e7a', 'Paco', 'pacopaquete@gmail.com', 'Calle Comuneros de Castilla', 'admin'),
('admin2', '315f166c5aca63a157f7d41007675cb44a948b33', 'Roberto', 'roberto@gmail.com', 'Calle Mejorada', 'admin'),
('empresa1', 'd559c7e8d82339927e76122c07aed2c8d47daa5c', 'Panadería Robles', 'marcopolo@gmail.com', 'Calle San Antón', 'empresa'),
('empresa2', 'd53acecbe4d74e7fb3476a1fe997e949536f0a7d', 'Supermercado Paqui', 'ruben@gmail.com', 'Calle San Vicente', 'empresa'),
('user1', 'b3daa77b4c04a9551b8781d03191fe098f325e67', 'Antonio', 'anton@gmail.com', 'Av. Pio XII', 'cliente'),
('user2', 'a1881c06eec96db9901c7bbfe41c42a3f08e9cb4', 'Manolo', 'manolete@gmail.com', 'Calle Lagartera', 'cliente');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`codPedido`),
  ADD KEY `pedidos_cliente` (`idCliente`),
  ADD KEY `pedidos_empresa` (`idEmpresa`),
  ADD KEY `pedidos_producto` (`idProducto`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`idProducto`),
  ADD KEY `productos_emoresaFK` (`idEmpresa`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`userId`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `codPedido` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `idProducto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_cliente` FOREIGN KEY (`idCliente`) REFERENCES `usuarios` (`userId`),
  ADD CONSTRAINT `pedidos_empresa` FOREIGN KEY (`idEmpresa`) REFERENCES `usuarios` (`userId`),
  ADD CONSTRAINT `pedidos_producto` FOREIGN KEY (`idProducto`) REFERENCES `productos` (`idProducto`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_emoresaFK` FOREIGN KEY (`idEmpresa`) REFERENCES `usuarios` (`userId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

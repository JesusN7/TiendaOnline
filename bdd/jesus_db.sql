-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 01-03-2025 a las 20:44:12
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `jesus_db`
--
CREATE DATABASE IF NOT EXISTS `jesus_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `jesus_db`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `categoriaPadre` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `activo`, `categoriaPadre`) VALUES
(1, 'Velas', 1, NULL),
(2, 'Quemadores', 1, NULL),
(3, 'Minerales', 1, NULL),
(22, 'Velas de miel', 1, 1),
(23, 'Velas aromáticas', 1, 1),
(24, 'Quemadores grandes', 1, 2),
(25, 'Quemadores pequeños', 1, 2),
(26, 'Pirámides de shunguita', 1, 3),
(27, 'Cuarzos', 1, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lineapedido`
--

CREATE TABLE `lineapedido` (
  `numPedido` int(11) NOT NULL,
  `numLinea` int(11) NOT NULL,
  `codigo_producto` varchar(6) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `descuento` float NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `lineapedido`
--

INSERT INTO `lineapedido` (`numPedido`, `numLinea`, `codigo_producto`, `cantidad`, `precio`, `descuento`) VALUES
(7, 1, 'VMM002', 1, 8.00, 0),
(8, 1, 'PBV001', 2, 12.00, 0),
(8, 2, 'VMM002', 1, 8.00, 0);

--
-- Disparadores `lineapedido`
--
DELIMITER $$
CREATE TRIGGER `after_insert_lineaPedido` AFTER INSERT ON `lineapedido` FOR EACH ROW BEGIN
    -- Actualizar stock y estado del producto
    UPDATE productos
    SET stock = GREATEST(stock - NEW.cantidad, 0),
        activo = IF(stock - NEW.cantidad <= 0, 0, activo)
    WHERE codigo = NEW.codigo_producto;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_insert_lineaPedido` BEFORE INSERT ON `lineapedido` FOR EACH ROW BEGIN
    DECLARE stock_actual INT;
    DECLARE producto_activo TINYINT;

    -- Obtener stock y estado del producto
    SELECT stock, activo INTO stock_actual, producto_activo
    FROM productos
    WHERE codigo = NEW.codigo_producto;

    -- Validar stock
    IF stock_actual < NEW.cantidad THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No hay suficiente stock para este producto';
    END IF;

    -- Validar estado del producto
    IF producto_activo = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El producto no está disponible';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `numPedido` int(11) NOT NULL,
  `usuario_id` varchar(9) DEFAULT NULL,
  `fecha_pedido` timestamp NOT NULL DEFAULT current_timestamp(),
  `total` decimal(10,2) NOT NULL,
  `estado` enum('pendiente','enviado','cancelado','entregado') DEFAULT 'pendiente',
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`numPedido`, `usuario_id`, `fecha_pedido`, `total`, `estado`, `activo`) VALUES
(7, '89462239J', '2025-02-28 17:59:52', 8.00, 'pendiente', 0),
(8, '89462239J', '2025-03-01 14:32:35', 32.00, 'entregado', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `codigo` varchar(6) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` mediumtext DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL,
  `descuento` float NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`codigo`, `nombre`, `descripcion`, `precio`, `stock`, `categoria_id`, `imagen`, `activo`, `descuento`) VALUES
('PBV001', 'Pirámide Shunguita', 'Pirámide Shunguita, 4x4 cm.', 12.00, 8, 26, 'minerales1.jpg', 1, 0),
('VMM001', 'Velas de miel', 'Caja de 6 velas de miel realizadas con aceites esenciales.', 12.00, 3, 22, 'velas1.jpg', 1, 5),
('VMM002', 'Velones de miel', 'Pack de dos velones de miel de 10x4 cm.', 8.00, 5, 22, 'velas2.jpg', 1, 5),
('VMM003', 'Vela de miel con mecha de madera', 'Vela de miel con mecha de madera con forma de flor de loto de 10 cm de alto.', 5.00, 2, 22, 'velas3.jpg', 1, 0),
('XRR069', 'Quemador', 'Quemador de aceites esenciales con acabado en blanco hecho a mano.', 15.00, 5, 24, 'aceites1.jpg', 1, 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` varchar(9) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `rol` enum('cliente','empleado','admin') NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `localidad` varchar(100) DEFAULT NULL,
  `provincia` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `direccion`, `telefono`, `rol`, `activo`, `localidad`, `provincia`) VALUES
('65144730J', 'Pedro', 'pedro@gmail.com', '$2y$10$/Splqiu/7uisui0Cf3/TtuhmdWXVkcV537MnAiLXbEl/9X5xdCzO.', 'C/ Fantasma, 99', '678771100', 'empleado', 1, 'Elche', 'Alicante'),
('71824614F', 'Mario', 'mario@gmail.com', '$2y$10$JyKhoNVE/sW9yfaOmdZOVuLXs3Bhv3n.nohPKWgZuGLAT8tE5Xpk.', 'C/ Estrella, 64', '640998765', 'cliente', 1, 'Elche', 'Alicante'),
('74366466Z', 'Jesús', 'jesus@gmail.com', '$2y$10$M4zplQwv5VGEhsLchIamvu6EinTTXMeEdlMcrexZrsb.FLGyl3aVa', 'Calle Andromeda, 15', '650772100', 'admin', 1, 'Elche', 'Alicante'),
('87654321X', 'Marta', 'marta@gmail.com', '$2y$10$SfhbSmsO3BKGsdAhYAK/ROgVjx3EOuYxlVIHK7HDQISKHnSTPQhKG', 'Avenida Luna, 10', '666117709', 'empleado', 1, 'Elche', 'Alicante'),
('89462239J', 'John', 'john@gmail.com', '$2y$10$OQg.MG/VmGtfODm5FCED/.tNB39/11dJ7sdlhQXBKzLUQ2HhFPdES', 'C/ Cerberus', '650998102', 'cliente', 1, 'Elche', 'Alicante');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_categoria_padre` (`categoriaPadre`);

--
-- Indices de la tabla `lineapedido`
--
ALTER TABLE `lineapedido`
  ADD PRIMARY KEY (`numPedido`,`numLinea`),
  ADD KEY `fk_lineaPedido_producto` (`codigo_producto`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`numPedido`),
  ADD KEY `fk_pedidos_usuario` (`usuario_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`codigo`),
  ADD KEY `fk_productos_categoria` (`categoria_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `numPedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD CONSTRAINT `fk_categoria_padre` FOREIGN KEY (`categoriaPadre`) REFERENCES `categorias` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `lineapedido`
--
ALTER TABLE `lineapedido`
  ADD CONSTRAINT `fk_lineaPedido_producto` FOREIGN KEY (`codigo_producto`) REFERENCES `productos` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_productos_pedido_pedido` FOREIGN KEY (`numPedido`) REFERENCES `pedidos` (`numPedido`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `fk_pedidos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_productos_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

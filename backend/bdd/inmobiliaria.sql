-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-10-2026 a las 01:29:37
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `inmobiliaria`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alquiler`
--

CREATE TABLE `alquiler` (
  `id_alquiler` int(11) NOT NULL,
  `id_inmobiliaria_propiedad` int(11) NOT NULL,
  `id_inquilino` int(11) NOT NULL,
  `precio` decimal(12,2) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `direccion`
--

CREATE TABLE `direccion` (
  `id_direccion` int(11) NOT NULL,
  `provincia` varchar(200) NOT NULL,
  `ciudad` varchar(200) NOT NULL,
  `calle` varchar(300) NOT NULL,
  `altura` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `direccion`
--

INSERT INTO `direccion` (`id_direccion`, `provincia`, `ciudad`, `calle`, `altura`) VALUES
(1, 'Misiones', 'Barrio del Lago', 'San Juan', 520),
(8, 'Catamarca', 'Alijilán', 'Buchardo', 250),
(30, 'Mendoza', 'Agrelo', 'San Juan', 4),
(33, 'Buenos Aires', 'Tres Arroyos', 'Passo', 950);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_propiedad`
--

CREATE TABLE `estado_propiedad` (
  `id_estado_propiedad` int(11) NOT NULL,
  `estado` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `estado_propiedad`
--

INSERT INTO `estado_propiedad` (`id_estado_propiedad`, `estado`) VALUES
(1, 'EN VENTA'),
(2, 'EN ALQUILER'),
(3, 'ALQUILADO'),
(4, 'VENDIDO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inmobiliaria`
--

CREATE TABLE `inmobiliaria` (
  `id_inmobiliaria` int(11) NOT NULL,
  `id_direccion` int(11) DEFAULT NULL,
  `nombre` varchar(150) NOT NULL,
  `email` varchar(200) NOT NULL,
  `area_telefono` varchar(10) NOT NULL,
  `telefono` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `inmobiliaria`
--

INSERT INTO `inmobiliaria` (`id_inmobiliaria`, `id_direccion`, `nombre`, `email`, `area_telefono`, `telefono`) VALUES
(1, 1, 'pecaan', 'jesmaster.08@gmail.com', '2541', '542012');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inmobiliaria_propiedad`
--

CREATE TABLE `inmobiliaria_propiedad` (
  `id_inmobiliaria_propiedad` int(11) NOT NULL,
  `id_inmobiliaria` int(11) NOT NULL,
  `id_propiedad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `inmobiliaria_propiedad`
--

INSERT INTO `inmobiliaria_propiedad` (`id_inmobiliaria_propiedad`, `id_inmobiliaria`, `id_propiedad`) VALUES
(7, 1, 7),
(29, 1, 29),
(32, 1, 32);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inquilino`
--

CREATE TABLE `inquilino` (
  `id_inquilino` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(200) NOT NULL,
  `area_telefono` varchar(10) NOT NULL,
  `telefono` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `propiedad`
--

CREATE TABLE `propiedad` (
  `id_propiedad` int(11) NOT NULL,
  `id_direccion` int(11) NOT NULL,
  `id_estado_propiedad` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `descripcion` varchar(600) NOT NULL,
  `precio` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `propiedad`
--

INSERT INTO `propiedad` (`id_propiedad`, `id_direccion`, `id_estado_propiedad`, `titulo`, `descripcion`, `precio`) VALUES
(7, 8, 2, 'Lote en San Agus', 'sadgfsdfgadfsg en dolares', 45000.00),
(29, 30, 4, 'Lote en San Agustin', 'sdfgsd', 52.00),
(32, 33, 1, 'Casa para consultorio', 'Casa para alquilar', 600000.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `propiedad_foto`
--

CREATE TABLE `propiedad_foto` (
  `id_foto` int(11) NOT NULL,
  `id_propiedad` int(11) NOT NULL,
  `ruta_foto` varchar(500) NOT NULL,
  `orden` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `propiedad_foto`
--

INSERT INTO `propiedad_foto` (`id_foto`, `id_propiedad`, `ruta_foto`, `orden`) VALUES
(20, 7, 'cargasImagenes/fotosPropiedades/7/foto_6ad29b36523b0.jpeg', 1),
(22, 7, 'cargasImagenes/fotosPropiedades/7/foto_6ad29b3654039.jpeg', 3),
(43, 29, 'cargasImagenes/fotosPropiedades/29/foto_6ad2a67649d7f.jpeg', 1),
(46, 32, 'cargasImagenes/fotosPropiedades/32/foto_6ad6a1ae8fcb8.jpeg', 1),
(47, 32, 'cargasImagenes/fotosPropiedades/32/foto_6ad6a1ae90ad2.jpeg', 2),
(48, 32, 'cargasImagenes/fotosPropiedades/32/foto_6ad6a1ae91533.jpeg', 3),
(49, 32, 'cargasImagenes/fotosPropiedades/32/foto_6ad6a1ae92b1a.jpeg', 4),
(50, 32, 'cargasImagenes/fotosPropiedades/32/foto_6ad6a1ae93599.jpeg', 5),
(51, 7, 'cargasImagenes/fotosPropiedades/7/foto_6ad6a609f3787.jpeg', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recuperacion_contrasenia`
--

CREATE TABLE `recuperacion_contrasenia` (
  `id_recuperacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `token` varchar(10) NOT NULL,
  `fecha_expiracion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `token`
--

CREATE TABLE `token` (
  `id_token` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `fecha_expiracion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `token`
--

INSERT INTO `token` (`id_token`, `id_usuario`, `token`, `fecha_expiracion`) VALUES
(1, 1, 'a21330cf000c2eb9a9137156d7257187750486552a102948d87bad33cad46204', '2026-10-20 04:05:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `id_inmobiliaria` int(11) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(250) NOT NULL,
  `nombre_usuario` varchar(100) NOT NULL,
  `apellido_usuario` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `id_inmobiliaria`, `email`, `password`, `nombre_usuario`, `apellido_usuario`) VALUES
(1, 1, 'jesmaster.08@gmail.com', '$2y$10$6e7lemuIyu3Am/yu6yjcw.eKQxr.tkR3XFak7LXY9idpIvGdY/yHi', 'jesus', 'castillo');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alquiler`
--
ALTER TABLE `alquiler`
  ADD PRIMARY KEY (`id_alquiler`),
  ADD KEY `fk_alquiler_inmobiliaria` (`id_inmobiliaria_propiedad`),
  ADD KEY `fk_alquiler_inquilino` (`id_inquilino`);

--
-- Indices de la tabla `direccion`
--
ALTER TABLE `direccion`
  ADD PRIMARY KEY (`id_direccion`);

--
-- Indices de la tabla `estado_propiedad`
--
ALTER TABLE `estado_propiedad`
  ADD PRIMARY KEY (`id_estado_propiedad`);

--
-- Indices de la tabla `inmobiliaria`
--
ALTER TABLE `inmobiliaria`
  ADD PRIMARY KEY (`id_inmobiliaria`),
  ADD KEY `fk_inmobiliaria_direccion` (`id_direccion`);

--
-- Indices de la tabla `inmobiliaria_propiedad`
--
ALTER TABLE `inmobiliaria_propiedad`
  ADD PRIMARY KEY (`id_inmobiliaria_propiedad`),
  ADD UNIQUE KEY `id_inmobiliaria` (`id_inmobiliaria`,`id_propiedad`),
  ADD KEY `fk_inmobiliaria_propiedad` (`id_propiedad`);

--
-- Indices de la tabla `inquilino`
--
ALTER TABLE `inquilino`
  ADD PRIMARY KEY (`id_inquilino`);

--
-- Indices de la tabla `propiedad`
--
ALTER TABLE `propiedad`
  ADD PRIMARY KEY (`id_propiedad`),
  ADD KEY `fk_propiedad_direccion` (`id_direccion`),
  ADD KEY `fk_propiedad_estado_propiedad` (`id_estado_propiedad`);

--
-- Indices de la tabla `propiedad_foto`
--
ALTER TABLE `propiedad_foto`
  ADD PRIMARY KEY (`id_foto`),
  ADD KEY `id_propiedad` (`id_propiedad`);

--
-- Indices de la tabla `recuperacion_contrasenia`
--
ALTER TABLE `recuperacion_contrasenia`
  ADD PRIMARY KEY (`id_recuperacion`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `token`
--
ALTER TABLE `token`
  ADD PRIMARY KEY (`id_token`),
  ADD UNIQUE KEY `id_usuario` (`id_usuario`),
  ADD UNIQUE KEY `token` (`token`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `uq_usuario_email` (`email`),
  ADD KEY `fk_usuario_inmobiliaria` (`id_inmobiliaria`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alquiler`
--
ALTER TABLE `alquiler`
  MODIFY `id_alquiler` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `direccion`
--
ALTER TABLE `direccion`
  MODIFY `id_direccion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT de la tabla `estado_propiedad`
--
ALTER TABLE `estado_propiedad`
  MODIFY `id_estado_propiedad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `inmobiliaria`
--
ALTER TABLE `inmobiliaria`
  MODIFY `id_inmobiliaria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `inmobiliaria_propiedad`
--
ALTER TABLE `inmobiliaria_propiedad`
  MODIFY `id_inmobiliaria_propiedad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `inquilino`
--
ALTER TABLE `inquilino`
  MODIFY `id_inquilino` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `propiedad`
--
ALTER TABLE `propiedad`
  MODIFY `id_propiedad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `propiedad_foto`
--
ALTER TABLE `propiedad_foto`
  MODIFY `id_foto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT de la tabla `recuperacion_contrasenia`
--
ALTER TABLE `recuperacion_contrasenia`
  MODIFY `id_recuperacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `token`
--
ALTER TABLE `token`
  MODIFY `id_token` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alquiler`
--
ALTER TABLE `alquiler`
  ADD CONSTRAINT `fk_alquiler_inmobiliaria` FOREIGN KEY (`id_inmobiliaria_propiedad`) REFERENCES `inmobiliaria_propiedad` (`id_inmobiliaria_propiedad`),
  ADD CONSTRAINT `fk_alquiler_inquilino` FOREIGN KEY (`id_inquilino`) REFERENCES `inquilino` (`id_inquilino`);

--
-- Filtros para la tabla `inmobiliaria`
--
ALTER TABLE `inmobiliaria`
  ADD CONSTRAINT `fk_inmobiliaria_direccion` FOREIGN KEY (`id_direccion`) REFERENCES `direccion` (`id_direccion`);

--
-- Filtros para la tabla `inmobiliaria_propiedad`
--
ALTER TABLE `inmobiliaria_propiedad`
  ADD CONSTRAINT `fk_inmobiliaria_propiedad` FOREIGN KEY (`id_propiedad`) REFERENCES `propiedad` (`id_propiedad`),
  ADD CONSTRAINT `fk_inmobiliaria_propiedad_inmobiliaria` FOREIGN KEY (`id_inmobiliaria`) REFERENCES `inmobiliaria` (`id_inmobiliaria`);

--
-- Filtros para la tabla `propiedad`
--
ALTER TABLE `propiedad`
  ADD CONSTRAINT `fk_propiedad_direccion` FOREIGN KEY (`id_direccion`) REFERENCES `direccion` (`id_direccion`),
  ADD CONSTRAINT `fk_propiedad_estado_propiedad` FOREIGN KEY (`id_estado_propiedad`) REFERENCES `estado_propiedad` (`id_estado_propiedad`);

--
-- Filtros para la tabla `propiedad_foto`
--
ALTER TABLE `propiedad_foto`
  ADD CONSTRAINT `propiedad_foto_ibfk_1` FOREIGN KEY (`id_propiedad`) REFERENCES `propiedad` (`id_propiedad`) ON DELETE CASCADE;

--
-- Filtros para la tabla `recuperacion_contrasenia`
--
ALTER TABLE `recuperacion_contrasenia`
  ADD CONSTRAINT `recuperacion_contrasenia_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `token`
--
ALTER TABLE `token`
  ADD CONSTRAINT `token_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `fk_usuario_inmobiliaria` FOREIGN KEY (`id_inmobiliaria`) REFERENCES `inmobiliaria` (`id_inmobiliaria`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

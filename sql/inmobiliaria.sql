-- Esquema de la base de datos de Habita (generado a partir del dump del 20/10/2026).
-- Se importa en una base vacia (la base la crea cada uno; despues va en DB_NAME).
-- Trae las tablas y los estados de propiedad, sin datos de prueba ni de usuarios.
-- Cada tabla se crea ya con su clave primaria (hay servicios en linea, como Aiven, que lo exigen).

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

CREATE TABLE `alquiler` (
  `id_alquiler` int(11) NOT NULL AUTO_INCREMENT,
  `id_inmobiliaria_propiedad` int(11) NOT NULL,
  `id_inquilino` int(11) NOT NULL,
  `precio` decimal(12,2) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  PRIMARY KEY (`id_alquiler`),
  KEY `fk_alquiler_inmobiliaria` (`id_inmobiliaria_propiedad`),
  KEY `fk_alquiler_inquilino` (`id_inquilino`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `direccion` (
  `id_direccion` int(11) NOT NULL AUTO_INCREMENT,
  `provincia` varchar(200) NOT NULL,
  `ciudad` varchar(200) NOT NULL,
  `calle` varchar(300) NOT NULL,
  `altura` int(11) NOT NULL,
  PRIMARY KEY (`id_direccion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `estado_propiedad` (
  `id_estado_propiedad` int(11) NOT NULL AUTO_INCREMENT,
  `estado` varchar(150) NOT NULL,
  PRIMARY KEY (`id_estado_propiedad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `inmobiliaria` (
  `id_inmobiliaria` int(11) NOT NULL AUTO_INCREMENT,
  `id_direccion` int(11) DEFAULT NULL,
  `nombre` varchar(150) NOT NULL,
  `email` varchar(200) NOT NULL,
  `area_telefono` varchar(10) NOT NULL,
  `telefono` varchar(10) NOT NULL,
  PRIMARY KEY (`id_inmobiliaria`),
  KEY `fk_inmobiliaria_direccion` (`id_direccion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `inmobiliaria_propiedad` (
  `id_inmobiliaria_propiedad` int(11) NOT NULL AUTO_INCREMENT,
  `id_inmobiliaria` int(11) NOT NULL,
  `id_propiedad` int(11) NOT NULL,
  PRIMARY KEY (`id_inmobiliaria_propiedad`),
  UNIQUE KEY `id_inmobiliaria` (`id_inmobiliaria`,`id_propiedad`),
  KEY `fk_inmobiliaria_propiedad` (`id_propiedad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `inquilino` (
  `id_inquilino` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(200) NOT NULL,
  `area_telefono` varchar(10) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  PRIMARY KEY (`id_inquilino`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `propiedad` (
  `id_propiedad` int(11) NOT NULL AUTO_INCREMENT,
  `id_direccion` int(11) NOT NULL,
  `id_estado_propiedad` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `descripcion` varchar(600) NOT NULL,
  `precio` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id_propiedad`),
  KEY `fk_propiedad_direccion` (`id_direccion`),
  KEY `fk_propiedad_estado_propiedad` (`id_estado_propiedad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `propiedad_foto` (
  `id_foto` int(11) NOT NULL AUTO_INCREMENT,
  `id_propiedad` int(11) NOT NULL,
  `ruta_foto` varchar(500) NOT NULL,
  `orden` int(11) NOT NULL DEFAULT 1,
  `contenido` mediumblob DEFAULT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_foto`),
  KEY `id_propiedad` (`id_propiedad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `recuperacion_contrasenia` (
  `id_recuperacion` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `token` varchar(10) NOT NULL,
  `fecha_expiracion` datetime NOT NULL,
  PRIMARY KEY (`id_recuperacion`),
  KEY `id_usuario` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `token` (
  `id_token` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `fecha_expiracion` datetime NOT NULL,
  PRIMARY KEY (`id_token`),
  UNIQUE KEY `id_usuario` (`id_usuario`),
  UNIQUE KEY `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `id_inmobiliaria` int(11) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(250) NOT NULL,
  `nombre_usuario` varchar(100) NOT NULL,
  `apellido_usuario` varchar(100) NOT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `uq_usuario_email` (`email`),
  KEY `fk_usuario_inmobiliaria` (`id_inmobiliaria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `estado_propiedad` (`id_estado_propiedad`, `estado`) VALUES
(1, 'EN VENTA'),
(2, 'EN ALQUILER'),
(3, 'ALQUILADO'),
(4, 'VENDIDO');

-- Claves foraneas (van al final, cuando ya existen todas las tablas)
ALTER TABLE `alquiler`
  ADD CONSTRAINT `fk_alquiler_inmobiliaria` FOREIGN KEY (`id_inmobiliaria_propiedad`) REFERENCES `inmobiliaria_propiedad` (`id_inmobiliaria_propiedad`),
  ADD CONSTRAINT `fk_alquiler_inquilino` FOREIGN KEY (`id_inquilino`) REFERENCES `inquilino` (`id_inquilino`);
ALTER TABLE `inmobiliaria`
  ADD CONSTRAINT `fk_inmobiliaria_direccion` FOREIGN KEY (`id_direccion`) REFERENCES `direccion` (`id_direccion`);
ALTER TABLE `inmobiliaria_propiedad`
  ADD CONSTRAINT `fk_inmobiliaria_propiedad` FOREIGN KEY (`id_propiedad`) REFERENCES `propiedad` (`id_propiedad`),
  ADD CONSTRAINT `fk_inmobiliaria_propiedad_inmobiliaria` FOREIGN KEY (`id_inmobiliaria`) REFERENCES `inmobiliaria` (`id_inmobiliaria`);
ALTER TABLE `propiedad`
  ADD CONSTRAINT `fk_propiedad_direccion` FOREIGN KEY (`id_direccion`) REFERENCES `direccion` (`id_direccion`),
  ADD CONSTRAINT `fk_propiedad_estado_propiedad` FOREIGN KEY (`id_estado_propiedad`) REFERENCES `estado_propiedad` (`id_estado_propiedad`);
ALTER TABLE `propiedad_foto`
  ADD CONSTRAINT `propiedad_foto_ibfk_1` FOREIGN KEY (`id_propiedad`) REFERENCES `propiedad` (`id_propiedad`) ON DELETE CASCADE;
ALTER TABLE `recuperacion_contrasenia`
  ADD CONSTRAINT `recuperacion_contrasenia_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);
ALTER TABLE `token`
  ADD CONSTRAINT `token_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);
ALTER TABLE `usuario`
  ADD CONSTRAINT `fk_usuario_inmobiliaria` FOREIGN KEY (`id_inmobiliaria`) REFERENCES `inmobiliaria` (`id_inmobiliaria`);

-- fin del esquema

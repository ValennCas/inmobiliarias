-- Migracion: las fotos de las propiedades se guardan en la base de datos.
-- Se corre UNA vez sobre una base que ya existe (Aiven, XAMPP). Las bases nuevas ya lo traen en sql/inmobiliaria.sql.
-- Las fotos viejas (archivos en backend/cargasImagenes) siguen funcionando: quedan con contenido NULL.

ALTER TABLE `propiedad_foto`
  ADD COLUMN `contenido` mediumblob DEFAULT NULL AFTER `orden`,
  ADD COLUMN `tipo` varchar(50) DEFAULT NULL AFTER `contenido`;

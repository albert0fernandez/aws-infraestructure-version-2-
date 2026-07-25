-- Active: 1737934286469@@127.0.0.1@3306@pdo_solucion
-- Nombre del fichero: pdo.sql
-- Descripción: Script SQL para la creación y carga inicial de la tabla `usuario`.
-- Autor: LMSGI - ASIR1 - IES Alisal
-- Fecha: Marzo 2025


-- Eliminación de la base de datos `pdo_solucion` si existe
DROP DATABASE IF EXISTS `pdo_solucion`;

-- Creación de la base de datos `pdo_solucion`
CREATE DATABASE `pdo_solucion`;
USE `pdo_solucion`;

-- Estructura de la tabla  `t_aula`
CREATE TABLE `t_aula` (
    `aula_id` int NOT NULL AUTO_INCREMENT,
    `aula_nombre` char(4) NOT NULL,
    `aula_ubicacion`char(5) NOT NULL,
    `aula_descripcion` varchar(25) NOT NULL,
    CONSTRAINT `t_aula_pk` PRIMARY KEY (`aula_id`),
    CONSTRAINT `t_aula_nombre_uk` UNIQUE (`aula_nombre`),
    CONSTRAINT `t_aula_nombre_chk` CHECK (regexp_like(`aula_nombre`, _cp850'^A[0-9]{3}$')),
    CONSTRAINT `t_aula_ubicacion_chk` CHECK (regexp_like(`aula_ubicacion`, _cp850'^E[0-9]-P[0-9]$'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Datos de la tabla `t_aula`
INSERT INTO `t_aula` (`aula_nombre`, `aula_ubicacion`, `aula_descripcion`) VALUES
('A000', 'E1-P0', 'Aula multiusos'),
('A001', 'E1-P0',  'Aula TIC'),
('A111', 'E1-P1',  'Laboratorio de física'),
('A113', 'E1-P1',  'Laboratorio de ciencias'),
('A212', 'E1-P2',  'Aula Info'),
('A210', 'E1-P2',  'Aula CFAD2');


-- Estructura de la tabla `t_usuario`
CREATE TABLE `t_usuario` (
    `usuario_id` int NOT NULL AUTO_INCREMENT,
    `usuario_nombre` varchar(30) NOT NULL,
    `usuario_apellido` varchar(30) NOT NULL,
    `usuario_usuario` varchar(20 ) NOT NULL,
    `usuario_telefono` varchar(9) NULL,
    `usuario_email` varchar(30) NOT NULL,
    `usuario_clave` varchar(255) NOT NULL,
    CONSTRAINT `t_usuario_pk` PRIMARY KEY (`usuario_id`),
    CONSTRAINT `t_usuario_email_uk` UNIQUE KEY `usuario_email` (`usuario_email`),
    CONSTRAINT `t_usuario_telefono_chk` CHECK (regexp_like(`usuario_telefono`, '^[0-9]{9}$')),
    CONSTRAINT `t_usuario_email_chk` CHECK (regexp_like(`usuario_email`, _cp850'^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Datos de la tabla `t_usuario`
INSERT INTO `t_usuario` (`usuario_nombre`, `usuario_apellido`, `usuario_usuario`, `usuario_email`, `usuario_clave`) VALUES
('Administrador', 'Principal', 'Administrador', 'administrador@reto.es', '$2y$10$EPY9LSLOFLDDBriuJICmFOqmZdnDXxLJG8YFbog5LcExp77DBQvgC'),
('Adrián', 'López', 'aLopez2', 'alopez2@reto.es', '$2y$10$HqX4egjYYDbiVh.fBN9Ixu68i8tUefUUZjlHqhzgBMAQUaVQD82zu'),
('Alma', 'Pereda', 'aPereda3', 'apereda3@reto.es', '$2y$10$9PbCz1MCEXCpHrM.uRDKCuG8eSpICoor6JrqNbkmm28HykEc.hoHS'),
('Ana', 'Perez', 'aPerez1', 'aperez1@reto.es', '$2y$10$uoqPJ/Ncnp025te2EKgoXueIn95upSABa9i47Hrs2kdRDRsUtaQW6');

-- Estructura de la tabla `t_departamento`
CREATE TABLE `t_departamento` (
    `departamento_id` int NOT NULL AUTO_INCREMENT,
    `departamento_nombre` varchar(30) NOT NULL,
    `departamento_ubicacion` char(14) DEFAULT NULL,
    `responsable_id` int DEFAULT NULL,
    CONSTRAINT `t_departamento_pk` PRIMARY KEY (`departamento_id`),
    CONSTRAINT `t_departamento_responsable_id_uk` UNIQUE (`responsable_id`),
    CONSTRAINT `t_departamento_t_usuario_fk` FOREIGN KEY (`responsable_id`) 
        REFERENCES `t_usuario` (`usuario_id`) ON DELETE SET NULL
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Datos de la tabla `t_departamento`
INSERT INTO `t_departamento` ( `departamento_nombre`, `departamento_ubicacion`, `responsable_id`) 
VALUES ('Informática', 'E1-P2', '1' ),
('Imagen Personal', 'E1-P2', '2'),
('Educación Física', 'E0-P0', '3');

-- Estructura de la tabla `t_proveedor`
CREATE TABLE `t_proveedor` (
    `proveedor_id` int NOT NULL AUTO_INCREMENT,
    `proveedor_nombre` varchar(30) NOT NULL,
    `proveedor_telefono` varchar(9) NULL,
    `proveedor_email` varchar(50) NOT NULL,
    CONSTRAINT `t_proveedor_pk` PRIMARY KEY (`proveedor_id`),
    CONSTRAINT `t_proveedor_email_uk` UNIQUE (`proveedor_email`),
    CONSTRAINT `t_proveedor_telefono_chk` CHECK (regexp_like(`proveedor_telefono`, '^[0-9]{9}$')),
    CONSTRAINT `t_proveedor_email_chk` CHECK (regexp_like(`proveedor_email`, _cp850'^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Datos de la tabla `t_proveedor`
INSERT INTO `t_proveedor` (`proveedor_nombre`, `proveedor_telefono`, `proveedor_email`) VALUES
('Proveedor A', '123456789', 'proveedorA@example.com'),
('Proveedor B', '987654321', 'proveedorB@example.com');

-- Estructura de la tabla `t_estado`
CREATE TABLE `t_estado` (
    `estado_id` int NOT NULL AUTO_INCREMENT,
    `estado_descripcion` ENUM( 'En reserva', 'En reparación', 'En servicio', 'Fuera de servicio') NOT NULL, 
    CONSTRAINT `t_estado_pk` PRIMARY KEY (`estado_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Datos de la tabla `t_estado`
INSERT INTO `t_estado` (`estado_descripcion`) VALUES
('En reserva'),
('En reparación'),
('En servicio'),
('Fuera de servicio');

-- Estructura de la tabla `t_recurso`
CREATE TABLE `t_recurso` (
    `recurso_id` int NOT NULL AUTO_INCREMENT,
    `recurso_codigo` char(8) NOT NULL,
    `recurso_nombre` varchar(50) NOT NULL,
    `recurso_descripcion` text,
    `estado_id` int NOT NULL,  
    `aula_id` int DEFAULT NULL,
    `proveedor_id` int DEFAULT NULL,
    `recurso_precio` decimal(25,2) NOT NULL,
    `recurso_fecha_compra` date NOT NULL,
    `recurso_fin_garantia` date NOT NULL,
    `recurso_foto` varchar(100) NOT NULL,
    CONSTRAINT `t_recurso_pk` PRIMARY KEY (`recurso_id`),
    CONSTRAINT `t_recurso_codigo_uk` UNIQUE (`recurso_codigo`),
    CONSTRAINT `t_recurso_codigo_chk` CHECK (regexp_like(`recurso_codigo`, _cp850'^[A-Z]{3}-[0-9]{4}$')),
    CONSTRAINT `t_recurso_precio_chk` CHECK (`recurso_precio`>=0),
    CONSTRAINT `t_recurso_fecha_compra_chk` CHECK (`recurso_fecha_compra` <= IFNULL(recurso_fin_garantia, '9999-12-31')),
    INDEX `idx_aula` (`aula_id`),
    INDEX `idx_proveedor` (`proveedor_id`),
    CONSTRAINT `t_recurso_t_aula_fk` FOREIGN KEY (`aula_id`) 
        REFERENCES `t_aula` (`aula_id`) ON DELETE RESTRICT,
    CONSTRAINT `t_recurso_t_proveedor_fk` FOREIGN KEY (`proveedor_id`) 
        REFERENCES `t_proveedor` (`proveedor_id`) ON DELETE RESTRICT, 
    CONSTRAINT `t_recurso_t_estado_fk` FOREIGN KEY (`estado_id`) 
        REFERENCES `t_estado` (`estado_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Datos de la tabla `t_recurso`
INSERT INTO `t_recurso` ( `recurso_codigo`, `recurso_nombre`, `recurso_descripcion`, `estado_id`, `aula_id`, `proveedor_id`, `recurso_precio`, `recurso_fecha_compra`, `recurso_fin_garantia`, `recurso_foto`) VALUES
('PRO-0001', 'Proyector', 'Proyector HD', 2, 1, 1, 500.00, '2025-01-01', '2026-01-01', 'Proyector_26.jpg'),
('ORD-0001','Ordenador', 'Ordenador de sobremesa', 1, 2, 2, 1000.00, '2025-02-01', '2027-02-01', 'Ordenador_68.png'),
('POR-0001', 'Portátil', 'Lenovo', 1, 2, 2, 1000.00, '2025-02-01', '2027-02-01', 'Lenovo_17.jpg');

-- Estructura de la tabla `t_historial_uso`
CREATE TABLE `t_historial_uso` (
    `uso_id` int NOT NULL AUTO_INCREMENT,
    `usuario_id` int NOT NULL,
    `recurso_id` int NOT NULL,
    `uso_fecha_inicio` datetime NOT NULL,
    `uso_fecha_fin` datetime DEFAULT NULL,
    CONSTRAINT `t_historial_uso_pk` PRIMARY KEY (`uso_id`),
    INDEX `id_usuario_idx` (`usuario_id`),
    INDEX `recurso_idx` (`recurso_id`),
    CONSTRAINT `t_historial_uso_uk` UNIQUE (`usuario_id`, `recurso_id`, `uso_fecha_inicio`),
    CONSTRAINT `t_historial_uso_fecha_in_chk` CHECK (`uso_fecha_inicio` <= IFNULL(`uso_fecha_fin`, '9999-12-31')),
    CONSTRAINT `t_historial_uso_t_usuarios_fk` FOREIGN KEY (`usuario_id`) 
        REFERENCES `t_usuario` (`usuario_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `t_historial_uso_t_recursos_fk` FOREIGN KEY (`recurso_id`) 
        REFERENCES `t_recurso` (`recurso_id`) ON DELETE RESTRICT ON UPDATE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Datos de la tabla `t_historial_uso`
INSERT INTO `t_historial_uso` (`usuario_id`, `recurso_id`, `uso_fecha_inicio`, `uso_fecha_fin`) VALUES
(1, 1, '2025-02-15 08:00:00', '2025-02-15 10:00:00'),
(2, 2, '2025-02-16 09:00:00', '2025-02-16 11:00:00');

-- Estructura de la tabla `t_mantenimiento`
CREATE TABLE `t_mantenimiento` (
    `mantenimiento_id` int NOT NULL AUTO_INCREMENT,
    `usuario_id` int NOT NULL,
    `recurso_id` int NOT NULL,
    `mantenimiento_fecha` date NOT NULL,
    `mantenimiento_descripcion` text NOT NULL,
    CONSTRAINT `t_mantenimiento_pk` PRIMARY KEY (`mantenimiento_id`),
    CONSTRAINT `t_mantenimiento_uk` UNIQUE (`usuario_id`, `recurso_id`, `mantenimiento_fecha`),
	CONSTRAINT t_mantenimiento_fecha_chk CHECK (`mantenimiento_fecha` <= '9999-12-31'),
    INDEX `id_usuario_idx` (`usuario_id`), 
    INDEX `id_recurso_mantenimiento_fecha_idx` (`recurso_id`, `mantenimiento_fecha`),
    CONSTRAINT `t_mantenimiento_t_usuarios_fk` FOREIGN KEY (`usuario_id`) 
        REFERENCES `t_usuario` (`usuario_id`) ON DELETE CASCADE,
    CONSTRAINT `t_mantenimiento_t_recursos_fk` FOREIGN KEY (`recurso_id`) 
        REFERENCES `t_recurso` (`recurso_id`) ON DELETE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Datos de la tabla `t_mantenimiento`
INSERT INTO `t_mantenimiento` (`usuario_id`, `recurso_id`, `mantenimiento_fecha`, `mantenimiento_descripcion`) VALUES
(1, 1, '2025-02-10', 'Revisión general del proyector'),
(2, 2, '2025-02-11', 'Actualización de software del ordenador');

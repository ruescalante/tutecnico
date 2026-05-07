-- =============================================================
-- MIGRATIONS - Sistema de Servicios Técnicos
-- Ejecutar en orden. Compatible con MySQL 8.0+
-- =============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- -------------------------------------------------------------
-- 1. users
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
    `id`              INT           NOT NULL AUTO_INCREMENT,
    `nombre`          VARCHAR(100)  NOT NULL,
    `correo`          VARCHAR(150)  NOT NULL UNIQUE,
    `contrasena`      VARCHAR(255)  NOT NULL,
    `telefono`        VARCHAR(20)   DEFAULT NULL,
    `rol`             ENUM('cliente','tecnico','admin') NOT NULL DEFAULT 'cliente',
    `activo`          TINYINT(1)   NOT NULL DEFAULT 1,
    `fecha_registro`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_users_correo` (`correo`),
    INDEX `idx_users_rol`    (`rol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 2. categorias
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categorias` (
    `id`          INT          NOT NULL AUTO_INCREMENT,
    `nombre`      VARCHAR(100) NOT NULL,
    `descripcion` TEXT         DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 3. tecnico_perfiles
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tecnico_perfiles` (
    `id`                       INT           NOT NULL AUTO_INCREMENT,
    `user_id`                  INT           NOT NULL,
    `zona_cobertura`           VARCHAR(255)  DEFAULT NULL,
    `descripcion`              TEXT          DEFAULT NULL,
    `disponibilidad`           TINYINT(1)   NOT NULL DEFAULT 1,
    `estado`                   ENUM('pendiente','activo','suspendido') NOT NULL DEFAULT 'pendiente',
    `cancelaciones`            INT           NOT NULL DEFAULT 0,
    `documentos_verificacion`  VARCHAR(500)  DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_tecnico_perfiles_user` (`user_id`),
    CONSTRAINT `fk_tecnico_perfiles_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 4. tecnico_categorias  (tabla pivote)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tecnico_categorias` (
    `user_id`       INT NOT NULL,
    `id_categoria`  INT NOT NULL,
    PRIMARY KEY (`user_id`, `id_categoria`),
    CONSTRAINT `fk_tc_user`
        FOREIGN KEY (`user_id`)      REFERENCES `users`      (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_tc_categoria`
        FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 5. foto_trabajos
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `foto_trabajos` (
    `id`                INT          NOT NULL AUTO_INCREMENT,
    `tecnico_perfil_id` INT          NOT NULL,
    `url`               VARCHAR(500) NOT NULL,
    `descripcion`       VARCHAR(255) DEFAULT NULL,
    `fecha`             DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_foto_trabajos_perfil` (`tecnico_perfil_id`),
    CONSTRAINT `fk_foto_trabajos_perfil`
        FOREIGN KEY (`tecnico_perfil_id`) REFERENCES `tecnico_perfiles` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 6. solicitudes
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `solicitudes` (
    `id`               INT          NOT NULL AUTO_INCREMENT,
    `id_cliente`       INT          NOT NULL,
    `id_tecnico`       INT          DEFAULT NULL,
    `titulo`           VARCHAR(200) NOT NULL,
    `descripcion`      TEXT         NOT NULL,
    `direccion`        VARCHAR(300) NOT NULL,
    `estado`           ENUM('pendiente','aceptada','en_progreso','completada','cancelada') NOT NULL DEFAULT 'pendiente',
    `cancelado_por`    ENUM('cliente','tecnico','admin')  DEFAULT NULL,
    `fecha_creacion`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `fecha_completado` DATETIME     DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_solicitudes_cliente`  (`id_cliente`),
    INDEX `idx_solicitudes_tecnico`  (`id_tecnico`),
    INDEX `idx_solicitudes_estado`   (`estado`),
    CONSTRAINT `fk_solicitudes_cliente`
        FOREIGN KEY (`id_cliente`) REFERENCES `users` (`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_solicitudes_tecnico`
        FOREIGN KEY (`id_tecnico`) REFERENCES `users` (`id`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 7. mensajes
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mensajes` (
    `id`              INT     NOT NULL AUTO_INCREMENT,
    `id_solicitud`    INT     NOT NULL,
    `remitente_tipo`  ENUM('cliente','tecnico','admin') NOT NULL,
    `remitente_id`    INT     NOT NULL,
    `contenido`       TEXT    NOT NULL,
    `leido`           TINYINT(1) NOT NULL DEFAULT 0,
    `fecha`           DATETIME  NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_mensajes_solicitud`  (`id_solicitud`),
    INDEX `idx_mensajes_remitente`  (`remitente_id`),
    CONSTRAINT `fk_mensajes_solicitud`
        FOREIGN KEY (`id_solicitud`) REFERENCES `solicitudes` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 8. cotizaciones
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cotizaciones` (
    `id`              INT            NOT NULL AUTO_INCREMENT,
    `id_solicitud`    INT            NOT NULL,
    `precio_estimado` DECIMAL(10,2)  NOT NULL,
    `descripcion`     TEXT           DEFAULT NULL,
    `estado`          ENUM('pendiente','aceptada','rechazada') NOT NULL DEFAULT 'pendiente',
    `fecha`           DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_cotizaciones_solicitud` (`id_solicitud`),
    CONSTRAINT `fk_cotizaciones_solicitud`
        FOREIGN KEY (`id_solicitud`) REFERENCES `solicitudes` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 9. calificaciones
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `calificaciones` (
    `id`           INT      NOT NULL AUTO_INCREMENT,
    `id_solicitud` INT      NOT NULL UNIQUE,   -- una calificación por solicitud
    `puntuacion`   TINYINT  NOT NULL CHECK (`puntuacion` BETWEEN 1 AND 5),
    `comentario`   TEXT     DEFAULT NULL,
    `fecha`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_calificaciones_solicitud`
        FOREIGN KEY (`id_solicitud`) REFERENCES `solicitudes` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 10. notificaciones
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `notificaciones` (
    `id`            INT      NOT NULL AUTO_INCREMENT,
    `user_id`       INT      NOT NULL,
    `tipo`          ENUM('solicitud_nueva','solicitud_aceptada','solicitud_cancelada',
                         'cotizacion_nueva','cotizacion_aceptada','cotizacion_rechazada',
                         'mensaje_nuevo','calificacion_nueva') NOT NULL,
    `referencia_id` INT      DEFAULT NULL,   -- ID del recurso relacionado
    `leida`         TINYINT(1) NOT NULL DEFAULT 0,
    `fecha`         DATETIME   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_notificaciones_user`  (`user_id`),
    INDEX `idx_notificaciones_leida` (`leida`),
    CONSTRAINT `fk_notificaciones_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
-- Active: 1693133193600@@127.0.0.1@3306@electrocash_dev


-- 01:
DROP TABLE IF EXISTS view_login;
CREATE TABLE view_login(
    `id` INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `file_preview` VARCHAR(255),
    `file_title` VARCHAR(50),
    `file_name` VARCHAR(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS system_config;
CREATE TABLE system_config(
    `id` INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `view_login` INT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO view_login(`file_preview`,`file_title`,`file_name`) VALUES 
('assets/images/config-system/login-v1.webp','Login v1','login_view_1'),
('assets/images/config-system/login-v2.webp','Login v2','login_view_2');



-- 02:
DROP TABLE IF EXISTS preferencias;
CREATE TABLE preferencias(
    `id` INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `usuario_id` INT UNSIGNED NOT NULL,
    `sucursal_init` INT,
    `dashboard_theme` ENUM('LIGHT','DARK','BLUE') DEFAULT 'LIGHT',
    `dashboard_design` ENUM('HORIZONTAL','VERTICAL','CONDENSED') DEFAULT 'HORIZONTAL',
    FOREIGN KEY(usuario_id) REFERENCES usuario(`usu_codigo`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO preferencias(`usuario_id`, `sucursal_init`) VALUES
(6, 4);



-- 03:
ALTER TABLE notas_credito 
ADD `mensaje` VARCHAR(200) NOT NULL,
ADD `row_fixed` TINYINT(1) DEFAULT 0 NOT NULL;

UPDATE notas_credito 
SET row_fixed = 1 
WHERE  ncred_anclado = 'SI';

ALTER TABLE notas_cliente 
ADD `mensaje` VARCHAR(200) NOT NULL,
ADD `row_fixed` TINYINT(1) NOT NULL DEFAULT 0;




-- SUGERENCIA:
-- modificar tipo de dato para la fecha de creacion, 
-- date => datetime
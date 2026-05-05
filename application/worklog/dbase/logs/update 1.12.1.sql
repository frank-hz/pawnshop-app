-- Active: 1726638434579@@127.0.0.1@3306@sistema_dev
-- 
-- [1.12.1] 
-- 


-- START APLICADO --

-- 1.
-- Modificar campo 'cred_update_at' y 'cred_create_at' para habilitar actualizacion automatica 
-- cuando se modifique el registro
ALTER TABLE credito MODIFY cred_update_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

ALTER TABLE credito MODIFY cred_create_at DATETIME DEFAULT CURRENT_TIMESTAMP

-- 2.
-- Crear campo temporal para la fecha de creacion
ALTER TABLE credito ADD cred_tmp DATETIME DEFAULT CURRENT_TIMESTAMP AFTER cred_create_at;


-- 3.
-- Guardar datos de liquidacion en el campo temporal
UPDATE credito 
SET cred_tmp = IF(cred_liquidacion, cred_liquidacion, NULL);

-- 4.
-- Modificar campo cred_liquidacion a cred_liquida_old
ALTER TABLE credito 
RENAME COLUMN cred_liquidacion TO cred_liquida_old;


-- 5.
-- Cambiar posicion liquida_old
ALTER TABLE credito 
MODIFY cred_liquida_old DATE AFTER cred_pago_cuota;


-- 6.
-- Modificar campo cred_liquidacion a cred_liquida_old
ALTER TABLE credito 
RENAME COLUMN cred_tmp TO cred_liquidacion;


-- 7.
-- Cambiar posicion liquidacion nuevo
ALTER TABLE credito 
MODIFY cred_liquidacion DATETIME AFTER cred_creacion;

-- 8.
ALTER TABLE empleado ADD create_at DATETIME DEFAULT CURRENT_TIMESTAMP AFTER emp_creacion;
ALTER TABLE empleado ADD update_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER create_at; 

-- 9. 
-- cambiar fecha de creacion de empleados y usuarios para evitar error constantes
-- actualizar campos 'create_at' y 'update_at' 
UPDATE `empleado` SET `emp_creacion` = '2019-09-04' WHERE `empleado`.`emp_codigo` = 1;
UPDATE `empleado` SET `emp_creacion` = '2019-09-04' WHERE `empleado`.`emp_codigo` = 2;
UPDATE `empleado` SET `emp_creacion` = '2019-09-04' WHERE `empleado`.`emp_codigo` = 3;
-- ALTER TABLE `usuario` CHANGE `usu_creacion` `usu_creacion` DATE NULL;

-- 10. crear campo para fecha de creacion y actualizcion de tabla 'usuario'
ALTER TABLE usuario ADD create_at DATETIME DEFAULT CURRENT_TIMESTAMP AFTER usu_estado;
ALTER TABLE usuario ADD update_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER create_at;

UPDATE `usuario` SET `usu_creacion` = '2019-09-04' WHERE `usuario`.`usu_codigo` = 1;
UPDATE usuario SET 
create_at = usu_creacion,
update_at = usu_creacion

-- 11. aumentar la longitud de almacenamiento del campo 'usu_clave'
ALTER TABLE usuario MODIFY usu_clave VARCHAR(255);


-- 12. 
DROP TABLE IF EXISTS notificacion_log; 
CREATE TABLE notificacion_log (
    `id` INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `credito_id` INT UNSIGNED NOT NULL,
    `empleado_id` INT UNSIGNED NOT NULL,
    `tipo` VARCHAR(50) NOT NULL,
    `asunto` VARCHAR(50) NOT NULL,
    `cuerpo` VARCHAR(500),
    `create_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `update_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY(credito_id) REFERENCES credito(`cred_codigo`) 
    ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY(empleado_id) REFERENCES empleado(`emp_codigo`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS notificacion_template;
CREATE TABLE notificacion_template (
    `id` INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `tipo` VARCHAR(50) DEFAULT 'RECORDATORIO',
    `nombre` VARCHAR(50) NOT NULL,
    `asunto` VARCHAR(50) NOT NULL,
    `cuerpo` VARCHAR(500) NOT NULL,
    `create_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `update_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;




DROP TABLE `clasificacion`; -- eliminar tabla

-- puntuacion - cambio estructura
ALTER TABLE `cliente` CHANGE `cli_puntuacion` `cli_puntuacion` ENUM('A','B','C','-','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '-';

-- puntuacion - actualizacion
update cliente 
set cli_puntuacion = "N" 
where cli_puntuacion = "" OR cli_puntuacion = "-" OR cli_puntuacion = "B";

-- 
-- END APLICADO --




-- . 
-- agregar campo para almacenar el monto original del prestamo
-- agregar campo para almacenar el saldo restante del capital

ALTER TABLE credito ADD monto_inicial DECIMAL(8,2) NOT NULL AFTER cred_monto;
ALTER TABLE credito ADD saldo_restante DECIMAL(8,2) NOT NULL AFTER monto_inicial;


-- . Agregar campo para almacenar metodos de pago
ALTER TABLE pago ADD pag_metodos_pago JSON;

-- . Crear campos para almacenar la fecha de creacion y actualizacion de pagos

ALTER TABLE pago ADD create_at DATETIME DEFAULT CURRENT_TIMESTAMP AFTER pag_comentario;
ALTER TABLE pago ADD update_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER create_at;

-- . copiar los datos del campo 'pag_fecha' al campo 'create_at' y 'update_at'
UPDATE pago 
SET 
create_at = pag_fecha, 
update_at = pag_fecha;
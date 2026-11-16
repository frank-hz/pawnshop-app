
-- PRENDA
-- Eliminar campos no utilizados:
ALTER TABLE `prenda`
  DROP `pren_precio_inicial`,
  DROP `pren_costo_reparacion`,
  DROP `pren_costo_total`,
  DROP `pren_precio_venta`,
  DROP `pren_fecha_liquida`,
  DROP `pren_fecha_venta`;



-- ACTUALIZACION DE COLUMNA 'fecha_creacion'
-- 0. crear respaldo
CREATE TABLE prenda_bk AS SELECT * FROM prenda; 

-- 1. creamos la nueva columna
ALTER TABLE prenda
    ADD COLUMN fecha_creacion DATETIME NULL AFTER pren_estado,
    ADD COLUMN es_fecha_original TINYINT(1) NOT NULL DEFAULT 0 AFTER fecha_creacion;


-- 2. Datos válidos: copia directa
UPDATE prenda
SET fecha_creacion = pren_fecha_registro
WHERE pren_fecha_registro IS NOT NULL
  AND pren_fecha_registro <> 0000-00-00;


-- 3. Datos con 0000-00-00: fecha referencial fija + marca
UPDATE prenda
SET fecha_creacion = '2019-08-21 00:00:00',
    es_fecha_original   = 1
WHERE pren_fecha_registro = 0000-00-00;


-- 4. Verificación
SELECT es_fecha_original, COUNT(*) AS total,
  MIN(fecha_creacion) AS minima,
  MAX(fecha_creacion) AS maxima
FROM prenda
GROUP BY es_fecha_original;

SELECT COUNT(*) AS aun_nulos FROM prenda WHERE fecha_creacion IS NULL;

-- 5. Default para registros nuevos
ALTER TABLE prenda
  MODIFY fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;

-- 6. crear indice
CREATE INDEX idx_prenda_fecha_creacion ON prenda (fecha_creacion);




-- CLIENTE
-- ACTUALIZACION DE COLUMNA 'fecha_creacion'
-- 0. crear respaldo
-- CREATE TABLE cliente_bk AS SELECT * FROM cliente; 

-- 1. creamos la nueva columna
ALTER TABLE cliente
    ADD COLUMN fecha_creacion DATETIME NULL AFTER cli_fecha,
    ADD COLUMN es_fecha_original TINYINT(1) NOT NULL DEFAULT 0 AFTER fecha_creacion;


-- 2. Datos válidos: copia directa
UPDATE cliente
SET fecha_creacion = cli_fecha
WHERE cli_fecha IS NOT NULL
  AND cli_fecha <> 0000-00-00;


-- 3. Datos con 0000-00-00: fecha referencial fija + marca
UPDATE cliente
SET fecha_creacion = '2019-08-21 00:00:00',
  es_fecha_original   = 1
WHERE cli_fecha = 0000-00-00;


-- 4. Verificación
SELECT es_fecha_original, COUNT(*) AS total,
  MIN(fecha_creacion) AS minima,
  MAX(fecha_creacion) AS maxima
FROM cliente
GROUP BY es_fecha_original;

SELECT COUNT(*) AS aun_nulos FROM cliente WHERE fecha_creacion IS NULL;

-- 5. Default para registros nuevos
ALTER TABLE cliente
  MODIFY fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;

-- 6. crear indice
CREATE INDEX idx_cliente_fecha_creacion ON cliente (fecha_creacion);
















-- TRASH
SELECT
  COUNT(*)                         AS total_ceros,
  SUM(c.primera_fecha IS NOT NULL) AS con_credito,
  SUM(c.primera_fecha IS NULL)     AS sin_credito
FROM prenda p
LEFT JOIN (
    SELECT cred_prenda, MIN(cred_create_at) AS primera_fecha
    FROM credito
    WHERE cred_create_at <> 0000-00-00
    GROUP BY cred_prenda
) c ON c.cred_prenda = p.pren_codigo
WHERE p.pren_fecha_registro = 0000-00-00;


-- SELECT * FROM prenda p inner join credito c on c.cred_prenda = p.pren_codigo 
-- where p.pren_fecha_registro = 0000-00-00 and exists(select 1 from credito c );

-- SELECT 
--     p.pren_codigo as prenda_id,
--     p.pren_descripcion as prenda,
--     c.cred_codigo as credito_id,
--     c.cred_serie as credito_serie,
--     c.cred_create_at
-- FROM prenda p
-- where 
-- p.pren_fecha_registro = 0000-00-00 and 
-- exists(select 1 from credito c where c.cred_prenda = p.pren_codigo);



SELECT 
    p.pren_codigo as prenda_id,
    p.pren_descripcion as prenda,
    c.cred_codigo as credito_id,
    c.cred_serie as credito_serie,
    c.cred_create_at
FROM prenda p 
inner join credito c on c.cred_prenda = p.pren_codigo
where 
p.pren_fecha_registro = 0000-00-00 and exists(select 1 from credito c where c.cred_prenda = p.pren_codigo)
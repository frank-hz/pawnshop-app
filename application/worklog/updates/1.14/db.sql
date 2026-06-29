
-- UPDATE 1.14

-- 1. LISTA DE CREDITOS
-- 






-- 2. ASIGNACION MULTIPLE DE SUCURSAL

DROP TABLE IF EXISTS empleado_sucursal;
CREATE TABLE empleado_sucursal (
    id INT PRIMARY KEY AUTO_INCREMENT,
    empleado_id INT UNSIGNED NOT NULL,
    sucursal_id INT UNSIGNED NOT NULL,
    UNIQUE KEY uk_empleado_sucursal (empleado_id, sucursal_id),
    log JSON,
    create_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    FOREIGN KEY (empleado_id) REFERENCES empleado(emp_codigo) ON DELETE CASCADE,
    FOREIGN KEY (sucursal_id) REFERENCES sucursal(suc_codigo) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;




-- 3. VERSIONADO DE PRENDAS

DROP TABLE IF EXISTS credito_prenda;
CREATE TABLE credito_prenda (
    id INT PRIMARY KEY AUTO_INCREMENT,
    empleado_id INT UNSIGNED NOT NULL,
    sucursal_id INT UNSIGNED NOT NULL,
    UNIQUE KEY uk_empleado_sucursal (empleado_id, sucursal_id),
    log JSON,
    create_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    FOREIGN KEY (empleado_id) REFERENCES empleado(emp_codigo) ON DELETE CASCADE,
    FOREIGN KEY (sucursal_id) REFERENCES sucursal(suc_codigo) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- OTROS
-- Crear indice unico de cuota
-- Evita que se registren cuotas de un credito con la misma fecha de vencimiento
ALTER TABLE cuota 
ADD UNIQUE KEY uq_credito_vencimiento (cuo_credito, cuo_vencimiento);



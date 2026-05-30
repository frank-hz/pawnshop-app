-- Creacion de indices para mejora de consultas
CREATE INDEX idx_prenda_cliente ON prenda(pren_cliente);
CREATE INDEX idx_prenda_categoria ON prenda(pren_categoria);
CREATE INDEX idx_categoria_tipo ON categoria(cat_tipo);



-- Modificar posibles estados de un credito
-- enum('E', 'AN', 'R', 'A', 'CC')
ALTER TABLE credito 
MODIFY COLUMN cred_estado ENUM('E', 'AN', 'R', 'A', 'CC', 'HIDE') NOT NULL;
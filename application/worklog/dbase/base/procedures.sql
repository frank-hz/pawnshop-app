-- D E P L O Y

-- Proc. 1
DROP PROCEDURE IF EXISTS proc_usuario_autenticar;
DELIMITER $$
CREATE PROCEDURE proc_usuario_autenticar
(IN _usuario VARCHAR(50), IN _clave VARCHAR(50))
BEGIN
	SELECT 
		e.emp_codigo as 'empleado',
		e.emp_estado as 'empleado_estado',
		concat(e.emp_nombres,' ',e.emp_paterno) as 'nombre_apellido',
		u.usu_codigo as 'id',
		e.emp_imagen as 'imagen',
		r.rol_titulo as 'rol',
		e.emp_sucursal as 'sucursal',
		s.suc_descripcion as 'sucursal_nombre',
		u.usu_estado as 'usuario_estado'
	FROM usuario u
	INNER JOIN roles r ON r.rol_codigo = u.usu_rol
	INNER JOIN empleado e ON e.emp_codigo = u.usu_empleado 
	INNER JOIN sucursal s ON s.suc_codigo = e.emp_sucursal 
	WHERE 
		u.usu_usuario = _usuario AND 
		u.usu_clave = _clave;
END $$
DELIMITER ; 


-- Proc. 2
DROP PROCEDURE IF EXISTS proc_menu_usuario;
DELIMITER $$
CREATE PROCEDURE proc_menu_usuario
(IN _usuario INT(10))
BEGIN
	DECLARE _rol VARCHAR(30);
	SET _rol = (SELECT r.rol_titulo FROM roles r 
	INNER JOIN usuario u ON u.usu_rol = r.rol_codigo WHERE u.usu_codigo = _usuario);
	
	IF _rol = 'Administrador' THEN 
		SELECT 
			mo.mod_titulo,
			mo.mod_descripcion,
			mo.mod_ruta,
			mo.mod_icono,
			'admin' AS 'mod_rol'
		FROM modulo mo
 		WHERE mo.mod_estado = 'A' 
 		ORDER BY mo.mod_codigo ASC;
	ELSE
		SELECT 
			me.men_codigo,
			mo.mod_titulo,
			mo.mod_descripcion,
			mo.mod_ruta,
			mo.mod_icono,
			'empleado' AS 'mod_rol'
		FROM menu me  
		INNER JOIN modulo mo ON mo.mod_codigo = me.men_modulo  
		WHERE me.men_usuario = _usuario AND mo.mod_estado = 'A' 
		ORDER BY mo.mod_codigo ASC;
	END IF;

END $$
DELIMITER ; 


-- -- Proc. 3
DROP PROCEDURE IF EXISTS proc_tarifa_lista;
DELIMITER $$
CREATE PROCEDURE proc_tarifa_lista()
BEGIN
	SELECT 
		t.tar_codigo as 'id',
		t.tar_minimo as 'minimo',
		t.tar_maximo as 'maximo',
		t.tar_interes as 'interes',
		t.tar_mora as 'mora',
		t.tar_tipo as 'tipo'
	FROM tarifa t 
	ORDER BY t.tar_codigo ASC;
END $$
DELIMITER ; 


-- Proc 4
DROP PROCEDURE IF EXISTS proc_cliente_lista;
DELIMITER $$
CREATE PROCEDURE proc_cliente_lista
(IN _tipo CHAR(30), IN _buscar CHAR(50), IN _limite INT)
BEGIN 
	IF _tipo = 'nombre' THEN
		SELECT
			c.cli_codigo as 'id', 
			c.cli_documento as 'documento',
			c.cli_tipo_documento as 'tipo_documento', 
			c.cli_nombres as 'nombres',
			c.cli_paterno as 'paterno',
			c.cli_materno as 'materno',
			u.ubi_distrito as 'ciudad',
			c.cli_direccion as 'direccion',
			c.cli_telefono as 'telefono',
			c.cli_imagen as 'imagen'
		FROM cliente c
		INNER JOIN ubigeo u ON u.ubi_codigo = c.cli_ubigeo 
		WHERE 
			c.cli_nombres LIKE CONCAT(_buscar, '%') OR 
			c.cli_paterno LIKE CONCAT(_buscar, '%') OR 
			c.cli_materno LIKE CONCAT(_buscar, '%')
		ORDER BY cli_nombres ASC LIMIT _limite;
	ELSEIF _tipo = 'documento' THEN
		SELECT
			c.cli_codigo as 'id', 
			c.cli_documento as 'documento',
			c.cli_tipo_documento as 'tipo_documento', 
			c.cli_nombres as 'nombres',
			c.cli_paterno as 'paterno',
			c.cli_materno as 'materno',
			u.ubi_distrito as 'ciudad',
			c.cli_direccion as 'direccion',
			c.cli_telefono as 'telefono',
			c.cli_imagen as 'imagen'
		FROM cliente c
		INNER JOIN ubigeo u ON u.ubi_codigo = c.cli_ubigeo 
		WHERE c.cli_documento LIKE CONCAT(_buscar, '%')
		ORDER BY cli_nombres ASC LIMIT _limite;
	ELSE 
		SELECT
			c.cli_codigo as 'id', 
			c.cli_documento as 'documento',
			c.cli_tipo_documento as 'tipo_documento', 
			c.cli_nombres as 'nombres',
			c.cli_paterno as 'paterno',
			c.cli_materno as 'materno',
			u.ubi_distrito as 'ciudad',
			c.cli_direccion as 'direccion',
			c.cli_telefono as 'telefono',
			c.cli_imagen as 'imagen'
		FROM cliente c
		INNER JOIN ubigeo u ON u.ubi_codigo = c.cli_ubigeo 
		ORDER BY cli_nombres ASC LIMIT _limite;
	END IF;
END $$
DELIMITER ;


-- Proc 5
DROP PROCEDURE IF EXISTS proc_tarifa_credito_relacion;
DELIMITER $$
CREATE PROCEDURE proc_tarifa_credito_relacion
(IN _tarifa INT)
BEGIN
	SELECT
		c.cred_codigo as 'id',
		c.cred_monto as 'monto',
		c.cred_tasa as 'tasa',
		c.cred_cargo as 'cargo',
		t.tar_interes as 'interes',
		t.tar_mora as 'mora',
		t.tar_tipo as 'tipo'
	FROM credito c  
	INNER JOIN tarifa t ON t.tar_codigo = c.cred_tarifa
	WHERE 
		(c.cred_estado = 'E' OR c.cred_estado = 'CC') AND 
		c.cred_tarifa = _tarifa 
	ORDER BY c.cred_codigo ASC;

END $$
DELIMITER ;  


-- Proc 6
DROP PROCEDURE IF EXISTS proc_cliente_detalle;
DELIMITER $$
CREATE PROCEDURE proc_cliente_detalle
(IN _codigo INT)
BEGIN 
	SELECT 
		c.cli_codigo as 'id',
		c.cli_documento as 'documento',
		c.cli_tipo_documento as 'tipo_documento',
		c.cli_nombres as 'nombres',
		c.cli_paterno as 'paterno',
		c.cli_materno as 'materno',
		c.cli_ubigeo as 'ubigeo',
		u.ubi_distrito as 'ciudad',
		u.ubi_departamento as 'departamento',
		c.cli_direccion as 'direccion',
		c.cli_telefono as 'telefono',
		c.cli_imagen as 'imagen',
		c.cli_email as 'email',
		c.cli_fecha	as 'fecha_registro'
	FROM cliente c
	INNER JOIN ubigeo u ON u.ubi_codigo = c.cli_ubigeo
	WHERE c.cli_codigo = _codigo;
END $$
DELIMITER ;

-- Proc 7
DROP PROCEDURE IF EXISTS proc_cliente_registro;
DELIMITER $$
CREATE PROCEDURE proc_cliente_registro
(IN _documento CHAR(20), IN _tipo_doc CHAR(50), IN _nombres VARCHAR(50), IN _paterno VARCHAR(50), IN _materno VARCHAR(50),
IN _ubigeo INT, _direccion VARCHAR(100), IN _telefono CHAR(50), IN _fecha DATE)
BEGIN 
	DECLARE __id INT;
	DECLARE `_rollback` BOOL DEFAULT 0;
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET `_rollback` = 1;
    START TRANSACTION;
		INSERT INTO cliente (
			`cli_documento`, `cli_tipo_documento`, `cli_nombres`, `cli_paterno`, 
			`cli_materno`, `cli_ubigeo`, `cli_direccion`, `cli_telefono`, `cli_clasificacion`, `cli_fecha`) 
		VALUES (
			_documento,
			_tipo_doc,
			UPPER(_nombres),
			UPPER(_paterno),
			UPPER(_materno),
			_ubigeo,
			UPPER(_direccion),
			_telefono,
			'NORMAL',
			_fecha);

	IF `_rollback` THEN
        ROLLBACK;
        SELECT "Error" as "codigo","Proceso incompleto." AS "mensaje";
    ELSE
        COMMIT;
        SET __id = (SELECT LAST_INSERT_ID());
        SELECT 'Ok' as 'codigo','Registro completo.' AS 'mensaje', __id as 'id';
    END IF;
END $$
DELIMITER ;


-- Proc 8
DROP PROCEDURE IF EXISTS proc_cliente_actualizar;
DELIMITER $$
CREATE PROCEDURE proc_cliente_actualizar
(IN _codigo INT, IN _documento CHAR(20), IN _tipo_doc CHAR(50), IN _nombres VARCHAR(50), 
IN _paterno VARCHAR(50), IN _materno VARCHAR(50),IN _ubigeo INT, _direccion VARCHAR(100), 
IN _telefono CHAR(50))
BEGIN 
	DECLARE __affected INT;
	DECLARE `_rollback` BOOL DEFAULT 0;
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET `_rollback` = 1;

    START TRANSACTION;
		UPDATE cliente 
			SET 
				cli_documento = _documento,
				cli_tipo_documento = _tipo_doc,
				cli_nombres = UPPER(_nombres),
				cli_paterno = UPPER(_paterno),
				cli_materno = UPPER(_materno),
				cli_ubigeo  = _ubigeo,
				cli_direccion = UPPER(_direccion),
				cli_telefono  = _telefono
		WHERE cli_codigo = _codigo;
		SET __affected = (SELECT ROW_COUNT());
	IF `_rollback` THEN
        ROLLBACK;
        SELECT "Error" as "codigo","Proceso incompleto." AS "mensaje";
    ELSE
        COMMIT;
        
        SELECT 'Ok' as 'codigo', 'cambios aplicados.' AS 'mensaje';
    END IF;

END $$
DELIMITER ;


-- Procedimiento 9
DROP PROCEDURE IF EXISTS proc_tipo_documento_lista;
DELIMITER $$
CREATE PROCEDURE proc_tipo_documento_lista()
BEGIN 
	SELECT td.tdoc_codigo as 'id',
	td.tdoc_nombre as 'nombre',
	td.tdoc_descripcion as 'descripcion',
	td.tdoc_max_caracteres as 'car_minimo',
	td.tdoc_max_caracteres as 'car_maximo',
	td.tdoc_estado as 'estado'
	FROM tipo_documento td;
END $$
DELIMITER ;


-- Procedimiento 10
DROP PROCEDURE IF EXISTS proc_permiso_usuario_verificar;
DELIMITER $$
CREATE PROCEDURE proc_permiso_usuario_verificar
(IN _usuario INT, IN _permiso CHAR(50))
BEGIN 
	DECLARE _verRol INT;
	DECLARE _verPermiso INT;

	SET _verRol = (SELECT COUNT(*) 
		FROM usuario u
		INNER JOIN roles rl ON rl.rol_codigo = u.usu_rol 
		WHERE 
			u.usu_codigo = _usuario AND 
			rl.rol_titulo = 'Administrador'
	);
	IF _verRol = 0 THEN 
		SET _verPermiso = (SELECT COUNT(*) FROM usuario_permiso up
			WHERE 
				up.up_usuario = _usuario AND 
				up.up_permiso = _permiso AND
				up.up_estado = 'A'
		);
		IF _verPermiso > 0 THEN 
			SELECT 'P' AS 'codigo', 'Permiso correcto' as 'mensaje'; 
		ELSE 
			SELECT 'D' AS 'codigo', 'Permisos insuficientes' as 'mensaje'; 
		END IF;
	ELSE 
		SELECT 'P' AS 'codigo', 'Permiso correcto' as 'mensaje'; 
	END IF;
END $$
DELIMITER ;


-- Procedimiento 11
DROP PROCEDURE IF EXISTS proc_ubigeo_muestra;
DELIMITER $$
CREATE PROCEDURE proc_ubigeo_muestra()
BEGIN 
	SELECT 
		u.ubi_codigo as 'id', 
		u.ubi_departamento as 'departamento', 
		u.ubi_provincia as 'provincia', 
		u.ubi_distrito as 'distrito',
		u.ubi_preferencia as 'preferencia'
	FROM ubigeo u  
		ORDER BY ubi_preferencia ASC, ubi_distrito ASC LIMIT 8;
END $$
DELIMITER ;


-- Procedimiento 12
DROP PROCEDURE IF EXISTS proc_ubigeo_buscar;
DELIMITER $$
CREATE PROCEDURE proc_ubigeo_buscar
( IN _buscar VARCHAR(50) )
BEGIN 
	SELECT 
		u.ubi_codigo as 'id', 
		u.ubi_departamento as 'departamento', 
		u.ubi_provincia as 'provincia', 
		u.ubi_distrito as 'distrito',
		u.ubi_preferencia as 'preferencia'
	FROM ubigeo u  
		WHERE 
			u.ubi_distrito LIKE CONCAT(_buscar, '%')
		ORDER BY ubi_preferencia ASC, ubi_distrito ASC LIMIT 8;
END $$
DELIMITER ;


DROP PROCEDURE IF EXISTS proc_ubigeo_lista;
DELIMITER $$
CREATE PROCEDURE proc_ubigeo_lista(IN _tipo CHAR(20), IN _buscar CHAR(20), IN _limite INT)
BEGIN 
	IF _tipo = 'BUSCAR' THEN
		SELECT 
			u.ubi_codigo as 'id', 
			u.ubi_departamento as 'departamento', 
			u.ubi_provincia as 'provincia', 
			u.ubi_distrito as 'distrito',
			u.ubi_preferencia as 'preferencia'
		FROM ubigeo u  
		WHERE u.ubi_distrito LIKE CONCAT(_buscar,'%')
			ORDER BY u.ubi_distrito ASC LIMIT _limite;
	ELSE 
		SELECT 
			u.ubi_codigo as 'id', 
			u.ubi_departamento as 'departamento', 
			u.ubi_provincia as 'provincia', 
			u.ubi_distrito as 'distrito',
			u.ubi_preferencia as 'preferencia'
		FROM ubigeo u  
			ORDER BY ubi_distrito ASC LIMIT _limite;
	END IF;
END $$
DELIMITER ;
-- Procedimiento 13
-- DROP PROCEDURE IF EXISTS proc_cliente_informacion;
-- DELIMITER $$
-- CREATE PROCEDURE proc_cliente_informacion
-- ()
-- BEGIN 
-- 

-- END $$
-- DELIMITER ;


-- Procedimiento 14
DROP PROCEDURE IF EXISTS proc_cliente_creditos;
DELIMITER $$
CREATE PROCEDURE proc_cliente_creditos
(IN _cliente INT)
BEGIN 

	SELECT 
		c.cred_codigo as 'id',
		c.cred_serie as 'codigo',
		c.cred_creacion as 'creacion',
		c.cred_monto as 'monto',
		c.cred_estado as 'estado',
		(SELECT COUNT(*) FROM cuota WHERE cuo_credito = c.cred_codigo) as 'cred_cuotas'
	FROM credito c WHERE cred_cliente = _cliente
	ORDER BY c.cred_creacion DESC, c.cred_serie DESC;
END $$
DELIMITER ;


-- Procedimiento 15
DROP PROCEDURE IF EXISTS proc_prenda_lista;
DELIMITER $$
CREATE PROCEDURE proc_prenda_lista
()
BEGIN 
	SELECT 
		p.pren_codigo as 'id',
		p.pren_id_almacen as 'almacen_id',
		CONCAT(cl.cli_nombres,' ',cl.cli_paterno,' ',cl.cli_materno) AS 'cliente',
		p.pren_descripcion as 'descripcion',
		p.pren_serial as 'serial',
		p.pren_valoracion as 'valoracion',
		p.pren_categoria as 'categoria_id',
		c.cat_nombre as 'categoria',
		tp.tpren_nombre as 'tipo',
		p.pren_monto_previo as 'monto_previo',
		p.pren_reutilizacion as 'reutilizacion',
		p.pren_estado as 'estado'
	FROM prenda p
	INNER JOIN cliente cl ON cl.cli_codigo = p.pren_cliente 
	INNER JOIN categoria c ON c.cat_codigo = p.pren_categoria
	INNER JOIN tipo_prenda tp ON tp.tpren_codigo = c.cat_tipo
	ORDER BY pren_cliente ASC LIMIT 10;
END $$
DELIMITER ;



-- Procedimiento 16
DROP PROCEDURE IF EXISTS proc_categoria_lista;
DELIMITER $$
CREATE PROCEDURE proc_categoria_lista
(IN _tipo CHAR(30), IN _buscar CHAR(50))
BEGIN 
	IF _tipo = 'buscar' THEN 
		SELECT 
			c.cat_codigo as 'id',
			c.cat_tipo as 'tipo_id',
			tp.tpren_nombre as 'tipo',
			c.cat_nombre as 'nombre',
			c.cat_estado as 'estado'
		FROM categoria c 
		INNER JOIN tipo_prenda tp ON tp.tpren_codigo = c.cat_tipo
		WHERE c.cat_nombre LIKE CONCAT('%', _buscar)
		ORDER BY c.cat_nombre ASC;
	ELSE 
		SELECT 
			c.cat_codigo as 'id',
			c.cat_tipo as 'tipo_id',
			tp.tpren_nombre as 'tipo',
			c.cat_nombre as 'nombre',
			c.cat_estado as 'estado'
		FROM categoria c 
		INNER JOIN tipo_prenda tp ON tp.tpren_codigo = c.cat_tipo
		ORDER BY c.cat_nombre ASC;
	END IF;
END $$
DELIMITER ;


-- Procedimiento 17
DROP PROCEDURE IF EXISTS proc_categoria_registro;
DELIMITER $$
CREATE PROCEDURE proc_categoria_registro
(IN _tipo CHAR(50), IN _nombre VARCHAR(50))
BEGIN 
	DECLARE __affected INT;
	DECLARE __reps INT;
	DECLARE `_rollback` BOOL DEFAULT 0;
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET `_rollback` = 1;

    SET __reps = (SELECT COUNT(*) FROM categoria WHERE cat_tipo = _tipo AND cat_nombre = UPPER(_nombre));
    IF __reps > 0 THEN 
    	SELECT 
        	"Error" as "codigo",
        	CONCAT('La categoria ', UPPER(_nombre), ' ya existe en el sistema.') AS "mensaje",
        	'' as 'id';
    ELSE 
	    START TRANSACTION;


			INSERT INTO categoria (`cat_tipo`,`cat_nombre`,`cat_estado`)
			VALUES(UPPER(_tipo), UPPER(_nombre), 'A');

		IF `_rollback` THEN
	        ROLLBACK;
	        SELECT 
	        	"Error" as "codigo",
	        	"Proceso incompleto." AS "mensaje",
	        	'' as 'id';
	    ELSE
	        COMMIT;
	        SELECT 
	        	'Ok' as 'codigo', 
	        	'Categoria agregada.' AS 'mensaje', 
	        	(SELECT LAST_INSERT_ID()) as 'id' ;
	    END IF;
    END IF;
END $$
DELIMITER ;


-- -- Procedimiento 18
-- DROP PROCEDURE IF EXISTS proc_categoria_informacion;
-- DELIMITER $$
-- CREATE PROCEDURE proc_categoria_informacion
-- ()
-- BEGIN 
-- 	DECLARE __total INT;
-- 	DECLARE __electro INT;
-- 	DECLARE __joya INT;

-- 	SET __total = (SELECT COUNT(*) FROM categoria);
-- 	SET __electro = (SELECT COUNT(*) FROM categoria c 
-- 		INNER JOIN tipo_prenda t ON t.tpren_codigo = c.cat_tipo 
-- 		WHERE t.tpren_nombre = 'ELECTRO' OR t.tpren_nombre = 'Electro');
-- 	SET __joya = (SELECT COUNT(*) FROM categoria c 
-- 		INNER JOIN tipo_prenda t ON t.tpren_codigo = c.cat_tipo 
-- 		WHERE t.tpren_nombre = 'JOYA' OR t.tpren_nombre = 'Joya');

-- 	SELECT 
-- 		__total as 'total',
-- 		__electro as 'total_electro', 
-- 		__joya as 'total_joya';

-- END $$
-- DELIMITER ;


-- Procedimiento 19
DROP PROCEDURE IF EXISTS proc_categoria_actualizar;
DELIMITER $$
CREATE PROCEDURE proc_categoria_actualizar
(IN _codigo INT, IN _nombre VARCHAR(50), IN _tipo CHAR(50))
BEGIN 
	DECLARE __affected INT;
	DECLARE `_rollback` BOOL DEFAULT 0;
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET `_rollback` = 1;

    START TRANSACTION;
		UPDATE categoria 
		SET 
			cat_nombre = UPPER(_nombre),
			cat_tipo = _tipo 
		WHERE cat_codigo = _codigo;
		SET __affected = (SELECT ROW_COUNT());

	IF `_rollback` THEN
        ROLLBACK;
        SELECT 
        	"Error" as "codigo",
        	"Actualizacion incompleta." AS "mensaje";
    ELSE
        COMMIT;
        SELECT 
        	'Ok' as 'codigo', 
        	CONCAT(__affected,' cambios aplicados.') AS 'mensaje';
    END IF;

END $$
DELIMITER ;


-- Procedimiento 20
DROP PROCEDURE IF EXISTS proc_categoria_eliminar;
DELIMITER $$
CREATE PROCEDURE proc_categoria_eliminar
(IN _codigo INT)
BEGIN 
	DECLARE __affected INT;
	DECLARE _verifica INT;

	SET _verifica = (SELECT COUNT(*) FROM prenda WHERE pren_categoria = _codigo);
	IF _verifica = 0 THEN
		DELETE FROM categoria WHERE cat_codigo = _codigo;
		SELECT 
    		"Ok" AS "codigo",
    		"Categoria eliminada." AS "mensaje";
	ELSE 
		SELECT 
    		"Error" AS "codigo",
    		"No se puede eliminar esta categoria." AS "mensaje";
	END IF;
END $$
DELIMITER ;


-- -- Procedimiento 21
-- DROP PROCEDURE IF EXISTS proc_prenda_informacion;
-- DELIMITER $$
-- CREATE PROCEDURE proc_prenda_informacion
-- ()
-- BEGIN 
-- 	DECLARE __total INT;
-- 	DECLARE __ocupado INT;
-- 	DECLARE __recuperado INT;

-- 	SET __total = (SELECT COUNT(*) FROM prenda);

-- 	SET __ocupado = (SELECT COUNT(*) FROM prenda p 
-- 		WHERE p.pren_estado = 'O');

-- 	SET __recuperado = (SELECT COUNT(*) FROM prenda p 
-- 		WHERE p.pren_estado = 'R');

-- 	SELECT 
-- 		__total as 'total',
-- 		__ocupado as 'total_ocupado', 
-- 		__recuperado as 'total_recuperado';

-- END $$
-- DELIMITER ;


-- -- Procedimiento 22
-- DROP PROCEDURE IF EXISTS proc_categoria_buscar_nombre;
-- DELIMITER $$
-- CREATE PROCEDURE proc_categoria_buscar_nombre
-- (IN _tipo VARCHAR(50))
-- BEGIN 
-- 	SELECT 
-- 		c.cat_codigo,
-- 		c.cat_tipo,
-- 		tp.tpren_nombre as 'cat_tipo_nombre',
-- 		c.cat_nombre,
-- 		c.cat_estado
-- 	FROM categoria c 
-- 	INNER JOIN tipo_prenda tp ON c.cat_tipo = tp.tpren_codigo
-- 	WHERE tp.tpren_nombre = _tipo
-- 	ORDER BY c.cat_nombre ASC;
-- END $$
-- DELIMITER ;



-- Procedimiento 23
DROP PROCEDURE IF EXISTS proc_prenda_registro;
DELIMITER $$
CREATE PROCEDURE proc_prenda_registro
(
	IN _cliente INT, IN _descripcion VARCHAR(50),
	IN _categoria INT, IN _serial CHAR(50), IN _marca CHAR(50),
	IN _modelo CHAR(50), IN _quilate CHAR(20), IN _peso CHAR(10),
	IN _valora CHAR(5), IN _observacion VARCHAR(50), IN _comentario VARCHAR(50),
	IN _empleado INT, IN _fecha DATE
)
BEGIN 
	DECLARE __id INT;
	DECLARE _id_almacen CHAR(15);
	DECLARE `_rollback` BOOL DEFAULT 0;
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET `_rollback` = 1;

    START TRANSACTION;

		INSERT INTO prenda(`pren_cliente`,`pren_descripcion`,`pren_categoria`,`pren_serial`,
			`pren_marca`,`pren_modelo`,`pren_quilate`,`pren_peso`,`pren_valoracion`,`pren_comentario`,
			`pren_observacion`, `pren_fecha_registro`)
		VALUES(
			_cliente, UPPER(_descripcion),
			_categoria, _serial, UPPER(_marca),
			UPPER(_modelo), UPPER(_quilate), UPPER(_peso), 
			_valora, UPPER(_observacion), UPPER(_comentario), _fecha
		);

		SET __id = (SELECT LAST_INSERT_ID());
		SET _id_almacen = CONCAT('PREN',LPAD(__id, 5, '0'));
		UPDATE prenda 
			SET 
				pren_id_almacen = _id_almacen
		WHERE pren_codigo = __id;

		INSERT INTO propiedad(`prop_empleado`,`prop_cliente`,`prop_prenda`,`prop_inicio`,`prop_motivo`,`prop_estado`)
			VALUES(_empleado, _cliente, __id, _fecha, 'Registro en el sistema.', 'A');

	IF `_rollback` THEN
        ROLLBACK;
        SELECT 
        	"Error" as "codigo",
        	'' as 'id',
        	"Registro incompleto." AS "mensaje";
    ELSE
        COMMIT;
        
        SELECT 
        	'Ok' as 'codigo', 
        	__id as 'id',
        	'Prenda registrada!'AS 'mensaje';
    END IF;

END $$
DELIMITER ;


-- Proc 24
DROP PROCEDURE IF EXISTS proc_prenda_detalle;
DELIMITER $$
CREATE PROCEDURE proc_prenda_detalle
(IN _codigo INT)
BEGIN 
	SELECT 
		p.pren_codigo as 'id',
		p.pren_descripcion as 'descripcion',
		p.pren_id_almacen as 'almacen_id',
		p.pren_cliente as 'cliente_id',
		CONCAT(c.cli_nombres,' ', c.cli_paterno, ' ',c.cli_materno) as 'cliente',
		ct.cat_nombre as 'categoria',
		tp.tpren_nombre as 'tipo',
		p.pren_categoria as 'categoria_id',
		p.pren_serial as 'serial',
		p.pren_marca as 'marca',
		p.pren_modelo as 'modelo',
		p.pren_quilate as 'quilate',
		p.pren_peso as 'peso',
		p.pren_observacion as 'observacion',
		p.pren_comentario as 'comentario',
		p.pren_valoracion as 'valoracion',
		p.pren_reutilizacion as 'reutilizacion',
		p.pren_fecha_registro as 'fecha_registro',
		p.pren_estado as 'estado'	
	FROM prenda p
	INNER JOIN cliente c ON c.cli_codigo = p.pren_cliente
	INNER JOIN categoria ct ON ct.cat_codigo = p.pren_categoria
	INNER JOIN tipo_prenda tp ON tp.tpren_codigo = ct.cat_tipo
	WHERE p.pren_codigo = _codigo;
END $$
DELIMITER ;


-- Procedimiento 25
DROP PROCEDURE IF EXISTS proc_categoria_lista_tipo;
DELIMITER $$
CREATE PROCEDURE proc_categoria_lista_tipo
(IN _tipo CHAR(50))
BEGIN 
	SELECT 
		c.cat_codigo as 'id',
		tp.tpren_nombre as 'tipo',
		c.cat_nombre as 'nombre'
	FROM categoria c 
	INNER JOIN tipo_prenda tp ON tp.tpren_codigo = c.cat_tipo
	WHERE c.cat_tipo = _tipo
	ORDER BY c.cat_nombre ASC;
END $$
DELIMITER ;


-- Procedimiento 26
DROP PROCEDURE IF EXISTS proc_prenda_actualizar;
DELIMITER $$
CREATE PROCEDURE proc_prenda_actualizar
(
	IN _codigo INT, IN _descripcion VARCHAR(50),
	IN _categoria INT, IN _serial CHAR(50), IN _marca CHAR(50),
	IN _modelo CHAR(50), IN _quilate CHAR(20), IN _peso CHAR(10),
	IN _valora CHAR(5), IN _observacion VARCHAR(50), IN _comentario VARCHAR(50) 
)
BEGIN 
	DECLARE __affected INT;
	DECLARE `_rollback` BOOL DEFAULT 0;
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET `_rollback` = 1;

    START TRANSACTION;

		UPDATE prenda
			SET 
				pren_descripcion = UPPER(_descripcion),
				pren_categoria = _categoria,
				pren_serial = _serial,
				pren_marca = UPPER(_marca),
				pren_modelo = UPPER(_modelo),
				pren_quilate = _quilate,
				pren_peso = _peso,
				pren_valoracion = _valora,
				pren_observacion = UPPER(_observacion), 
				pren_comentario = UPPER(_comentario)
		WHERE
			pren_codigo = _codigo;
		SET __affected = (SELECT ROW_COUNT());

	IF `_rollback` THEN
        ROLLBACK;
        SELECT 
        	"Error" as "codigo",
        	"Actualizacion incompleta." AS "mensaje";
    ELSE
        COMMIT;
        
        SELECT 
        	'Ok' as 'codigo', 
        	IF(__affected > 0, 'Cambios Aplicados', 'Sin cambios') AS 'mensaje';
    END IF;

END $$
DELIMITER ;


-- Procedimiento 27
DROP PROCEDURE IF EXISTS proc_prenda_eliminar;
DELIMITER $$
CREATE PROCEDURE proc_prenda_eliminar
( IN _id INT )
BEGIN 
	DECLARE _verifica INT;
    SET _verifica = (SELECT COUNT(*) FROM credito WHERE cred_prenda = _id);

    IF _verifica = 0 THEN 
		DELETE FROM prenda WHERE pren_codigo = _id;
		SELECT 
        	"Ok" as "codigo",
        	"Prenda eliminada." AS "mensaje";
    ELSE 
    	SELECT 
        	"Error" as "codigo",
        	"La prenda tiene creditos asociados." AS "mensaje";
    END IF;

END $$
DELIMITER ;


-- Procedimiento 28
DROP PROCEDURE IF EXISTS proc_cliente_eliminar;
DELIMITER $$
CREATE PROCEDURE proc_cliente_eliminar
( IN _codigo INT )
BEGIN 
	DECLARE __affected INT;
	DECLARE __verifica INT;
    SET __verifica = (SELECT COUNT(*) FROM prenda WHERE pren_cliente = _codigo);

    IF __verifica > 0 THEN 
    	SELECT 
        	"Error" as "codigo",
        	"No se puede eliminar el cliente por que tiene prendas asociadas." AS "mensaje";
    ELSE 
		DELETE FROM cliente WHERE cli_codigo = _codigo;
		SET __affected = (SELECT ROW_COUNT());
		IF __affected > 0 THEN 
			SELECT 
	        	"Ok" as "codigo",
	        	"Cliente eliminado." AS "mensaje";
		ELSE
			SELECT 
	        	"Error" as "codigo",
	        	"El proceso de eliminacion no se completo." AS "mensaje";
		END IF;
    END IF;
END $$
DELIMITER ;



-- Procedimiento 28
DROP PROCEDURE IF EXISTS proc_tarifa_actualizar;
DELIMITER $$
CREATE PROCEDURE proc_tarifa_actualizar
( 
	IN _codigo INT, IN _tipo CHAR(10), IN _interes DECIMAL(8,2), 
	IN _mora DECIMAL(8,2)
)
BEGIN 
	
	DECLARE __affected INT;
	DECLARE `_rollback` BOOL DEFAULT 0;
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET `_rollback` = 1;

    START TRANSACTION;

		UPDATE tarifa
			SET 
				tar_tipo = _tipo,
				tar_interes = _interes,
				tar_mora = _mora
		WHERE
			tar_codigo = _codigo;
		SET __affected = (SELECT ROW_COUNT());

	IF `_rollback` THEN
        ROLLBACK;
        SELECT 
        	"Error" as "codigo",
        	"Actualizacion incompleta." AS "mensaje";
    ELSE
        COMMIT;
        
        SELECT 
        	'Ok' as 'codigo', 
        	IF(__affected > 0, 'Cambios Aplicados', 'Sin cambios') AS 'mensaje';
    END IF;
	
END $$
DELIMITER ;


-- Procedimiento 29
-- DROP PROCEDURE IF EXISTS proc_credito_registro;
-- DELIMITER $$
-- CREATE PROCEDURE proc_credito_registro
-- ( 
-- 	IN _empleado INT, IN _cliente INT, IN _prenda INT,
-- 	IN _monto DECIMAL(8,2), IN _prorroga INT, IN _tarifa INT, 
-- 	IN _cargo DECIMAL(8,2), IN _tasa CHAR(20), IN _pago DECIMAL(8,2), 
-- 	IN _mora DECIMAL(8,2), IN _serie CHAR(15), IN _sucursal INT,
-- 	IN _fecha DATE 
-- )
-- BEGIN 
	
-- 	DECLARE __affected INT;
-- 	DECLARE __actualEvaluar INT;
-- 	DECLARE __penalidadEvaluar INT;
-- 	DECLARE __prorrogaEvaluar INT;

-- 	DECLARE __vencimientoFinal DATE;
-- 	DECLARE __penalidadFinal DATE;
-- 	DECLARE __creditoID INT;

-- 	DECLARE `_rollback` BOOL DEFAULT 0;
--     DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET `_rollback` = 1;

--     START TRANSACTION;

-- 		INSERT INTO credito
-- 			(
-- 				`cred_serie`,`cred_creacion`,`cred_empleado`,`cred_cliente`,`cred_prenda`,
-- 				`cred_monto`,`cred_prorroga`, `cred_tarifa`,`cred_cargo`,`cred_tasa`,
-- 				`cred_pago_semanal`,`cred_penalidad`,`cred_sucursal`,`cred_estado`
-- 			)
-- 			VALUES
-- 			(
-- 				UPPER(_serie), _fecha, _empleado, _cliente, _prenda,
-- 				_monto, _prorroga, _tarifa, _cargo, _tasa,
-- 				_pago, _mora, _sucursal, 'E'
-- 			);

-- 		SET __creditoID = (SELECT LAST_INSERT_ID());
		
-- 		SET __actualEvaluar = (SELECT WEEKDAY(_fecha));
-- 		IF __actualEvaluar = 6 THEN 
-- 			SET __vencimientoFinal = (DATE_ADD(_fecha, INTERVAL 8 DAY));
-- 		ELSE 
-- 			SET __vencimientoFinal = (DATE_ADD(_fecha, INTERVAL 7 DAY));
-- 		END IF;


-- 		SET __penalidadEvaluar = (DATE_ADD(__vencimientoFinal, INTERVAL _prorroga DAY));
-- 		SET __prorrogaEvaluar  = (SELECT WEEKDAY(__penalidadEvaluar));
-- 		IF __prorrogaEvaluar = 6 THEN 
-- 			SET __penalidadEvaluar = (DATE_ADD(__vencimientoFinal, INTERVAL 1 DAY));
-- 		END IF;

-- 		INSERT INTO cuota(
-- 			`cuo_credito`,`cuo_orden`,`cuo_monto`,`cuo_penalidad`,
-- 			`cuo_vencimiento`,`cuo_inicio_mora`,`cuo_estado`
-- 		)
-- 		VALUES(
-- 			__creditoID, 1, _pago, _mora,
-- 			__vencimientoFinal, __penalidadEvaluar, 'E'
-- 		);

-- 		IF _prenda > 0 THEN 
-- 			UPDATE prenda 
-- 				SET 
-- 					pren_estado = 'O'
-- 			WHERE
-- 				pren_codigo = _prenda;
-- 		END IF;
-- 	IF `_rollback` THEN
--         ROLLBACK;
--         SELECT 
--         	"Error" as "codigo",
--         	"No se pudo crear el credito." AS "mensaje",
--         	'' as 'id';
--     ELSE
--         COMMIT;
        
--         SELECT 
--         	'Ok' as 'codigo', 
--         	'Credito creado con exito.' AS 'mensaje',
--         	__creditoID as 'id';
--     END IF;
	
-- END $$
-- DELIMITER ;


-- Procedimiento 30
DROP PROCEDURE IF EXISTS proc_cliente_prendas_disponibles;
DELIMITER $$
CREATE PROCEDURE proc_cliente_prendas_disponibles
(IN _cliente INT)
BEGIN 
	SELECT 
		p.pren_codigo as 'id', 
		p.pren_descripcion as 'descripcion', 
		c.cat_nombre as 'categoria',
		c.cat_tipo as 'tipo', 
		p.pren_monto_previo as 'monto_previo',
		p.pren_reutilizacion as 'reutilizacion',
		p.pren_valoracion as 'valoracion', 
		p.pren_serial as 'serial',
		p.pren_modelo as 'modelo',
		p.pren_estado as 'estado'
	FROM prenda p
	INNER JOIN categoria c ON c.cat_codigo = p.pren_categoria
	WHERE 
		pren_cliente = _cliente AND 
		(p.pren_estado = 'D' OR p.pren_estado = 'R') 
	ORDER BY c.cat_nombre ASC;
END $$
DELIMITER ;


-- Procedimiento 31
DROP PROCEDURE IF EXISTS proc_credito_detalle;
DELIMITER $$
CREATE PROCEDURE proc_credito_detalle
(IN _credito INT, IN _FechaActual DATE)
BEGIN 
	SELECT  
		c.cred_codigo as 'id',
		c.cred_serie as 'codigo',
		DATE_FORMAT(c.cred_creacion, "%d/%m/%Y") as 'creacion',
		c.cred_creacion as 'fecha',
		DATE_FORMAT(c.cred_liquidacion, "%d/%m/%Y")as 'liquidacion',
		c.cred_empleado as 'empleado_id',
		c.cred_cliente as 'cliente_id',
		CONCAT(e.emp_nombres, ' ', e.emp_paterno, ' ', e.emp_materno) as 'empleado',
		CONCAT(cl.cli_nombres, ' ', cl.cli_paterno, ' ', cl.cli_materno) as 'cliente',
		IF(
			c.cred_prenda > 0,
			(SELECT pren_descripcion FROM prenda WHERE pren_codigo = c.cred_prenda),
			'SIN GARANTIA' 
		) as 'prenda',
		c.cred_prenda as 'prenda_id',
		c.cred_monto as 'monto',
		c.cred_prorroga as 'prorroga',
		c.cred_cargo as 'cargo',
		t.tar_tipo as 'tipo_tasa',
		c.cred_tasa as 'tasa',
		c.cred_pago_semanal as 'pago',
		c.cred_penalidad as 'penalidad',
		c.cred_amortizacion as 'amortizacion',
		c.cred_adelanto as 'adelanto',
		c.cred_aplica_penalidad as 'aplica_penalidad',
		IF(c.cred_estado = 'E',
			(SELECT 
				DATE_FORMAT(cuo_vencimiento, "%d/%m/%Y") 
			FROM cuota 
			WHERE 
				cuo_credito = c.cred_codigo
			ORDER BY cuo_orden DESC LIMIT 1),
			'No Definido'
		) as 'cuota_proxima',
		c.cred_estado as 'estado'
	FROM credito c 
	INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente
	INNER JOIN empleado e ON e.emp_codigo = c.cred_empleado
	INNER JOIN tarifa t ON t.tar_codigo = c.cred_tarifa
	WHERE
		c.cred_codigo = _credito;
END $$
DELIMITER ;

-- Procedimiento 32
DROP PROCEDURE IF EXISTS proc_prenda_detalle_creditos;
DELIMITER $$
CREATE PROCEDURE proc_prenda_detalle_creditos
(IN _prenda INT)
BEGIN 
	SELECT 
		c.cred_codigo as 'id',
		DATE_FORMAT(c.cred_creacion, "%d/%m/%Y") as 'creacion',
		c.cred_monto as 'monto',
		c.cred_estado as 'estado',
		(SELECT COUNT(*) FROM cuota WHERE cuo_credito = c.cred_codigo) as 'cuotas'
	FROM credito c WHERE cred_prenda = _prenda;
END $$
DELIMITER ;

-- Procedimiento 33
DROP PROCEDURE IF EXISTS proc_cliente_eliminar;
DELIMITER $$
CREATE PROCEDURE proc_cliente_eliminar
(IN _id INT)
BEGIN 
	DECLARE _creditos INT;
	DECLARE _prendas INT;

	SET _creditos = (SELECT COUNT(*) FROM credito WHERE cred_cliente = _id);
	SET _prendas  = (SELECT COUNT(*) FROM prenda WHERE pren_cliente = _id);

	IF _creditos = 0 THEN 

		IF _prendas = 0 THEN 
			DELETE FROM cliente WHERE cli_codigo = _id;
			DELETE FROM notas_cliente WHERE ncli_cliente = _id;
			SELECT 
				'Ok' as 'codigo',
				'Cliente eliminado.' as 'mensaje';
		ELSE 
			SELECT 
				'Error' as 'codigo',
				'El cliente tiene prendas asociadas.' as 'mensaje';
		END IF;

	ELSE 
		SELECT 
			'Error' as 'codigo',
			'El cliente tiene creditos asociados.' as 'mensaje';
	END IF;

END $$
DELIMITER ;

-- Procedimiento 34
DROP PROCEDURE IF EXISTS proc_prenda_propiedad;
DELIMITER $$
CREATE PROCEDURE proc_prenda_propiedad
(IN _prenda INT)
BEGIN 
	
	SELECT
		CONCAT(cl.cli_nombres, ' ', cl.cli_paterno, ' ', cl.cli_materno) as 'cliente',
		CONCAT(e.emp_nombres, ' ', e.emp_paterno, ' ', e.emp_materno) as 'empleado',
		pr.prop_inicio as 'inicio',
		IF(pr.prop_estado = 'A', 'En posesion', pr.prop_termino) as 'termino',
		pr.prop_motivo as 'motivo',
		pr.prop_estado as 'estado'
	FROM propiedad pr 
	INNER JOIN cliente cl ON cl.cli_codigo = pr.prop_cliente 
	INNER JOIN empleado e ON e.emp_codigo = pr.prop_empleado
	WHERE pr.prop_prenda = _prenda;
END $$
DELIMITER ;


-- Procedimiento 35
-- DROP PROCEDURE IF EXISTS proc_prenda_transferir;
-- DELIMITER $$
-- CREATE PROCEDURE proc_prenda_transferir
-- (IN _cliente INT, IN _prenda INT, IN _motivo VARCHAR(50), IN _empleado INT, IN _fecha DATE)
-- BEGIN 

-- 	DECLARE `_rollback` BOOL DEFAULT 0;
--     DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET `_rollback` = 1;

--     START TRANSACTION;

--     	UPDATE prenda 
--     		SET pren_cliente = _cliente 
--     	WHERE pren_codigo = _prenda;

--     	UPDATE propiedad 
--     		SET 
--     			prop_termino = _fecha,
--     			prop_estado  = 'I' 
--     	WHERE prop_prenda = _prenda AND prop_estado = 'A';

-- 		INSERT INTO propiedad(`prop_cliente`,`prop_empleado`,`prop_prenda`,`prop_inicio`,`prop_motivo`,`prop_estado`)
-- 		VALUES(_cliente, _empleado, _prenda, _fecha, UPPER(_motivo), 'A');

-- 	IF `_rollback` THEN
--         ROLLBACK;
--         SELECT 
--         	"Error" as "codigo",
--         	'' as 'id',
--         	"No se pudo transferir la prenda." AS "mensaje";
--     ELSE
--         COMMIT;
        
--         SELECT 
--         	'Ok' as 'codigo', 
--         	'Prenda transferida con exito.'AS 'mensaje';
--     END IF;

-- END $$
-- DELIMITER ;


-- Procedimiento 36
DROP PROCEDURE IF EXISTS proc_credito_lista;
DELIMITER $$
CREATE PROCEDURE proc_credito_lista
(IN _tipo CHAR(30), IN _buscar VARCHAR(50), IN _limite INT, IN _sucursal INT)
BEGIN
	IF _tipo = 'codigo' THEN 
		SELECT
			c.cred_codigo as 'id',
			c.cred_serie as 'codigo',
			DATE_FORMAT(c.cred_creacion, "%d/%m/%Y") as 'creacion',
			c.cred_monto as 'monto',
			CONCAT(cl.cli_nombres,' ',cl.cli_paterno,' ',cl.cli_materno) as 'cliente',
			c.cred_estado as 'estado',
			IF(
				c.cred_estado='E', 
				(SELECT COUNT(*) FROM cuota WHERE cuo_estado='V' AND cuo_credito=c.cred_codigo), 
				'---'
			) as 'cuotas_vencidas'
		FROM credito c 
		INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente
		WHERE 
			c.cred_serie LIKE CONCAT('%', _buscar, '%') AND 
			c.cred_sucursal = _sucursal 
		ORDER BY  
			CASE 
				WHEN c.cred_estado = 'E' then 1
				WHEN c.cred_estado = 'CC' then 2
				ELSE 3 
			END,
			c.cred_codigo DESC
		LIMIT _limite;

	ELSEIF _tipo = 'cliente' THEN
		SELECT
			c.cred_codigo as 'id',
			c.cred_serie as 'codigo',
			DATE_FORMAT(c.cred_creacion, "%d/%m/%Y") as 'creacion',
			c.cred_monto as 'monto',
			CONCAT(cl.cli_nombres,' ',cl.cli_paterno,' ',cl.cli_materno) as 'cliente',
			c.cred_estado as 'estado',
			IF(
				c.cred_estado='E', 
				(SELECT COUNT(*) FROM cuota WHERE cuo_estado='V' AND cuo_credito=c.cred_codigo), 
				'---'
			) as 'cuotas_vencidas'
		FROM credito c 
		INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente
		WHERE 
			(
				cl.cli_paterno LIKE CONCAT(_buscar, '%') OR
				cl.cli_documento LIKE CONCAT(_buscar, '%') OR 
				cl.cli_nombres LIKE CONCAT(_buscar, '%')
			) AND c.cred_sucursal = _sucursal 
		ORDER BY 
			CASE 
				WHEN c.cred_estado = 'E' then 1
				WHEN c.cred_estado = 'CC' then 2
				ELSE 3 
			END,
			c.cred_codigo DESC
		LIMIT _limite;

	ELSEIF _tipo = 'estado' THEN
		SELECT
			c.cred_codigo as 'id',
			c.cred_serie as 'codigo',
			DATE_FORMAT(c.cred_creacion, "%d/%m/%Y") as 'creacion',
			c.cred_monto as 'monto',
			CONCAT(cl.cli_nombres,' ',cl.cli_paterno,' ',cl.cli_materno) as 'cliente',
			c.cred_estado as 'estado',
			IF(
				c.cred_estado='E', 
				(SELECT COUNT(*) FROM cuota WHERE cuo_estado='V' AND cuo_credito=c.cred_codigo), 
				'---'
			) as 'cuotas_vencidas'
		FROM credito c 
		INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente
		WHERE 
			c.cred_estado = _buscar AND 
			c.cred_sucursal = _sucursal 
		ORDER BY c.cred_codigo DESC LIMIT _limite;

	ELSEIF _tipo = 'atraso' THEN
		SELECT
			c.cred_codigo as 'id',
			c.cred_serie as 'codigo',
			DATE_FORMAT(c.cred_creacion, "%d/%m/%Y") as 'creacion',
			c.cred_monto as 'monto',
			CONCAT(cl.cli_nombres,' ',cl.cli_paterno,' ',cl.cli_materno) as 'cliente',
			c.cred_estado as 'estado',
			IF(
				c.cred_estado='E', 
				(SELECT COUNT(*) FROM cuota WHERE cuo_estado='V' AND cuo_credito=c.cred_codigo), 
				'---'
			) as 'cuotas_vencidas'
		FROM credito c 
		INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente
		WHERE 
			(SELECT COUNT(*) FROM cuota WHERE cuo_estado='V' AND cuo_credito=c.cred_codigo) = _buscar AND 
			c.cred_sucursal = _sucursal 
		ORDER BY c.cred_codigo DESC LIMIT _limite;
	ELSE
		SELECT
			c.cred_codigo as 'id',
			c.cred_serie as 'codigo',
			DATE_FORMAT(c.cred_creacion, "%d/%m/%Y") as 'creacion',
			c.cred_monto as 'monto',
			CONCAT(cl.cli_nombres,' ',cl.cli_paterno,' ',cl.cli_materno) as 'cliente',
			c.cred_estado as 'estado',
			IF(
				c.cred_estado='E', 
				(SELECT COUNT(*) FROM cuota WHERE cuo_estado='V' AND cuo_credito=c.cred_codigo), 
				'---'
			) as 'cuotas_vencidas'
		FROM credito c 
		INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente
		WHERE c.cred_sucursal = _sucursal 
		ORDER BY  
			CASE 
				WHEN c.cred_estado = 'E' then 1
				WHEN c.cred_estado = 'CC' then 2
				ELSE 3 
			END,
			c.cred_codigo DESC
		LIMIT _limite;
	END IF;
END $$
DELIMITER ; 

-- order by case 
--     when name LIKE "%John%" then 1 
--     when name LIKE "%Doe%"  then 2 
--     else 3 
-- end

-- Procedimiento 36
-- DROP PROCEDURE IF EXISTS proc_credito_detalle;
-- DELIMITER $$
-- CREATE PROCEDURE proc_credito_detalle
-- (IN _id INT)
-- BEGIN
-- 	SELECT
-- 		c.cred_codigo as 'id',
-- 		c.cred_serie as 'codigo',
-- 		c.cred_creacion as 'creacion',
-- 		c.cred_liquidacion as 'liquidacion',
-- 		c.cred_monto as 'monto',
-- 		t.tar_tipo as 'tipo_tasa',
-- 		c.cred_tasa as 'tasa',
-- 		c.cred_pago_semanal as 'pago',
-- 		c.cred_penalidad as 'mora',
-- 		c.cred_amortizacion as 'amortizacion',
-- 		c.cred_cliente as 'cliente_id',
-- 		c.cred_empleado as 'empleado_id',
-- 		CONCAT(cl.cli_nombres,' ',cl.cli_paterno,' ',cl.cli_materno) as 'cliente',
-- 		CONCAT(e.emp_nombres,' ',e.emp_paterno,' ',e.emp_materno) as 'empleado',
-- 		IF(
-- 			c.cred_prenda > 0,
-- 			(SELECT p.pren_descripcion FROM prenda p WHERE p.pren_codigo = c.cred_prenda),
-- 			'SIN GARANTIA'
-- 		) as 'prenda',
-- 		c.cred_prenda as 'prenda_id',
-- 		c.cred_estado as 'estado'
-- 	FROM credito c 
-- 	INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente
-- 	INNER JOIN empleado e ON e.emp_codigo = c.cred_empleado 
-- 	INNER JOIN tarifa t ON t.tar_codigo = c.cred_tarifa
-- 	WHERE c.cred_codigo = _id;
-- END $$
-- DELIMITER ; 


-- Procedimiento 37
DROP PROCEDURE IF EXISTS proc_cliente_prendas;
DELIMITER $$
CREATE PROCEDURE proc_cliente_prendas
(IN _cliente INT, IN _tipo CHAR(10))
BEGIN 
	IF _tipo = 'disponible' THEN 
		SELECT 
			p.pren_codigo as 'id', 
			p.pren_descripcion as 'descripcion', 
			c.cat_nombre as 'categoria',
			c.cat_tipo as 'id_tipo',
			tp.tpren_nombre as 'tipo',
			p.pren_serial as 'serial',
			p.pren_modelo as 'modelo', 
			p.pren_estado as 'estado',
			p.pren_quilate as 'quilate',
			p.pren_peso as 'peso',
			p.pren_anio as 'anio',
			p.pren_placa as 'placa',
			p.pren_placa as 'marca',
			p.pren_kilometraje as 'kilometraje',
			p.pren_monto_previo as 'monto_previo',
			p.pren_reutilizacion as 'reutilizacion',
			p.pren_valoracion as 'valoracion'
		FROM prenda p
		INNER JOIN categoria c ON c.cat_codigo = p.pren_categoria 
		INNER JOIN tipo_prenda tp ON tp.tpren_codigo = c.cat_tipo
		WHERE 
			pren_cliente = _cliente AND 
			(p.pren_estado = 'D' OR p.pren_estado = 'R') 
		ORDER BY c.cat_nombre ASC;
	ELSE 
		SELECT 
			p.pren_codigo as 'id', 
			p.pren_descripcion as 'descripcion', 
			c.cat_nombre as 'categoria',
			c.cat_tipo as 'tipo', 
			p.pren_monto_previo as 'monto_previo',
			p.pren_reutilizacion as 'reutilizacion',
			p.pren_valoracion as 'valoracion',
			p.pren_serial as 'serial',
			p.pren_modelo as 'modelo', 
			p.pren_estado as 'estado'
		FROM prenda p
		INNER JOIN categoria c ON c.cat_codigo = p.pren_categoria
		WHERE 
			pren_cliente = _cliente
		ORDER BY c.cat_nombre ASC;
	END IF;
END $$
DELIMITER ;


-- Procedimiento 38
DROP PROCEDURE IF EXISTS proc_credito_cuotas;
DELIMITER $$
CREATE PROCEDURE proc_credito_cuotas
(IN _id INT)
BEGIN 
	SELECT 
		cu.cuo_codigo as 'id', 
		cu.cuo_orden as 'orden', 
		cu.cuo_monto as 'monto',
		cu.cuo_penalidad as 'penalidad',
		DATE_FORMAT(cu.cuo_vencimiento, "%d/%m/%Y") as 'vencimiento', 
		IF(
			(SELECT mor_credito FROM mora WHERE mor_cuota = cu.cuo_codigo AND mor_estado != 'PAG'),
			cu.cuo_dias_atraso,
			0
		) as 'atraso',
		cu.cuo_mora as 'mora',
		(cu.cuo_monto + cu.cuo_mora) as 'pago_total',
		cu.cuo_prorrateo as 'prorrateo', 
		cu.cuo_estado as 'estado'
	FROM cuota cu 
	INNER JOIN credito c ON c.cred_codigo = cu.cuo_credito
	WHERE c.cred_codigo = _id 
	ORDER BY cu.cuo_codigo DESC; 
END $$
DELIMITER ;



-- Proc 39
DROP PROCEDURE IF EXISTS proc_empleado_registro;
DELIMITER $$
CREATE PROCEDURE proc_empleado_registro
(
	IN _documento CHAR(20), IN _tipo_doc CHAR(50), IN _nombres VARCHAR(50), 
	IN _paterno VARCHAR(50), IN _materno VARCHAR(50), IN _ubigeo INT, 
	_direccion VARCHAR(100), IN _telefono CHAR(50), IN _email CHAR(50), IN _sucursal INT,
	IN _fecha DATE 
)
BEGIN 
	DECLARE __id INT;
	DECLARE __usuario CHAR(50);
	DECLARE `_rollback` BOOL DEFAULT 0;
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET `_rollback` = 1;
    START TRANSACTION;
		INSERT INTO empleado (
			`emp_documento`, `emp_tipo_documento`, `emp_nombres`, `emp_paterno`, 
			`emp_materno`, `emp_ubigeo`, `emp_direccion`, `emp_telefono`, `emp_email`, 
			`emp_sucursal`,`emp_creacion`) 
		VALUES (
			_documento,
			_tipo_doc,
			UPPER(_nombres),
			UPPER(_paterno),
			UPPER(_materno),
			_ubigeo,
			UPPER(_direccion),
			_telefono,
			_email,
			_sucursal,
			_fecha
		);

		SET __id = (SELECT LAST_INSERT_ID());

		SET __usuario = (
			CONCAT( 
				(SELECT SUBSTRING(_nombres,1,1)), 
				REPLACE(_paterno, " ", ""), 
				REPLACE(_materno, " ", "")
			)
		);


		INSERT INTO usuario(
			`usu_empleado`,`usu_rol`,`usu_usuario`,`usu_clave`,`usu_creacion`,`usu_estado`)
			VALUES(
				__id, 2, __usuario, _documento, _fecha, 'A'
			);

	IF `_rollback` THEN
        ROLLBACK;
        SELECT "Error" as "codigo","Proceso incompleto." AS "mensaje";
    ELSE
        COMMIT;
        
        SELECT 'Ok' as 'codigo','Registro completo.' AS 'mensaje', __id as 'id';
    END IF;
END $$
DELIMITER ;


-- Proc 40
DROP PROCEDURE IF EXISTS proc_empleado_lista;
DELIMITER $$
CREATE PROCEDURE proc_empleado_lista
(IN _tipo CHAR(30), IN _buscar CHAR(30), IN _limite INT, IN _sucursal INT)
BEGIN 
	
	IF _tipo = 'nombres' THEN
		SELECT
			e.emp_codigo as 'id', 
			e.emp_documento as 'documento',
			e.emp_tipo_documento as 'tipo_documento', 
			e.emp_nombres as 'nombres',
			e.emp_paterno as 'paterno',
			e.emp_materno as 'materno',
			u.ubi_distrito as 'ciudad',
			e.emp_direccion as 'direccion',
			e.emp_telefono as 'telefono',
			s.suc_descripcion as 'sucursal',
			e.emp_estado as 'estado'
		FROM empleado e
		INNER JOIN ubigeo u ON u.ubi_codigo = e.emp_ubigeo 
		INNER JOIN sucursal s ON s.suc_codigo = e.emp_sucursal
		WHERE 
			e.emp_nombres LIKE CONCAT(_buscar, '%') AND 
			e.emp_sucursal = _sucursal
		ORDER BY emp_nombres ASC LIMIT _limite;
	ELSEIF _tipo = 'doc' THEN
		SELECT
			e.emp_codigo as 'id', 
			e.emp_documento as 'documento',
			e.emp_tipo_documento as 'tipo_documento', 
			e.emp_nombres as 'nombres',
			e.emp_paterno as 'paterno',
			e.emp_materno as 'materno',
			u.ubi_distrito as 'ciudad',
			e.emp_direccion as 'direccion',
			e.emp_telefono as 'telefono',
			s.suc_descripcion as 'sucursal',
			e.emp_estado as 'estado'
		FROM empleado e
		INNER JOIN ubigeo u ON u.ubi_codigo = e.emp_ubigeo 
		INNER JOIN sucursal s ON s.suc_codigo = e.emp_sucursal
		WHERE
			e.emp_documento LIKE CONCAT(_buscar, '%') AND 
			e.emp_sucursal = _sucursal
		ORDER BY emp_nombres ASC LIMIT _limite;
	ELSE
		SELECT
			e.emp_codigo as 'id', 
			e.emp_documento as 'documento',
			e.emp_tipo_documento as 'tipo_documento', 
			e.emp_nombres as 'nombres',
			e.emp_paterno as 'paterno',
			e.emp_materno as 'materno',
			u.ubi_distrito as 'ciudad',
			e.emp_direccion as 'direccion',
			e.emp_telefono as 'telefono',
			s.suc_descripcion as 'sucursal',
			e.emp_estado as 'estado'
		FROM empleado e
		INNER JOIN ubigeo u ON u.ubi_codigo = e.emp_ubigeo
		INNER JOIN sucursal s ON s.suc_codigo = e.emp_sucursal
		WHERE 
			e.emp_sucursal = _sucursal 
		ORDER BY emp_nombres ASC LIMIT _limite;
	END IF;
	
END $$
DELIMITER ;


-- Proc 41
DROP PROCEDURE IF EXISTS proc_empleado_detalle;
DELIMITER $$
CREATE PROCEDURE proc_empleado_detalle
(IN _codigo INT)
BEGIN 
	SELECT 
		e.emp_codigo as 'id',
		e.emp_documento as 'documento',
		e.emp_tipo_documento as 'tipo_documento',
		e.emp_nombres as 'nombres',
		e.emp_paterno as 'paterno',
		e.emp_materno as 'materno',
		e.emp_ubigeo as 'ubigeo',
		u.ubi_distrito as 'ciudad',
		u.ubi_departamento as 'departamento',
		e.emp_direccion as 'direccion',
		e.emp_telefono as 'telefono',
		e.emp_imagen as 'imagen',
		e.emp_email as 'email',
		e.emp_estado 'estado',
		DATE_FORMAT(e.emp_creacion, "%d/%m/%Y")	as 'creacion',
		us.usu_usuario as 'usuario',
		us.usu_codigo as 'id_usuario',
		s.suc_descripcion as 'sucursal',
		e.emp_sucursal as 'id_sucursal'
	FROM empleado e 
	INNER JOIN ubigeo u ON u.ubi_codigo = e.emp_ubigeo
	INNER JOIN usuario us ON us.usu_empleado = e.emp_codigo
	INNER JOIN sucursal s ON s.suc_codigo = e.emp_sucursal
	WHERE e.emp_codigo = _codigo;
END $$
DELIMITER ;


-- Proc 42
DROP PROCEDURE IF EXISTS proc_usuario_lista;
DELIMITER $$
CREATE PROCEDURE proc_usuario_lista
()
BEGIN 
	SELECT
		u.usu_codigo as 'id',  
		CONCAT(e.emp_nombres,' ',e.emp_paterno,' ',e.emp_materno) as 'empleado',
		u.usu_usuario as 'usuario',
		r.rol_titulo as 'rol',
		u.usu_estado as 'estado'
	FROM usuario u
	INNER JOIN empleado e ON u.usu_empleado = e.emp_codigo 
	INNER JOIN roles r ON r.rol_codigo = u.usu_rol
	ORDER BY rol ASC LIMIT 10;
END $$
DELIMITER ;


-- Proc 43
DROP PROCEDURE IF EXISTS proc_sucursal_lista;
DELIMITER $$
CREATE PROCEDURE proc_sucursal_lista
(IN _tipo CHAR(10), IN _ubigeo INT)
BEGIN 
	IF _tipo = 'UBIGEO' THEN 
		SELECT
			s.suc_codigo as 'id', 
			s.suc_descripcion as 'descripcion',
			CONCAT(u.ubi_distrito,', ',u.ubi_departamento) as 'ciudad', 
			s.suc_direccion as 'direccion',
			s.suc_telefono as 'telefono',
			s.suc_estado as 'estado'
		FROM sucursal s 
		INNER JOIN ubigeo u ON u.ubi_codigo = s.suc_ubigeo 
		WHERE s.suc_ubigeo = u.ubi_codigo
		ORDER BY ciudad ASC LIMIT 10;
	ELSE 
		SELECT
			s.suc_codigo as 'id', 
			s.suc_descripcion as 'descripcion',
			CONCAT(u.ubi_distrito,', ',u.ubi_departamento) as 'ciudad', 
			s.suc_direccion as 'direccion',
			s.suc_telefono as 'telefono',
			s.suc_estado as 'estado'
		FROM sucursal s 
		INNER JOIN ubigeo u ON u.ubi_codigo = s.suc_ubigeo 
		ORDER BY ciudad ASC LIMIT 10;
	END IF;
END $$
DELIMITER ;



-- Proc 44
-- DROP PROCEDURE IF EXISTS proc_sucursal_registro;
-- DELIMITER $$
-- CREATE PROCEDURE proc_sucursal_registro
-- (
-- 	IN _descripcion VARCHAR(70), IN _ubigeo INT, 
-- 	IN _direccion VARCHAR(100), IN _telefono CHAR(50),
-- 	IN _fecha DATE
-- )
-- BEGIN 
-- 	DECLARE __id INT;
-- 	DECLARE `_rollback` BOOL DEFAULT 0;
--     DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET `_rollback` = 1;
--     START TRANSACTION;
-- 		INSERT INTO sucursal (
-- 			`suc_descripcion`, `suc_ubigeo`, `suc_direccion`, 
-- 			`suc_telefono`, `suc_creacion`, `suc_estado`) 
-- 		VALUES (
-- 			UPPER(_descripcion),
-- 			_ubigeo,
-- 			UPPER(_direccion),
-- 			_telefono,
-- 			_fecha,
-- 			'A'
-- 		);

-- 		SET __id = (SELECT LAST_INSERT_ID());
-- 	IF `_rollback` THEN
--         ROLLBACK;
--         SELECT "Error" as "codigo","Proceso incompleto." AS "mensaje";
--     ELSE
--         COMMIT;
        
--         SELECT 'Ok' as 'codigo','Registro completo.' AS 'mensaje', __id as 'id';
--     END IF;
-- END $$
-- DELIMITER ;


-- Proc 45
DROP PROCEDURE IF EXISTS proc_sucursal_actualizar;
DELIMITER $$
CREATE PROCEDURE proc_sucursal_actualizar
(
	IN _codigo INT, IN _descripcion VARCHAR(70), IN _ubigeo INT, IN _direccion VARCHAR(50), 
	IN _telefono VARCHAR(50), IN _estado CHAR(10)
)
BEGIN 
	DECLARE __affected INT;
	DECLARE `_rollback` BOOL DEFAULT 0;
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET `_rollback` = 1;

    START TRANSACTION;
		UPDATE sucursal 
			SET 
				suc_descripcion = UPPER(_descripcion),
				suc_ubigeo = _ubigeo,
				suc_direccion = UPPER(_direccion),
				suc_telefono = _telefono,
				suc_estado = _estado
		WHERE suc_codigo = _codigo;
		SET __affected = (SELECT ROW_COUNT());
	IF `_rollback` THEN
        ROLLBACK;
        SELECT "Error" as "codigo","Proceso incompleto." AS "mensaje";
    ELSE
        COMMIT;
        
        SELECT 
        	'Ok' as 'codigo',
        	IF(__affected > 0,'cambios aplicados.', 'Sin cambios.') AS 'mensaje';
    END IF;

END $$
DELIMITER ;


-- Proc 46
DROP PROCEDURE IF EXISTS proc_sucursal_detalle;
DELIMITER $$
CREATE PROCEDURE proc_sucursal_detalle
(IN _id INT)
BEGIN 
	SELECT
		s.suc_codigo as 'id', 
		s.suc_descripcion as 'descripcion',
		s.suc_ubigeo as 'ubigeo',
		u.ubi_distrito as 'ciudad', 
		u.ubi_departamento as 'departamento',
		s.suc_direccion as 'direccion',
		s.suc_telefono as 'telefono',
		s.suc_sigla as 'sigla',
		s.suc_cuenta_tipo as 'cuenta_tipo',
		s.suc_cuenta_numero as 'cuenta_numero',
		s.suc_banco as 'banco',
		s.suc_orden as 'orden',
		s.suc_serie as 'serie',
		s.suc_pago_serie as 'pago_serie',
		DATE_FORMAT(s.suc_creacion, "%d/%m/%Y") as 'creacion', 
		s.suc_estado as 'estado'
	FROM sucursal s 
	INNER JOIN ubigeo u ON u.ubi_codigo = s.suc_ubigeo 
	WHERE s.suc_codigo = _id;
END $$
DELIMITER ;


-- Procedimiento 47
DROP PROCEDURE IF EXISTS proc_sucursal_eliminar;
DELIMITER $$
CREATE PROCEDURE proc_sucursal_eliminar
( IN _codigo INT )
BEGIN 
	DECLARE __affected INT;
	DECLARE __verifica INT;
    SET __verifica = (SELECT COUNT(*) FROM empleado WHERE emp_sucursal = _codigo);

    IF __verifica > 0 THEN 
    	SELECT 
        	"Error" as "codigo",
        	"No se puede eliminar la sucursal tiene empleados activos asociados." AS "mensaje";
    ELSE 
		DELETE FROM sucursal WHERE suc_codigo = _codigo;
		SET __affected = (SELECT ROW_COUNT());
		IF __affected > 0 THEN 
			SELECT 
	        	"Ok" as "codigo",
	        	"Sucursal eliminada." AS "mensaje";
		ELSE
			SELECT 
	        	"Error" as "codigo",
	        	"El proceso de eliminacion no se completo." AS "mensaje";
		END IF;
    END IF;
END $$
DELIMITER ;


-- Proc 48
DROP PROCEDURE IF EXISTS proc_sucursal_empleado;
DELIMITER $$
CREATE PROCEDURE proc_sucursal_empleado
(IN _id INT)
BEGIN 
	SELECT
		e.emp_codigo as 'id', 
		e.emp_documento as 'documento',
		e.emp_tipo_documento as 'tipo_documento', 
		e.emp_nombres as 'nombres',
		e.emp_paterno as 'paterno',
		e.emp_materno as 'materno',
		e.emp_direccion as 'direccion',
		e.emp_telefono as 'telefono',
		e.emp_estado as 'estado'
	FROM empleado e
	INNER JOIN sucursal s ON s.suc_codigo = e.emp_sucursal 
	WHERE s.suc_codigo = _id 
	ORDER BY emp_nombres ASC;
END $$
DELIMITER ;


-- Proc 49
DROP PROCEDURE IF EXISTS proc_sucursal_activo;
DELIMITER $$
CREATE PROCEDURE proc_sucursal_activo
()
BEGIN 
	SELECT
		s.suc_codigo as 'id', 
		s.suc_descripcion as 'descripcion',
		CONCAT(u.ubi_distrito,', ',u.ubi_departamento) as 'ciudad', 
		s.suc_direccion as 'direccion',
		s.suc_estado as 'estado'
	FROM sucursal s 
	INNER JOIN ubigeo u ON u.ubi_codigo = s.suc_ubigeo 
	WHERE s.suc_estado = 'A'
	ORDER BY ciudad ASC LIMIT 10;
END $$
DELIMITER ;


-- Proc 50
DROP PROCEDURE IF EXISTS proc_empleado_actualizar;
DELIMITER $$
CREATE PROCEDURE proc_empleado_actualizar
(IN _codigo INT, IN _documento CHAR(20), IN _tipo_doc CHAR(50), IN _nombres VARCHAR(50), 
IN _paterno VARCHAR(50), IN _materno VARCHAR(50),IN _ubigeo INT, _direccion VARCHAR(100), 
IN _telefono CHAR(50), IN _email CHAR(50), IN _sucursal INT)
BEGIN 
	DECLARE __affected INT;
	DECLARE `_rollback` BOOL DEFAULT 0;
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET `_rollback` = 1;

    START TRANSACTION;
		UPDATE empleado 
			SET 
				emp_documento = _documento,
				emp_tipo_documento = _tipo_doc,
				emp_nombres = UPPER(_nombres),
				emp_paterno = UPPER(_paterno),
				emp_materno = UPPER(_materno),
				emp_ubigeo  = _ubigeo,
				emp_direccion = UPPER(_direccion),
				emp_telefono  = _telefono,
				emp_email  = _email,
				emp_sucursal  = _sucursal
		WHERE emp_codigo = _codigo;
		SET __affected = (SELECT ROW_COUNT());
	IF `_rollback` THEN
        ROLLBACK;
        SELECT "Error" as "codigo","Proceso incompleto." AS "mensaje";
    ELSE
        COMMIT;
        
        SELECT 
        	'Ok' as 'codigo', 
        	IF(__affected > 0,'cambios aplicados.', 'Sin cambios.') AS 'mensaje';
    END IF;

END $$
DELIMITER ;



-- Proc 51
DROP PROCEDURE IF EXISTS proc_usuario_detalle;
DELIMITER $$
CREATE PROCEDURE proc_usuario_detalle
(IN _id INT)
BEGIN 
	SELECT
		u.usu_codigo as 'id',  
		u.usu_empleado as 'id_empleado',
		CONCAT(e.emp_nombres,' ',e.emp_paterno,' ',e.emp_materno) as 'empleado',
		u.usu_usuario as 'usuario',
		r.rol_titulo as 'rol',
		e.emp_imagen as 'imagen',
		CONCAT(e.emp_codigo, '__',e.emp_documento) as 'nombre_imagen',
		DATE_FORMAT(e.emp_creacion, "%d/%m/%Y") as 'creacion',
		u.usu_estado as 'estado'
	FROM usuario u
	INNER JOIN empleado e ON u.usu_empleado = e.emp_codigo 
	INNER JOIN roles r ON r.rol_codigo = u.usu_rol 
	WHERE usu_codigo = _id 
	ORDER BY rol ASC LIMIT 10;
END $$
DELIMITER ;


-- Procedimiento 52
DROP PROCEDURE IF EXISTS proc_usuario_modulo;
DELIMITER $$
CREATE PROCEDURE proc_usuario_modulo 
(IN _id INT)
BEGIN 

	SELECT 
		IF( mod_codigo NOT IN 
			(SELECT 
				mo.mod_codigo 
				FROM menu me 
				INNER JOIN modulo mo ON mo.mod_codigo = me.men_modulo 
				WHERE me.men_usuario = _id), 
			'NO','SI') AS 'estado',
		m.mod_codigo as 'id', 
		m.mod_titulo as 'nombre', 
		m.mod_descripcion as 'descripcion', 
		m.mod_icono as 'icono'
	FROM modulo m 
	ORDER BY m.mod_titulo ASC;

END $$
DELIMITER ;


-- Procedimiento 53
DROP PROCEDURE IF EXISTS proc_usuario_permiso;
DELIMITER $$
CREATE PROCEDURE proc_usuario_permiso
( IN _id INT )
BEGIN 
	SELECT 
		p.perm_codigo as 'id',
		p.perm_titulo as 'titulo',
		p.perm_nombre as 'nombre',
		p.perm_descripcion as 'descripcion',
		IF(
			(SELECT COUNT(*) FROM usuario_permiso 
				WHERE up_permiso = p.perm_nombre AND up_usuario = _id) > 0,
			"SI", "NO") AS 'estado' 
		FROM permiso p 
		ORDER BY p.perm_titulo ASC;
END $$
DELIMITER ;


-- Procedimiento 54
DROP PROCEDURE IF EXISTS proc_usuario_permiso_agregar;
DELIMITER $$
CREATE PROCEDURE proc_usuario_permiso_agregar
( IN _usuario INT, IN _permiso CHAR(50), IN _fecha DATE )
BEGIN 
	INSERT INTO usuario_permiso(up_usuario, up_permiso, up_fecha, up_estado) 
		VALUES(_usuario, _permiso, _fecha, 'A');
END $$
DELIMITER ;


-- Procedimiento 55
DROP PROCEDURE IF EXISTS proc_usuario_modulo_agregar;
DELIMITER $$
CREATE PROCEDURE proc_usuario_modulo_agregar
( IN _usuario INT, IN _modulo INT)
BEGIN 
	INSERT INTO menu(men_usuario, men_modulo, men_estado) 
		VALUES(_usuario, _modulo, 'A');
END $$
DELIMITER ;


-- Procedimiento 56
DROP PROCEDURE IF EXISTS proc_usuario_reiniciar;
DELIMITER $$
CREATE PROCEDURE proc_usuario_reiniciar
( IN _id INT)
BEGIN 
	DECLARE __nombre VARCHAR(50);
	DECLARE __paterno VARCHAR(50);
	DECLARE __materno VARCHAR(50);
	DECLARE __dni CHAR(20); 
	DECLARE __usuario VARCHAR(50);
	DECLARE __affected INT;
	DECLARE `_rollback` BOOL DEFAULT 0;
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET `_rollback` = 1;

    START TRANSACTION;

    	SELECT emp_nombres, emp_paterno, emp_materno, emp_documento INTO 
    		__nombre, __paterno, __materno, __dni FROM empleado WHERE emp_codigo = _id;

		SET __usuario = (
			CONCAT( 
				(SELECT SUBSTRING(__nombre,1,1)), 
				REPLACE(__paterno, " ", ""), 
				REPLACE(__materno, " ", "")
			)
		);

		UPDATE usuario 
			SET 
				usu_usuario = __usuario,
				usu_clave = __dni
			WHERE usu_empleado = _id;
		SET __affected = (SELECT ROW_COUNT());
	IF `_rollback` THEN
        ROLLBACK;
        SELECT "Error" as "codigo","Proceso incompleto." AS "mensaje";
    ELSE
        COMMIT;
        
        SELECT 
        	'Ok' as 'codigo', 
        	'Usuario reiniciado.' AS 'mensaje';
    END IF;
END $$
DELIMITER ;


-- Proc 57
DROP PROCEDURE IF EXISTS proc_usuario_actualizar_clave;
DELIMITER $$
CREATE PROCEDURE proc_usuario_actualizar_clave
(IN _id INT, IN _clave CHAR(50))
BEGIN 
	DECLARE __affected INT;

	UPDATE usuario 
		SET usu_clave = _clave
	WHERE usu_codigo = _id;

	SET __affected = (SELECT ROW_COUNT());

	SELECT
		'Ok' as 'codigo',
		IF(__affected > 0, 'Contraseña actualizada', 'Sin cambios') as 'mensaje';

END $$
DELIMITER ;




-- Procedimiento 58
DROP PROCEDURE IF EXISTS proc_electro_creditos_recientes;
DELIMITER $$
CREATE PROCEDURE proc_electro_creditos_recientes
(IN _sucursal INT)
BEGIN
	
	SELECT
		c.cred_codigo as 'id',
		c.cred_serie as 'codigo',
		DATE_FORMAT(c.cred_creacion, "%d/%m/%Y") as 'creacion',
		c.cred_monto as 'monto',
		CONCAT(cl.cli_nombres,' ',cl.cli_paterno,' ',cl.cli_materno) as 'cliente',
		c.cred_estado as 'estado'
	FROM credito c 
	INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente
	WHERE 
		(c.cred_estado = 'E' OR c.cred_estado = 'CC') AND 
		c.cred_sucursal = _sucursal   
	ORDER BY c.cred_creacion DESC LIMIT 10;

END $$
DELIMITER ;


-- Procedimiento 59
DROP PROCEDURE IF EXISTS proc_electro_cuotas_recientes;
DELIMITER $$
CREATE PROCEDURE proc_electro_cuotas_recientes
(IN _sucursal INT)
BEGIN
	SELECT
		c.cred_codigo as 'id',
		c.cred_serie as 'codigo',
		cu.cuo_orden as 'orden',
		DATE_FORMAT(cu.cuo_vencimiento, "%d/%m/%Y") as 'vencimiento',
		cu.cuo_monto as 'monto',
		cu.cuo_estado as 'estado'
	FROM cuota cu 
	INNER JOIN credito c ON c.cred_codigo = cu.cuo_credito
	WHERE 
		(c.cred_estado = 'E' OR c.cred_estado = 'CC') AND 
		c.cred_sucursal = _sucursal   
	ORDER BY cu.cuo_codigo DESC LIMIT 10;
END $$
DELIMITER ;  


-- Procedimiento 60
DROP PROCEDURE IF EXISTS proc_credito_recalibrar;
DELIMITER $$
CREATE PROCEDURE proc_credito_recalibrar
(
	IN _id INT, IN _monto DECIMAL(8,2), IN _tasa DECIMAL(8,2),
	IN _cargo DECIMAL(8,2), IN _interes DECIMAL(8,2), IN _mora DECIMAL(8,2),
	IN _tipo CHAR(10)
)
BEGIN
	DECLARE _RecPago DECIMAL(8,2);
	DECLARE _RecMora DECIMAL(8,2);
	DECLARE _RecTasa DECIMAL(8,2);
	DECLARE _TotalTasa DECIMAL(8,2);
	DECLARE _msg CHAR(50);
	DECLARE _code CHAR(50);
	DECLARE `_rollback` BOOL DEFAULT 0;
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET `_rollback` = 1;


    START TRANSACTION;

		IF _tipo = 'F' THEN
			SET _RecPago = _interes;
			SET _RecMora = _mora;

			UPDATE credito 
			SET 
				cred_pago_semanal = _RecPago,
				cred_penalidad = _RecMora,
				cred_tasa = _interes
			WHERE cred_codigo = _id;

			UPDATE cuota 
			SET 
				cuo_monto = _RecPago, 
				cuo_penalidad = _RecMora
			WHERE 
				cuo_credito = _id AND 
				cuo_estado = 'E'; 

			SET _code = 'Ok';
			SET _msg = 'Calibracion ejecutada.';

		ELSEIF _tipo = 'P' THEN
			SET _TotalTasa = _interes + _cargo;
			SET _RecPago = ( (_monto * _TotalTasa)/100 );
			SET _RecMora = ( (_monto * _mora)/100 );

			UPDATE credito 
			SET 
				cred_pago_semanal = _RecPago,
				cred_penalidad = _RecMora,
				cred_tasa = _interes
			WHERE cred_codigo = _id;

			UPDATE cuota 
			SET 
				cuo_monto = _RecPago, 
				cuo_penalidad = _RecMora
			WHERE 
				cuo_credito = _id AND 
				cuo_estado = 'E'; 

			SET _code = 'Ok';
			SET _msg = 'Calibracion ejecutada.';

		ELSE
			SET _code = 'Warn';
			SET _msg = 'Tipo de tarifa desconocida.';
		END IF;

	IF `_rollback` THEN
        ROLLBACK;
        SELECT 
        	"Error" as "codigo",
        	"Proceso incompleto." AS "mensaje";
    ELSE
        COMMIT;
        SELECT 
        	_code as 'codigo', 
        	_msg AS 'mensaje';
    END IF;

END $$
DELIMITER ;  


-- Procedimiento 61
DROP PROCEDURE IF EXISTS proc_configuracion;
DELIMITER $$
CREATE PROCEDURE proc_configuracion()
BEGIN
	SELECT
		c.conf_codigo as 'id',
		c.conf_nombre as 'nombre',
		c.conf_descripcion as 'descripcion',
		c.conf_valor as 'valor',
		c.conf_estado as 'estado'
	FROM configuracion c   
	ORDER BY c.conf_nombre ASC;
END $$
DELIMITER ;  

-- Procedimiento 62
DROP PROCEDURE IF EXISTS proc_nota_cliente_lista;
DELIMITER $$
CREATE PROCEDURE proc_nota_cliente_lista(IN _id INT)
BEGIN
	SELECT
		n.ncli_codigo as 'id',
		concat(e.emp_nombres, ' ',e.emp_paterno) as 'empleado',
		n.ncli_descripcion as 'descripcion',
		DATE_FORMAT(n.ncli_fecha, "%d/%m/%Y") as 'fecha'
	FROM notas_cliente n 
	INNER JOIN empleado e ON e.emp_codigo = n.ncli_empleado  
	WHERE n.ncli_cliente = _id
	ORDER BY n.ncli_codigo DESC;
END $$
DELIMITER ;  

-- -- Procedimiento 62
-- DROP PROCEDURE IF EXISTS proc_nota_cliente_lista;
-- DELIMITER $$
-- CREATE PROCEDURE proc_nota_cliente_lista(IN _id INT)
-- BEGIN
-- 	SELECT
-- 		n.ncli_codigo as 'id',
-- 		n.ncli_nombre as 'nombre',
-- 		n.ncli_descripcion as 'descripcion',
-- 		DATE_FORMAT(n.ncli_fecha, "%d/%m/%Y") as 'fecha',
-- 		n.ncli_estado as 'estado'
-- 	FROM notas_cliente n    
-- 	WHERE n.ncli_cliente = _id
-- 	ORDER BY n.ncli_fecha ASC;
-- END $$
-- DELIMITER ;  

-- Procedimiento 63
DROP PROCEDURE IF EXISTS proc_nota_cliente_registro;
DELIMITER $$
CREATE PROCEDURE proc_nota_cliente_registro
( IN _cliente INT, IN _empleado INT, IN _nombre VARCHAR(50), IN _descripcion VARCHAR(200), IN _fecha DATE)
BEGIN 
	DECLARE _id INT;
	DECLARE `_rollback` BOOL DEFAULT 0;
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET `_rollback` = 1;

    START TRANSACTION;
    	INSERT INTO notas_cliente(ncli_cliente, ncli_empleado, ncli_nombre, ncli_descripcion, ncli_fecha)
    		VALUES(_cliente, _empleado, UPPER(_nombre), UPPER(_descripcion), _fecha);
		
	IF `_rollback` THEN
        ROLLBACK;
        SELECT "Error" as "codigo",'' as 'id', "Proceso incompleto." AS "mensaje";
    ELSE
        COMMIT;
        SET _id = (SELECT LAST_INSERT_ID());
        SELECT 
        	'Ok' as 'codigo', 
        	_id as 'id',
        	'Nota Agregada.' AS 'mensaje';
    END IF;
END $$
DELIMITER ;

-- -- Procedimiento 64
-- DROP PROCEDURE IF EXISTS proc_nota_cliente_eliminar;
-- DELIMITER $$
-- CREATE PROCEDURE proc_nota_cliente_eliminar
-- ( IN _id INT)
-- BEGIN 
-- 	DECLARE `_rollback` BOOL DEFAULT 0;
--     DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET `_rollback` = 1;

--     START TRANSACTION;
--     	DELETE FROM notas_cliente WHERE ncli_codigo = _id;
		
-- 	IF `_rollback` THEN
--         ROLLBACK;
--         SELECT 
--         	"Error" as "codigo",
--         	"Proceso incompleto." AS "mensaje";
--     ELSE
--         COMMIT;
--         SELECT 
--         	'Ok' as 'codigo', 
--         	'Nota Eliminada.' AS 'mensaje';
--     END IF;
-- END $$
-- DELIMITER ;

-- Procedimiento 65
DROP PROCEDURE IF EXISTS proc_credito_cuotas_regularizacion;
DELIMITER $$
CREATE PROCEDURE proc_credito_cuotas_regularizacion
(IN _id INT)
BEGIN 
	SELECT 
		cu.cuo_codigo as 'id', 
		cu.cuo_orden as 'orden', 
		cu.cuo_monto as 'monto',
		cu.cuo_penalidad as 'penalidad',
		DATE_FORMAT(cu.cuo_vencimiento, "%d/%m/%Y") as 'vencimiento', 
		cu.cuo_dias_atraso as 'atraso',
		cu.cuo_mora as 'mora',
		(cu.cuo_monto + cu.cuo_mora) as 'pago_total',
		cu.cuo_prorrateo as 'prorrateo', 
		cu.cuo_estado as 'estado'
	FROM cuota cu 
		INNER JOIN credito c ON c.cred_codigo = cu.cuo_credito
		WHERE c.cred_codigo = _id; 
END $$
DELIMITER ;


-- REGULARIZACION 

-- Procedimiento 66
DROP PROCEDURE IF EXISTS proc_regularizacion_cuotas;
DELIMITER $$
CREATE PROCEDURE proc_regularizacion_cuotas
(IN _id INT, IN _FechaActual DATE)
BEGIN 
	SELECT 
		cu.cuo_codigo as 'id', 
		IF(cu.cuo_orden < 10, CONCAT('0',cu.cuo_orden), cu.cuo_orden) as 'orden', 
		DATE_FORMAT(cu.cuo_vencimiento, "%d/%m/%Y") as 'vencimiento', 
		IF(
			_FechaActual < cu.cuo_vencimiento,
			cu.cuo_prorrateo,
			cu.cuo_monto
		) as 'monto',
		cu.cuo_estado as 'estado'
	FROM cuota cu 
		INNER JOIN credito c ON c.cred_codigo = cu.cuo_credito
		WHERE 
		c.cred_codigo = _id AND 
		(cu.cuo_estado = 'V' OR cu.cuo_estado = 'E' || cu.cuo_estado = 'CC') AND 
		cu.cuo_vencimiento <= _FechaActual; 
END $$
DELIMITER ;

-- Procedimiento 67
DROP PROCEDURE IF EXISTS proc_recuperacion_cuota;
DELIMITER $$
CREATE PROCEDURE proc_recuperacion_cuota
(IN _id INT, IN _FechaActual DATE)
BEGIN 
	SELECT 
		cu.cuo_codigo as 'id', 
		IF(cu.cuo_orden < 10, CONCAT('0',cu.cuo_orden), cu.cuo_orden) as 'orden', 
		DATE_FORMAT(cu.cuo_vencimiento, "%d/%m/%Y") as 'vencimiento', 
		IF(
			_FechaActual >= cu.cuo_vencimiento OR cu.cuo_orden = 1,
			cu.cuo_monto,
			cu.cuo_prorrateo
		) as 'monto',
		IF(
			_FechaActual < cu.cuo_vencimiento,
			'PROR',
			'NORM'
		) as 'tipo_cobro',
		cu.cuo_estado as 'estado'
	FROM cuota cu 
		INNER JOIN credito c ON c.cred_codigo = cu.cuo_credito
		WHERE 
			c.cred_codigo = _id AND
			(c.cred_estado = 'E' OR c.cred_estado = 'CC') AND 
			(cu.cuo_estado = 'V' OR cu.cuo_estado = 'E' OR cu.cuo_estado = 'CC'); 
END $$
DELIMITER ;


-- Procedimiento 68
DROP PROCEDURE IF EXISTS proc_mora_pendiente;
DELIMITER $$
CREATE PROCEDURE proc_mora_pendiente
(IN _id INT)
BEGIN 
	SELECT 
		m.mor_codigo as 'id',
		m.mor_credito as 'credito',
		m.mor_cuota as 'cuota',
		m.mor_dias as 'dias',
		c.cuo_penalidad as 'penalidad',
		ROUND(m.mor_monto,1) as 'monto',
		m.mor_estado as 'estado'
	FROM mora m 
	INNER JOIN cuota c ON c.cuo_codigo = m.mor_cuota
		WHERE m.mor_credito = _id AND m.mor_estado = 'PEN'; 
END $$
DELIMITER ;

-- Procedimiento 69
DROP PROCEDURE IF EXISTS proc_mora_pospuesta;
DELIMITER $$
CREATE PROCEDURE proc_mora_pospuesta
(IN _id INT)
BEGIN 
	SELECT 
		m.mor_codigo as 'id',
		m.mor_credito as 'credito',
		m.mor_cuota as 'cuota',
		m.mor_dias as 'dias',
		ROUND(m.mor_monto,1) as 'monto',
		c.cuo_penalidad as 'penalidad',
		m.mor_estado as 'estado'
	FROM mora m 
	INNER JOIN cuota c ON c.cuo_codigo = m.mor_cuota
		WHERE m.mor_credito = _id AND m.mor_estado = 'POS'; 
END $$
DELIMITER ;


-- Procedimiento 70
DROP PROCEDURE IF EXISTS proc_pago_informacion;
DELIMITER $$
CREATE PROCEDURE proc_pago_informacion
(IN _id INT)
BEGIN
	SELECT
		p.pag_codigo as 'id',
		c.cred_codigo as 'credito',
		c.cred_serie as 'codigo_credito',
		p.pag_serie as 'serie',
		DATE_FORMAT(p.pag_fecha, "%d/%m/%Y %H:%i:%s") as 'fecha',
		p.pag_forma_pago as 'forma',
		p.pag_comprobante as 'comprobante',
		IF(
			(p.pag_forma_pago = 'DEP' OR p.pag_forma_pago = 'TRAN') AND p.pag_banco > 0,
			(select ban_nombre from banco WHERE ban_codigo = p.pag_banco),
			''
		) as 'banco',
		p.pag_subtotal as 'subtotal',
		p.pag_descuento as 'descuento',
		p.pag_total as 'total',
		p.pag_recibido as 'recibido',
		p.pag_devuelto as 'devuelto',
		p.pag_concepto as 'concepto',
		CONCAT(e.emp_nombres,' ',e.emp_paterno,' ',e.emp_materno) as 'empleado',
		CONCAT(cl.cli_nombres,' ',cl.cli_paterno,' ',cl.cli_materno) as 'cliente'
	FROM pago p 
	INNER JOIN credito c ON c.cred_codigo = p.pag_credito
	INNER JOIN empleado e ON e.emp_codigo = p.pag_empleado
	INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente
	WHERE 
		p.pag_codigo = _id;
END $$
DELIMITER ; 

-- Procedimiento 71
DROP PROCEDURE IF EXISTS proc_pago_detalle;
DELIMITER $$
CREATE PROCEDURE proc_pago_detalle
(IN _id INT)
BEGIN
	SELECT
		pd.pdet_codigo as 'id',
		pd.pdet_tipo as 'tipo',
		IF(
			pd.pdet_tipo = 'CU', 
			(select concat('- Vencimiento: ',DATE_FORMAT(cuo_vencimiento, "%d/%m/%Y")) from cuota where cuo_codigo = pd.pdet_id_ref), 
			''
		) as 'subref',
		pd.pdet_referencia as 'referencia',
		pd.pdet_id_ref as 'id_referencia',
		pd.pdet_monto as 'monto'
	FROM pago_detalle pd 
	WHERE pd.pdet_pago = _id;
END $$
DELIMITER ; 



-- Procedimiento 72
DROP PROCEDURE IF EXISTS proc_mora_deuda_total;
DELIMITER $$
CREATE PROCEDURE proc_mora_deuda_total
(IN _id INT)
BEGIN 
	SELECT 
		m.mor_codigo as 'id',
		m.mor_credito as 'credito',
		m.mor_cuota as 'cuota',
		m.mor_dias as 'dias',
		ROUND(m.mor_monto,1) as 'monto',
		IF(m.mor_estado = 'POS', 'MORA POSPUESTA', 'MORA') as 'descripcion'
	FROM mora m 
	INNER JOIN credito cd ON cd.cred_codigo = m.mor_credito
	WHERE 
		m.mor_credito = _id AND
		(cd.cred_estado = 'E' OR cd.cred_estado = 'CC') AND
		m.mor_estado != 'PAG'; 
END $$
DELIMITER ;


-- Procedimiento 73
DROP PROCEDURE IF EXISTS proc_capital_pendiente;
DELIMITER $$
CREATE PROCEDURE proc_capital_pendiente
(IN _id INT)
BEGIN 
	SELECT 
		c.cred_codigo as 'id',
		(c.cred_monto - c.cred_amortizacion) as 'monto'
	FROM credito c
	WHERE 
		c.cred_codigo = _id AND 
		(c.cred_estado = 'E' OR c.cred_estado = 'CC');
END $$
DELIMITER ;



-- Procedimiento 74
DROP PROCEDURE IF EXISTS proc_evaluar_recuperacion;
DELIMITER $$
CREATE PROCEDURE proc_evaluar_recuperacion
(IN _id INT)
BEGIN 
	DECLARE _vcuota INT;
	DECLARE _vmora INT;
	DECLARE _vcapital DECIMAL(8,2);

    SET _vcuota = (SELECT COUNT(*) FROM cuota WHERE cuo_credito = _id AND (cuo_estado = 'E' OR cuo_estado = 'V'));
    SET _vmora = (SELECT COUNT(*) FROM mora WHERE mor_credito = _id AND (mor_estado = 'PEN' OR mor_estado = 'POS'));
    SET _vcapital = (SELECT (cred_monto - cred_amortizacion) as 'rest' FROM credito WHERE cred_codigo = _id);
    IF _vcuota = 0 AND _vmora = 0 AND _vcapital = 0 THEN 
		SELECT 'PERMITIDO' as 'codigo';
	ELSE 
		SELECT 'DENEGADO' as 'codigo';
	END IF;
END $$
DELIMITER ;

-- Procedimiento 75
DROP PROCEDURE IF EXISTS proc_credito_pagos;
DELIMITER $$
CREATE PROCEDURE proc_credito_pagos
(IN _id INT)
BEGIN
	SELECT
		p.pag_codigo as 'id',
		p.pag_serie as 'serie',
		DATE_FORMAT(p.pag_fecha, "%d/%m/%Y %H:%i:%s") as 'fecha',
		p.pag_forma_pago as 'forma',
		p.pag_total as 'total',
		p.pag_concepto as 'concepto'
	FROM pago p
	WHERE 
		p.pag_credito = _id 
	ORDER BY p.pag_codigo DESC;
END $$
DELIMITER ; 

-- Procedimiento 76
DROP PROCEDURE IF EXISTS proc_credito_prorrogas;
DELIMITER $$
CREATE PROCEDURE proc_credito_prorrogas
(IN _id INT)
BEGIN
	SELECT
		DATE_FORMAT(p.pror_inicio, "%d/%m/%Y") as 'inicio',
		DATE_FORMAT(p.pror_termino, "%d/%m/%Y") as 'termino',
		p.pror_estado as 'estado'
	FROM prorroga p
	WHERE 
		p.pror_credito = _id;
END $$
DELIMITER ; 

-- Procedimiento 77
DROP PROCEDURE IF EXISTS proc_contrato_credito;
DELIMITER $$
CREATE PROCEDURE proc_contrato_credito
(IN _id INT)
BEGIN 
	SELECT  
		c.cred_codigo as 'id',
		c.cred_serie as 'codigo',
		DATE_FORMAT(c.cred_creacion, "%d/%m/%Y") as 'creacion',
		c.cred_creacion as 'fecha',
		c.cred_liquidacion as 'liquidacion',
		CONCAT(e.emp_nombres, ' ', e.emp_paterno, ' ', e.emp_materno) as 'empleado',
		cl.cli_nombres as 'cliente_nombre',
		CONCAT(cl.cli_paterno, ' ', cl.cli_materno) as 'cliente_apellido',
		cl.cli_documento as 'cliente_documento',
		cl.cli_direccion as 'cliente_direccion',
		u.ubi_distrito as 'cliente_ciudad',
		u.ubi_departamento as 'cliente_departamento',
		cl.cli_telefono as 'cliente_telefono',
		IF(
			c.cred_prenda > 0,
			(SELECT pren_descripcion FROM prenda WHERE pren_codigo = c.cred_prenda),
			'SIN GARANTIA' 
		) as 'prenda',
		c.cred_monto as 'monto',
		c.cred_prorroga as 'prorroga',
		c.cred_cargo as 'cargo',
		t.tar_tipo as 'tipo_tasa',
		c.cred_tasa as 'tasa',
		c.cred_pago_semanal as 'pago',
		c.cred_penalidad as 'penalidad',
		c.cred_amortizacion as 'amortizacion',
		CONCAT(suc_banco, ' ',s.suc_cuenta_tipo) as 'banco_tipo',
		s.suc_cuenta_numero as 'cuenta_numero',
		s.suc_direccion as 'sucursal_direccion',
		s.suc_telefono as 'sucursal_telefono',
		c.cred_estado as 'estado'
	FROM credito c 
	INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente
	INNER JOIN empleado e ON e.emp_codigo = c.cred_empleado
	INNER JOIN ubigeo u ON u.ubi_codigo = cl.cli_ubigeo
	INNER JOIN tarifa t ON t.tar_codigo = c.cred_tarifa
	INNER JOIN sucursal s ON s.suc_codigo = c.cred_sucursal
	WHERE
		c.cred_codigo = _id;
END $$
DELIMITER ;

-- Procedimiento 78
DROP PROCEDURE IF EXISTS proc_contrato_prenda;
DELIMITER $$
CREATE PROCEDURE proc_contrato_prenda
(IN _id INT)
BEGIN 
	SELECT 
		p.pren_codigo as 'id',
		p.pren_descripcion as 'descripcion',
		ct.cat_nombre as 'categoria',
		tp.tpren_nombre as 'tipo',
		p.pren_serial as 'serial',
		p.pren_marca as 'marca',
		p.pren_modelo as 'modelo',
		p.pren_quilate as 'quilate',
		p.pren_peso as 'peso',
		p.pren_placa as 'placa',
		p.pren_anio as 'anio',
		p.pren_kilometraje as 'kilometraje',
		p.pren_observacion as 'observacion',
		p.pren_comentario as 'comentario',
		p.pren_valoracion as 'valoracion',
		p.pren_estado as 'estado'	
	FROM prenda p
	INNER JOIN credito cd ON cd.cred_prenda = p.pren_codigo
	INNER JOIN categoria ct ON ct.cat_codigo = p.pren_categoria
	INNER JOIN tipo_prenda tp ON tp.tpren_codigo = ct.cat_tipo
	WHERE cd.cred_codigo = _id;
END $$
DELIMITER ;

-- Procedimiento 79
DROP PROCEDURE IF EXISTS proc_nota_credito_lista;
DELIMITER $$
CREATE PROCEDURE proc_nota_credito_lista(IN _id INT)
BEGIN
	SELECT
		n.ncred_codigo as 'id',
		concat(e.emp_nombres, ' ',e.emp_paterno) as 'empleado',
		n.ncred_descripcion as 'descripcion',
		DATE_FORMAT(n.ncred_fecha, "%d/%m/%Y") as 'fecha'
	FROM notas_credito  n 
	INNER JOIN empleado e ON e.emp_codigo = n.ncred_empleado
	WHERE n.ncred_credito = _id
	ORDER BY n.ncred_codigo DESC;
END $$
DELIMITER ; 

-- Proc 80
DROP PROCEDURE IF EXISTS proc_pago_lista;
DELIMITER $$
CREATE PROCEDURE proc_pago_lista
(IN _tipo CHAR(30), IN _buscar CHAR(30), IN _limite INT, IN _sucursal INT)
BEGIN 
	IF _tipo = 'cliente' THEN
		SELECT
			p.pag_codigo as 'id', 
			p.pag_serie as 'serie',
			c.cred_serie as 'credito',
			p.pag_concepto as 'concepto',
			concat(cl.cli_nombres,' ',cl.cli_paterno,' ',cl.cli_materno) as 'cliente',
			DATE_FORMAT(p.pag_fecha, "%d/%m/%Y %H:%i") as 'fecha',
			IF(p.pag_forma_pago = 'TIEN', 'TIENDA', 'DEPOSITO') as 'forma',
			p.pag_total as 'total',
			p.pag_concepto as 'concepto'
		FROM pago p 
		INNER JOIN credito c ON c.cred_codigo = p.pag_credito
		INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente 
		WHERE 
			c.cred_sucursal = _sucursal AND 
			(
				cl.cli_nombres LIKE CONCAT(UPPER(_buscar), '%') OR 
				cl.cli_paterno LIKE CONCAT(UPPER(_buscar), '%') OR 
				cl.cli_materno LIKE CONCAT(UPPER(_buscar), '%')
			)
		ORDER BY p.pag_codigo DESC;

	ELSEIF _tipo = 'credito' THEN
		SELECT
			p.pag_codigo as 'id', 
			p.pag_serie as 'serie',
			c.cred_serie as 'credito',
			p.pag_concepto as 'concepto',
			concat(cl.cli_nombres,' ',cl.cli_paterno,' ',cl.cli_materno) as 'cliente',
			DATE_FORMAT(p.pag_fecha, "%d/%m/%Y %H:%i") as 'fecha',
			IF(p.pag_forma_pago = 'TIEN', 'TIENDA', 'DEPOSITO') as 'forma',
			p.pag_total as 'total',
			p.pag_concepto as 'concepto'
		FROM pago p 
		INNER JOIN credito c ON c.cred_codigo = p.pag_credito
		INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente 
		WHERE 
			c.cred_sucursal = _sucursal AND 
			c.cred_serie LIKE CONCAT(UPPER(_buscar), '%')
		ORDER BY p.pag_codigo DESC;

	ELSEIF _tipo = 'forma' THEN
		SELECT
			p.pag_codigo as 'id', 
			p.pag_serie as 'serie',
			c.cred_serie as 'credito',
			p.pag_concepto as 'concepto',
			concat(cl.cli_nombres,' ',cl.cli_paterno,' ',cl.cli_materno) as 'cliente',
			DATE_FORMAT(p.pag_fecha, "%d/%m/%Y %H:%i") as 'fecha',
			IF(p.pag_forma_pago = 'TIEN', 'TIENDA', 'DEPOSITO') as 'forma',
			p.pag_total as 'total',
			p.pag_concepto as 'concepto'
		FROM pago p 
		INNER JOIN credito c ON c.cred_codigo = p.pag_credito
		INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente 
		WHERE 
			c.cred_sucursal = _sucursal AND 
			p.pag_forma_pago = UPPER(_buscar)
		ORDER BY p.pag_codigo DESC;

	ELSEIF _tipo = 'fecha' THEN
		SELECT
			p.pag_codigo as 'id', 
			p.pag_serie as 'serie',
			c.cred_serie as 'credito',
			p.pag_concepto as 'concepto',
			concat(cl.cli_nombres,' ',cl.cli_paterno,' ',cl.cli_materno) as 'cliente',
			DATE_FORMAT(p.pag_fecha, "%d/%m/%Y %H:%i") as 'fecha',
			IF(p.pag_forma_pago = 'TIEN', 'TIENDA', 'DEPOSITO') as 'forma',
			p.pag_total as 'total',
			p.pag_concepto as 'concepto'
		FROM pago p 
		INNER JOIN credito c ON c.cred_codigo = p.pag_credito
		INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente 
		WHERE 
			c.cred_sucursal = _sucursal AND 
			DATE(p.pag_fecha) = DATE(_buscar)
		ORDER BY p.pag_codigo DESC;
	ELSE 
		SELECT
			p.pag_codigo as 'id', 
			p.pag_serie as 'serie',
			c.cred_serie as 'credito',
			p.pag_concepto as 'concepto',
			concat(cl.cli_nombres,' ',cl.cli_paterno,' ',cl.cli_materno) as 'cliente',
			DATE_FORMAT(p.pag_fecha, "%d/%m/%Y %H:%i") as 'fecha',
			IF(p.pag_forma_pago = 'TIEN', 'TIENDA', 'DEPOSITO') as 'forma',
			p.pag_total as 'total',
			p.pag_concepto as 'concepto'
		FROM pago p 
		INNER JOIN credito c ON c.cred_codigo = p.pag_credito
		INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente 
		WHERE 
			c.cred_sucursal = _sucursal
		ORDER BY p.pag_codigo DESC;
	END IF;
END $$
DELIMITER ;


-- Procedimiento 81
DROP PROCEDURE IF EXISTS proc_pago_rango_fecha;
DELIMITER $$
CREATE PROCEDURE proc_pago_rango_fecha
(IN _inicio DATE, IN _final DATE, IN _limite INT, IN _sucursal INT)
BEGIN
	SELECT
		p.pag_codigo as 'id', 
		p.pag_serie as 'serie',
		c.cred_serie as 'credito',
		concat(cl.cli_nombres,' ',cl.cli_paterno,' ',cl.cli_materno) as 'cliente',
		DATE_FORMAT(p.pag_fecha, "%d/%m/%Y %H:%i") as 'fecha',
		IF(p.pag_forma_pago = 'TIEN', 'TIENDA', 'DEPOSITO') as 'forma',
		p.pag_total as 'total',
		p.pag_concepto as 'concepto'
	FROM pago p 
	INNER JOIN credito c ON c.cred_codigo = p.pag_credito
	INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente 
	WHERE 
		c.cred_sucursal = _sucursal AND 
		(p.pag_fecha BETWEEN _inicio AND _final)
	ORDER BY p.pag_codigo DESC;
END $$
DELIMITER ; 

-- Procedimiento 82
DROP PROCEDURE IF EXISTS proc_info_general;
DELIMITER $$
CREATE PROCEDURE proc_info_general
(IN _fecha DATE, IN _sucursal INT)
BEGIN
	SELECT 
		(SELECT COUNT(*) FROM credito 
			WHERE 
				(cred_estado = 'E' OR cred_estado = 'CC') AND 
				cred_sucursal = _sucursal
		) as 'general_activos',
		(SELECT COUNT(*) FROM credito 
			WHERE 
				cred_estado = 'E' AND
				(select count(*) from cuota where cuo_estado = 'V' AND cuo_credito = cred_codigo) > 0 AND
				cred_sucursal = _sucursal
		) as 'general_vencidos',
		(SELECT COUNT(*) FROM credito 
			WHERE 
				cred_estado = 'E' AND
				(select count(*) from cuota where cuo_vencimiento = _fecha AND cuo_credito = cred_codigo) > 0 AND
				cred_sucursal = _sucursal
		) as 'general_cumple_hoy',
		(SELECT COUNT(*) FROM credito 
			WHERE 
				cred_creacion = _fecha AND
				cred_sucursal = _sucursal
		) as 'movimiento_credito_registro',
		(SELECT SUM(cred_monto) FROM credito 
			WHERE 
				cred_creacion = _fecha AND
				cred_sucursal = _sucursal
		) as 'movimiento_monto_prestado',
		(SELECT SUM(pdet_monto) FROM pago_detalle
			INNER JOIN pago ON pag_codigo = pdet_pago 
			INNER JOIN credito ON cred_codigo = pag_credito 
			WHERE 
				cred_liquidacion = _fecha AND
				date(pag_fecha) = _fecha AND 
				pdet_tipo = 'CAP' AND
				cred_sucursal = _sucursal
		) as 'movimiento_monto_recuperado'
	;
END $$
DELIMITER ; 



-- Procedimiento 83
DROP PROCEDURE IF EXISTS proc_voucher_pago;
DELIMITER $$
CREATE PROCEDURE proc_voucher_pago
(IN _id INT)
BEGIN
	SELECT
		p.pag_codigo as 'id',
		p.pag_serie as 'codigo',
		c.cred_serie as 'credito',
		CONCAT(cl.cli_nombres,' ',cl.cli_paterno,' ',cl.cli_materno) as 'cliente',
		DATE_FORMAT(p.pag_fecha, "%d/%m/%Y %H:%i") as 'fecha',
		p.pag_recibido as 'recibido',
		p.pag_devuelto as 'devuelto',
		p.pag_descuento as 'descuento',
		p.pag_descuento as 'total',
		p.pag_concepto as 'concepto'		
	FROM pago p 
	INNER JOIN credito c ON c.cred_codigo = p.pag_credito
	INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente
	WHERE 
		p.pag_codigo = _id;
END $$
DELIMITER ; 

-- Procedimiento 84
DROP PROCEDURE IF EXISTS proc_voucher_pago_detalle;
DELIMITER $$
CREATE PROCEDURE proc_voucher_pago_detalle
(IN _id INT)
BEGIN
	SELECT
		pd.pdet_codigo as 'id',
		pd.pdet_tipo as 'tipo',
		pd.pdet_referencia as 'referencia',
		(CASE 
		  WHEN pd.pdet_tipo = 'MO' THEN 
		  	(SELECT 
				CONCAT('S/ ',c.cuo_penalidad,' x ',m.mor_dias,' dias')
				FROM mora m INNER JOIN cuota c ON c.cuo_codigo = m.mor_cuota 
				WHERE m.mor_codigo = pd.pdet_id_ref
			)
		  WHEN pd.pdet_tipo = 'CU' THEN 
		  	(SELECT c.cuo_vencimiento FROM cuota c WHERE c.cuo_codigo = pd.pdet_id_ref)
		  WHEN pd.pdet_tipo = 'ADEL' THEN ''
		  WHEN pd.pdet_tipo = 'AMOR' THEN ''
		  ELSE ''
		END) as 'subinfo',
		pd.pdet_monto as 'monto'
	FROM pago_detalle pd 
	WHERE pd.pdet_pago = _id;
END $$
DELIMITER ; 


-- Procedimiento 85
DROP PROCEDURE IF EXISTS proc_credito_moras;
DELIMITER $$
CREATE PROCEDURE proc_credito_moras
(IN _id INT)
BEGIN
	SELECT
		m.mor_codigo as 'id',
		m.mor_credito as 'credito',
		DATE_FORMAT(c.cuo_vencimiento, "%d/%m/%Y") as 'vence',
		m.mor_dias as 'dias',
		m.mor_monto as 'monto',
		m.mor_estado as 'estado',
		c.cuo_orden as 'orden',
		c.cuo_codigo as 'cuota' 
	FROM mora m
	INNER JOIN cuota c ON c.cuo_codigo = m.mor_cuota
	WHERE 
		m.mor_credito = _id 
	ORDER BY m.mor_estado ASC;
END $$
DELIMITER ; 



-- Procedimiento 86
DROP PROCEDURE IF EXISTS proc_prenda_formulario_verifica;
DELIMITER $$
CREATE PROCEDURE proc_prenda_formulario_verifica
( IN _id INT )
BEGIN 
	SELECT 
		p.patr_codigo as 'id',
		p.patr_nombre as 'nombre',
		p.patr_descripcion as 'descripcion',
		IF(
			(SELECT COUNT(*) FROM prenda_formulario 
				WHERE pform_tipo = _id AND p.patr_codigo = pform_atributo) > 0,
			"SI", "NO") AS 'estado' 
		FROM prenda_atributos p 
		ORDER BY p.patr_codigo ASC;
END $$
DELIMITER ;


-- Procedimiento 86
DROP PROCEDURE IF EXISTS proc_prenda_formulario;
DELIMITER $$
CREATE PROCEDURE proc_prenda_formulario
( IN _id INT )
BEGIN 
	SELECT 
		pf.pform_codigo as 'id',
		p.patr_nombre as 'nombre'
	FROM prenda_formulario pf
	INNER JOIN prenda_atributos p ON p.patr_codigo = pf.pform_atributo
	WHERE pf.pform_tipo = _id
	ORDER BY pf.pform_codigo ASC;
END $$
DELIMITER ;

-- -- Procedimiento 86
-- DROP PROCEDURE IF EXISTS proc_mora_detalle;
-- DELIMITER $$
-- CREATE PROCEDURE proc_mora_detalle
-- (IN _id INT)
-- BEGIN
-- 	SELECT
-- 		m.mor_codigo as 'id',
-- 		m.mor_credito as 'credito',
-- 		DATE_FORMAT(c.cuo_vencimiento, "%d/%m/%Y") as 'vence',
-- 		m.mor_dias as 'dias',
-- 		m.mor_monto as 'monto',
-- 		m.mor_estado as 'estado',
-- 		c.cuo_orden as 'orden',
-- 		c.cuo_codigo as 'cuota' 
-- 	FROM mora m
-- 	INNER JOIN cuota c ON c.cuo_codigo = m.mor_cuota
-- 	WHERE 
-- 		m.mor_codigo = _id;
-- END $$
-- DELIMITER ; 





-- R E V I S I O N
-- Procedimiento 01
DROP PROCEDURE IF EXISTS proc_revision_generacion_cuota;
DELIMITER $$
CREATE PROCEDURE proc_revision_generacion_cuota
(
	IN _id INT, IN _credito INT, IN _vence DATE, 
	IN _monto DECIMAL(8,2), IN _penalidad DECIMAL(8,2), 
	IN _prorroga INT, IN _orden INT, IN _fecha_actual DATE,
	IN _sucursal INT, IN _empleado INT
)
BEGIN 

	DECLARE __respuesta CHAR(40);
	DECLARE __cont_next_cuota INT;
	DECLARE __next_vence DATE;
	DECLARE __next_inicio_mora DATE;
	DECLARE __adelanto_disponible DECIMAL(8,2);
	DECLARE __cuota_id INT;
	DECLARE __sucursal_serie CHAR(15);
	DECLARE __pago_orden INT;
	DECLARE __pago_serie CHAR(20);
	DECLARE __pago_id INT;
	DECLARE `_rollback` BOOL DEFAULT 0;
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET `_rollback` = 1;

	START TRANSACTION;
		SET __next_vence = ( DATE_ADD( _vence, INTERVAL 7 DAY) ); 
		SET __cont_next_cuota = (SELECT COUNT(*) FROM cuota 
			WHERE cuo_vencimiento = __next_vence AND cuo_credito = _credito AND cuo_orden = (_orden+1));

		IF __cont_next_cuota = 0 THEN 
			SET __next_inicio_mora = (DATE_ADD( __next_vence, INTERVAL _prorroga DAY));
			IF (SELECT WEEKDAY(__next_inicio_mora)) = '6' THEN
				SET __next_inicio_mora = (DATE_ADD(__next_inicio_mora, INTERVAL 1 DAY));
			END IF;

			INSERT INTO cuota 
			(`cuo_credito`,`cuo_orden`, `cuo_monto`, `cuo_penalidad`, `cuo_vencimiento`, `cuo_inicio_mora`, `cuo_estado`)
			VALUES(_credito, (_orden+1), _monto, _penalidad, __next_vence, __next_inicio_mora, 'E');

			SET __cuota_id = (SELECT LAST_INSERT_ID());
			SET __adelanto_disponible = (SELECT cred_adelanto FROM credito WHERE cred_codigo = _credito);

			IF __adelanto_disponible >= _monto THEN
				SET __sucursal_serie = (SELECT IF(suc_pago_serie, suc_pago_serie, 'PS-') FROM sucursal WHERE suc_codigo = _sucursal);
				SET __pago_orden = (SELECT IF(MAX(pag_orden), MAX(pag_orden), 1) FROM pago INNER JOIN credito ON cred_codigo = pag_credito WHERE 
					pag_nomen = __sucursal_serie AND cred_sucursal = _sucursal);
				SET __pago_serie = CONCAT(__sucursal_serie, LPAD(__pago_orden,7,'0'));


				INSERT INTO pago(pag_credito,pag_serie,pag_orden,pag_nomen,pag_fecha,pag_forma_pago,pag_subtotal,pag_total,
					pag_recibido,pag_concepto,pag_empleado)
				VALUES(_credito,__pago_serie,__pago_orden,__sucursal_serie,_fecha_actual,'REV',_monto,_monto,_monto,'ADEL_AUTO',
					_empleado);

				SET __pago_id = (SELECT LAST_INSERT_ID());

				INSERT INTO pago_detalle(pdet_pago,pdet_tipo,pdet_referencia,pdet_id_ref,pdet_monto)
				VALUES(__pago_id,'CU',CONCAT('Cuota ',(_orden+1),': Adelanto'), __cuota_id, _monto);

				INSERT INTO adelanto_salida(sal_credito, sal_tipo, sal_modo, sal_detalle, sal_monto, sal_id_ref, sal_fecha)
				VALUES(_credito, 'CU', 'AUTO', CONCAT('Cuota ',(_orden+1), ': Adelanto aplicado'), _monto, __pago_id, _fecha_actual);

				UPDATE credito SET cred_adelanto = cred_adelanto - _monto WHERE cred_codigo = _credito; 
				UPDATE cuota SET cuo_estado = 'P' WHERE cuo_codigo = __cuota_id;
			END IF;
			SET __respuesta = ('200');
		ELSE 
			SET __respuesta = ('100');
		END IF;

	IF `_rollback` THEN
        ROLLBACK;
        SELECT 
        	"Error" as "codigo",
        	"400" as "resultado",
        	"Proceso incompleto." AS "mensaje";
    ELSE
        COMMIT;
        SELECT 
        	'Ok' as 'codigo',
        	__respuesta AS 'resultado', 
        	"revision ejecutada." as 'mensaje';
    END IF;

END $$
DELIMITER ;



DROP PROCEDURE IF EXISTS proc_revision_cuota_calculo;
DELIMITER $$
CREATE PROCEDURE proc_revision_cuota_calculo
(IN _id INT, IN _credito INT, IN _vence DATE, IN _inicio_mora DATE, IN _monto DECIMAL(8,2), 
 IN _penalidad DECIMAL(8,2), IN _estado CHAR(10), IN _evalua CHAR(10), IN _FechaActual DATE,
 IN _sucursal INT, IN _empleado INT
)
BEGIN 
	DECLARE __upd_estado CHAR(10);
	DECLARE __upd_prorrateo DECIMAL(8,2);
	DECLARE __upd_atraso INT;
	DECLARE __upd_mora DECIMAL(8,2);
	DECLARE __ver_mora INT;
	DECLARE __ver_prorroga INT;
	DECLARE __pror_dias INT UNSIGNED;
	DECLARE __amortizacion_actual DECIMAL(8,2);
	DECLARE _aplica_penalidad CHAR(10);
	DECLARE __pago_id INT;
	DECLARE __sucursal_serie CHAR(15);
	DECLARE __pago_serie CHAR(20);
	DECLARE __pago_orden INT;
	DECLARE __cuota_orden INT;

	DECLARE `_rollback` BOOL DEFAULT 0;
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET `_rollback` = 1;

	START TRANSACTION;
	IF _vence >= _FechaActual THEN 
		SET __upd_estado = 'E';
		SET __upd_prorrateo = (
			CASE
    			WHEN ( DATEDIFF(_vence, _FechaActual) < 7) 
    				THEN (_monto * DATEDIFF(_vence, _FechaActual) * 0.15)
    			WHEN ( DATEDIFF(_vence, _FechaActual) = 7) 
    				THEN _monto
    			ELSE 0 
    		END );
		SET __upd_atraso = 0;
		SET __upd_mora = 0;
	ELSE 
		SET __upd_estado = 'V';
		SET __upd_prorrateo = 0;
		SET __amortizacion_actual = (SELECT cred_amortizacion FROM credito WHERE cred_codigo = _credito);

		IF __amortizacion_actual >= _monto THEN
			-- Generar serie del pago
			SET __sucursal_serie = (SELECT IF(suc_pago_serie, suc_pago_serie, 'PS-') FROM sucursal WHERE suc_codigo = _sucursal);
			SET __pago_orden = (SELECT IF(MAX(pag_orden), MAX(pag_orden), 1) FROM pago INNER JOIN credito ON cred_codigo = pag_credito WHERE 
				pag_nomen = __sucursal_serie AND cred_sucursal = _sucursal);
			SET __pago_serie = CONCAT(__sucursal_serie, LPAD(__pago_orden,7,'0'));


			-- Registrar pago
			INSERT INTO pago(pag_credito,pag_serie,pag_orden,pag_nomen,pag_fecha,pag_forma_pago,pag_subtotal,pag_total,pag_recibido,
				pag_concepto,pag_empleado)
			VALUES(_credito,__pago_serie,__pago_orden,__sucursal_serie,_FechaActual,'REV',_monto,_monto,_monto,'DEB_AUTO',
				_empleado);
			
			SET __pago_id = (SELECT LAST_INSERT_ID());
			SET __cuota_orden = (SELECT cuo_orden FROM cuota WHERE cuo_codigo = _id);

			INSERT INTO pago_detalle(pdet_pago, pdet_tipo, pdet_referencia, pdet_id_ref, pdet_monto)
			VALUES(__pago_id, 'CU', CONCAT('Cuota ',__cuota_orden,': Debitacion'), _id, _monto);

			INSERT INTO debitacion(deb_credito,deb_pago,deb_tipo,deb_detalle,deb_monto,deb_fecha)
			VALUES(
				_credito, __pago_id,'CU', CONCAT('Debitacion automatica: cuota ',__cuota_orden), 
				_monto, _FechaActual
			);

			UPDATE credito SET cred_amortizacion = cred_amortizacion - _monto WHERE cred_codigo = _credito; 
			SET __upd_estado = 'P';	
		ELSE 
			IF  _evalua = "A" AND _inicio_mora <= _FechaActual THEN 
				SET _aplica_penalidad = (SELECT cred_aplica_penalidad FROM credito where cred_codigo = _credito);
				SET __ver_prorroga = (SELECT COUNT(*) FROM prorroga 
					WHERE 
					_FechaActual >= pror_inicio AND 
					_FechaActual < pror_termino AND 
					pror_credito = _credito);

				IF _aplica_penalidad = 'I' OR __ver_prorroga > 0 THEN
					SET __upd_atraso = 0;
					SET __upd_mora = 0;
				ELSE 
					SET __pror_dias = (
					SELECT DATEDIFF(pror_termino, _inicio_mora) FROM prorroga 
						WHERE 
						_inicio_mora >= pror_inicio AND 
						_inicio_mora < pror_termino AND 
						pror_credito = _credito LIMIT 1
					);

					SET __upd_atraso = IF(
						__pror_dias, 
						DATEDIFF(_FechaActual, _inicio_mora) - __pror_dias,
						DATEDIFF(_FechaActual, _inicio_mora)
						);
					SET __upd_mora = ((__upd_atraso) * _penalidad);

					SET __ver_mora = (SELECT mor_codigo FROM mora WHERE 
						mor_credito = _credito AND mor_estado = 'PEN');
					IF __ver_mora THEN 
						UPDATE mora 
						SET 
							mor_cuota = _id,
							mor_dias = __upd_atraso,
							mor_monto = ROUND(__upd_mora, 1)
						WHERE 
							mor_codigo = __ver_mora AND 
							mor_dias <= __upd_atraso;
					ELSE 
						INSERT INTO mora(mor_credito,mor_cuota,mor_dias,mor_monto,mor_prev_estado,mor_estado)
							VALUES(_credito, _id, __upd_atraso, ROUND(__upd_mora,1), 'PEN', 'PEN'); 
					END IF;
				END IF;
			ELSE 
				SET __upd_atraso = 0;
				SET __upd_mora = 0;
			END IF;
		END IF;

	END IF;

	UPDATE cuota 
		SET 
			cuo_dias_atraso = __upd_atraso,
			cuo_mora = ROUND(__upd_mora, 1),
			cuo_prorrateo = ROUND(__upd_prorrateo, 1),
			cuo_estado = __upd_estado
		WHERE 
			cuo_codigo = _id;

	IF `_rollback` THEN
        ROLLBACK;
        SELECT 
        	__pago_serie as "codigo",
        	"Proceso incompleto." AS "mensaje";
    ELSE
        COMMIT;
        SELECT 
        	'Ok' as 'codigo', 
        	"revision ejecutada." as 'mensaje';
    END IF;

END $$
DELIMITER ;


DROP PROCEDURE IF EXISTS proc_reporte_diario;
DELIMITER $$
CREATE PROCEDURE proc_reporte_diario
(IN _credito INT, IN _FA DATE)
BEGIN
	SELECT
		c.cred_serie as 'credito',
		c.cred_codigo as 'credito_id',
		c.cred_monto as 'prestamo',
		c.cred_pago_semanal as 'interes',
		concat(cl.cli_nombres,' ',cl.cli_paterno,' ',cl.cli_materno) as 'cliente',
		cu.cuo_estado as 'estado',
		cu.cuo_orden as 'orden',
		DATE_FORMAT(cu.cuo_vencimiento, "%d/%m/%Y") as 'vencimiento',
		cu.cuo_penalidad as 'penalidad',
		cu.cuo_mora as 'mora',
		cu.cuo_dias_atraso as 'dias',
		IF(cu.cuo_vencimiento > _FA, cu.cuo_monto, cu.cuo_prorrateo) as 'monto',
		IF(cu.cuo_vencimiento > _FA, cu.cuo_prorrateo, 0) as 'prorrateo',
		(
			CASE
			    WHEN c.cred_estado = 'CC' THEN 'congelado'
			    WHEN cu.cuo_vencimiento > _FA THEN 'activo'
			    WHEN cu.cuo_vencimiento < _FA THEN 'vencido'
			    WHEN cu.cuo_vencimiento = _FA THEN 'cumplido'
		    	ELSE ""
	    	END ) as 'css'
	FROM cuota cu 
	INNER JOIN credito c ON c.cred_codigo = cu.cuo_credito
	INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente 
	WHERE 
		(c.cred_estado = 'E' OR c.cred_estado = 'CC') AND 
		(cu.cuo_estado = 'E' OR cu.cuo_estado = 'CC' OR cu.cuo_estado = 'V') AND 
		c.cred_codigo = _credito
	ORDER BY c.cred_serie ASC, cu.cuo_vencimiento ASC;
END $$
DELIMITER ; 





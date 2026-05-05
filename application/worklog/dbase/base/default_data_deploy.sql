-- TARIFAS DATA 
INSERT INTO tarifa (`tar_minimo`, `tar_maximo`, `tar_tipo`, `tar_interes`, `tar_mora`) VALUES
('1.00', '50.00', 'F', '6.00', '0.86'),
('51.00', '100.00', 'F', '9.50', '1.00'),
('101.00', '150.00', 'F', '12.50', '1.00'),
('151.00', '200.00', 'P', '8.50', '0.61'),
('201.00', '300.00', 'P', '7.50', '0.54'),
('301.00', '500.00', 'P', '7.00', '0.50'),
('501.00', '1000.00', 'P', '6.50', '0.46'),
('1001.00', '99999.00', 'P', '6.00', '0.43');


-- TIPO_DOCUMENTO DATA
INSERT INTO tipo_documento (`tdoc_codigo`, `tdoc_nombre`, `tdoc_descripcion`, `tdoc_max_caracteres`, `tdoc_estado`) VALUES
(1, 'DNI', 'DOCUMENTO NACIONAL DE IDENTIFICACION', 8, 'A'),
(2, 'LIBRETA ELEC.', 'LIBRETA ELECTORAL', 8, 'A'),
(3, 'CARNET EXT.', 'CARNET DE EXTRANJERIA', 12, 'A');

-- ROLES 
INSERT INTO roles (`rol_codigo`, `rol_titulo`, `rol_descripcion`) VALUES
(1, 'Administrador', ''),
(2, 'Empleado', '');	

-- SUCURSAL DATA 
INSERT INTO sucursal (`suc_codigo`, `suc_ubigeo`, `suc_direccion`, `suc_descripcion`, `suc_telefono`, `suc_estado`) VALUES
(1, 21801, 'Plaza de Armas', 'SEDE PRINCIPAL', '924587847', 'A'); 

-- EMPLEADO DATA 
INSERT INTO empleado 
(`emp_sucursal`, `emp_documento`, `emp_tipo_documento`, `emp_nombres`, `emp_paterno`, `emp_materno`, `emp_ubigeo`, `emp_direccion`, `emp_telefono`, `emp_email`, `emp_imagen`, `emp_estado`) 
VALUES (1, '43451421', 'DNI', 'FREDDY', 'ROJAS', 'VIGO', 21801, 'Centro de la ciudad', '928005868', 'rojasvega@gmail.com', '','A');


-- USUARIO DATA 
INSERT INTO usuario (`usu_empleado`, `usu_rol`, `usu_usuario`, `usu_clave`, `usu_estado`) VALUES
(1, 1, 'soda', '12345', 'A');

INSERT INTO permiso (`perm_nombre`, `perm_titulo`, `perm_descripcion`, `perm_estado`) 
VALUES
('empleado_registrar', 'Empleado Registrar', 'Permite registrar nuevos empleados.', 'A'),
('empleado_editar', 'Empleado Editar', 'Permite editar los datos del los empleados.', 'A'),
('empleado_eliminar', 'Empleado Eliminar', 'Permite eliminar empleados registrados.', 'A'),
('cliente_registrar', 'Cliente Registrar', 'Permite registrar nuevos clientes.','A'),
('cliente_editar', 'Cliente Editar', 'Permite editar los datos del los clientes.', 'A'),
('cliente_eliminar', 'Cliente Eliminar', 'Permite eliminar clientes registrados.', 'A'),
('credito_registrar', 'Credito Registrar', 'Permite registrar nuevos creditos.','A'),
('credito_editar', 'Credito Editar', 'Permite editar datos de los creditos.', 'A'),
('credito_liquidar', 'Credito Liquidar', 'Permite dar por finalizado un credito.', 'A'),
('usuario_editar', 'Usuario Editar', 'Permite editar datos de los usuarios.', 'A'),
('usuario_reiniciar', 'Usuario Reiniciar', 'Permite reiniciar los datos del usuario a los valores iniciales.', 'A'),
('categoria_registrar', 'Categoria Registrar', 'Permite registrar nuevas categorias.', 'A'),
('categoria_editar', 'Categoria Editar', 'Permite editar los datos del las categorias.', 'A'),
('categoria_eliminar', 'Categoria Eliminar', 'Permite eliminar categorias registradas.', 'A'),
('prenda_registrar', 'Prenda Registrar', 'Permite registrar nuevas prendas.', 'A'),
('prenda_editar', 'Prenda Editar', 'Permite editar los datos de las prendas.', 'A'),
('prenda_transferir', 'Prenda Transferir', 'Permite transferir la prenda de un cliente a otro.', 'A'),
('prenda_eliminar', 'Prenda Eliminar', 'Permite eliminar prendas registradas.', 'A'),
('pago_registrar', 'Pago Registrar', 'Permite registrar pagos.', 'A'),
('pago_anular', 'Pago Anular', 'Permite anular pagos registrados.', 'A'),
('sucursal_registrar', 'Registrar Sucursal', 'Permite registrar nuevas sucursales.', 'A'),
('sucursal_editar', 'Editar Sucursal', 'Permite editar los datos de una sucursal.', 'A'),
('sucursal_eliminar', 'Eliminar Sucursal', 'Permite eliminar los registros de sucursales.', 'A');




-- MODULOS DATA 
INSERT INTO modulo (`mod_titulo`, `mod_descripcion`, `mod_ruta`, `mod_icono`, `mod_estado`) VALUES
('Creditos', 'Modulo para la creacion y seguimiento de creditos', 'Credito', 'fe fe-file', 'A'),
('Clientes', 'Modulo para la administracion de los clientes', 'Cliente', 'fe fe-users', 'A'),
('Prendas', 'Modulo para la administracion de las prendas entregadas', 'Prenda', 'fe fe-tv', 'A'),
('Pagos', 'Modulo para la administracion de los pagos realizados', 'Pago', 'fe fe-dollar-sign', 'A'),
('Empleados', 'Modulo para la administracion de los empleados', 'Empleado', 'fe fe-user', 'A'),
('Usuarios', 'Modulo para la administracion de los usuarios', 'Usuario', 'fe fe-user', 'A'),
('Sucursal', 'Modulo para la administracion de las sucursales', 'Sucursal', 'fe fe-compass', 'A'),
('Categoria', 'Modulo para la administracion de las categorias de prenda', 'Categoria', 'fe fe-flag', 'A'),
('Tarifas', 'Modulo para la administracion de las tarifas aplicadas', 'Tarifa', 'fe fe-hash', 'A');


-- CONFIGURACION POR DEFECTO
INSERT INTO `configuracion` (`conf_codigo`, `conf_nombre`, `conf_descripcion`, `conf_valor`, `conf_estado`) VALUES
(1, 'LOGIN_FONDO', '', 'uploads/app/login_background.jpg', 'A');
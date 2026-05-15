<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'Inicio';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;


// Admin
$route['administrador/usuarios'] = 'admin/users/edgerunners';




// Inicio
$route["api/v1/inicio/creditos-activos"]['GET'] = "api/Inicio/get_creditos_activos";
$route['api/v1/inicio/resumen-dia']['POST'] = "api/Inicio/get_resumen_dia";


// NotasCredito
$route['api/v1/credito-notas/getbycredito']['POST'] = 'NotaCredito/get_by_credito';
$route['api/v1/credito-notas/getdata']['POST'] = 'NotaCredito/get_data';
$route['api/v1/credito-notas/save']['POST'] = 'NotaCredito/save_data';
$route['api/v1/credito-notas/delete']['POST'] = 'NotaCredito/delete_data';
$route['api/v1/credito-notas/update']['POST'] = 'NotaCredito/update_data';

// NotasCliente
$route['api/v1/cliente-notas/getbycliente']['POST'] = 'NotaCliente/get_by_cliente';
$route['api/v1/cliente-notas/getdata']['POST'] = 'NotaCliente/get_data';
$route['api/v1/cliente-notas/save']['POST'] = 'NotaCliente/save_data';
$route['api/v1/cliente-notas/update']['POST'] = 'NotaCliente/update_data';
$route['api/v1/cliente-notas/delete']['POST'] = 'NotaCliente/delete_data';

// Cuota
$route['api/v1/cuota/getbycredito']['POST'] = 'Cuota_api/get_by_credito';


// Pago
$route['api/v1/pago/getbycredito']['POST'] = 'Pago_api/get_by_credito';


// Credito
$route['api/v1/credito/save']['POST'] = 'CreditoAPI/save_data';
$route['api/v1/credito/delete']['POST'] = 'CreditoAPI/delete_sks';
$route['api/v1/credito/set-pause']['POST'] = 'CreditoAPI/setPause';
$route['api/v1/credito/setremate']['POST'] = 'CreditoAPI/setRemate';
$route['api/v1/credito/getbycliente']['POST'] = 'CreditoAPI/get_by_cliente';
$route['api/v1/credito/getall']['POST'] = 'CreditoAPI/get_data_list';

$route['api/v1/credito/get-all']['POST'] = 'CreditoAPI/get_all';



// Prenda
$route['api/v1/prenda/getbycredito']['POST'] = 'Prenda_api/get_by_credito';
$route['api/v1/prenda/getbycliente']['POST'] = 'Prenda_api/get_by_cliente';
$route['api/v1/prenda/getdata']['POST'] = 'Prenda_api/get_data';
$route['api/v1/prenda/save']['POST'] = 'Prenda_api/save_data';
$route['api/v1/prenda/update']['POST'] = 'Prenda_api/update_data';
$route['api/v1/prenda/delete']['POST'] = 'Prenda_api/delete_data';
$route['api/v1/prenda/get_form_by_tipo']['POST'] = 'Prenda_api/get_form_by_tipo';
$route['api/v1/prenda/get_form_edit']['POST'] = 'Prenda_api/get_form_edit';
$route['api/v2/prenda/getAll']['POST'] = 'api/PrendaAPI/get_all';
$route['api/v1/prenda/getAllMarcas']['POST'] = 'api/PrendaAPI/get_all_marcas';
$route['api/v1/prenda/getAllQuilates']['POST'] = 'api/PrendaAPI/get_all_quilates';


// Cliente
$route['api/v1/cliente/getall']['POST'] = 'ClienteAPI/get_all';
$route['api/v1/cliente/getdata']['POST'] = 'ClienteAPI/get_data';
$route['api/v1/cliente/save']['POST'] = 'ClienteAPI/save_data';
$route['api/v1/cliente/update']['POST'] = 'ClienteAPI/update_data';
$route['api/v1/cliente/delete']['POST'] = 'ClienteAPI/delete_data';
$route['api/v1/cliente/sendEmail']['POST'] = 'ClienteAPI/send_email';


// Conducta
$route['api/v1/conducta/getbycliente']['POST'] = 'ClienteAPI/conducta_get_data';
$route['api/v1/conducta/save']['POST'] = 'ClienteAPI/conducta_update_data';


// Tipo Prenda
$route['api/v1/tipoprenda/getall'] = 'TipoPrenda/get_all';
// $route['api/v1/tipoprenda/form_edit_data'] = 'TipoPrenda/get_form_edit_data';


// Categoria
$route['api/v1/categoria/getbytipo']['POST'] = 'Categoria/getByTipo';





// Reporte
$route['api-reporte/procesado/v1'] = 'Reporte/service_reporte_procesado_v2';




// PublicAPI
$route['api/v1/public/prenda-getall']['POST'] = 'PublicAPI/prenda__get_all';
$route['api/v1/public/prenda-getdata']['POST'] = 'PublicAPI/prenda__get_data';
$route['api/v1/public/tipoprenda-getall']['GET'] = 'PublicAPI/tipo_prenda__get_all';

$route['api/v1/public/sucursal-getall']['POST'] = 'PublicAPI/sucursal__get_all';





// NEW - 2024

// Sesion
$route['autenticacion']['GET'] = 'Sesion/login_form';
$route['Acceso']['GET'] = 'Sesion/login_form';
$route['api/v1/user/logout']['POST'] = 'Sesion/desconectar';
$route['api/v1/session/sede/change']['POST'] = 'Sesion/service_establecer_sede';

// 2026
$route['api/v1/user/auth']['POST'] = 'Sesion/autenticar';
$route['api/v1/auth/login']['POST'] = 'api/Auth/login';
$route['api/v1/auth/logout']['POST'] = 'api/Auth/logout';
$route['/logout']['GET'] = 'api/Auth/logout';



// Revision
$route['revision']['GET'] = 'Sesion/revision_screen';
$route['api/v1/revision/global']['POST'] = 'App/revision_completa';
// $route['api/v1/revision/unitary']['POST'] = 'revision/revision_unitaria';
// $route['api/v1/revision/sede']['POST'] = 'revision/revision_sede';
// $route['api/v1/revision/complete']['POST'] = 'revision/revision_completa';


// System Config
$route['config/system']['GET'] = 'SystemConfig/create_update';
$route['api/v1/config-system/get']['POST'] = 'SystemConfig/get_config';
$route['api/v1/config-system/update']['POST'] = 'SystemConfig/api_update';

// Preferencias
$route['usuario/preferencias']['GET'] = 'Preferencias/create_update_form';
$route['api/v1/preferencias/get-by-user']['POST'] = 'Preferencias/api_get_by_user';
$route['api/v1/preferencias/create_update']['POST'] = 'Preferencias/api_create_update';


// Login Views
$route['api/v1/login-view/get-all']['POST'] = 'LoginView/get_all';
// $route['api/v1/config-sistema/update']['POST'] = 'ConfigSystem/update';


// Sucursal
$route['api/v1/sucursal/get-available-user']['POST'] = 'Sucursal/get_available_for_user';
$route['api/v1/sucursal/save']['POST'] = 'Sucursal/registrar';
$route['api/v1/sucursal/public/getall']['POST'] = 'Sucursal/get_for_public';
// $route['api/v1/sucursal/delete']['POST'] = 'ClienteAPI/delete_data';



// Profile
$route['perfil']['GET'] = 'Sesion/profile_view';
$route['api/v1/profile/update']['POST'] = 'api/Sesion/profile_update';



// EMPLEADO API
$route['api/v1/empleados']['GET'] = 'api/EmpleadoAPI/read_all';
$route['api/v1/empleados/(:num)']['GET'] = 'api/EmpleadoAPI/read/$1';
$route['api/v1/empleados/crear']['POST'] = 'api/EmpleadoAPI/create';
$route['api/v1/empleados/editar']['POST'] = 'api/EmpleadoAPI/update';
$route['api/v1/empleados/editar-estado']['POST'] = 'api/EmpleadoAPI/updateStatus';
$route['api/v1/empleados/remover']['POST'] = 'api/EmpleadoAPI/delete';


// UBIGEO API 
$route['api/v1/ubigeo']['GET'] = 'api/UbigeoAPI/read_all';


// SEDE API 
$route['api/v1/sedes']['GET'] = 'api/SedeAPI/bs_read_all';


// PAGO API
// $route['api/v1/pagos']['GET'] = 'api/EmpleadoAPI/read_all';
// $route['api/v1/pagos/(:num)']['GET'] = 'api/EmpleadoAPI/read/$1';
// $route['api/v1/pagos/crear']['POST'] = 'api/EmpleadoAPI/create';
// $route['api/v1/pagos/editar']['POST'] = 'api/EmpleadoAPI/update';
// $route['api/v1/pagos/editar-estado']['POST'] = 'api/EmpleadoAPI/updateStatus';
// $route['api/v1/pagos/remover']['POST'] = 'api/EmpleadoAPI/delete';
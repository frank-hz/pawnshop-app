<!doctype html>
<html lang="es" dir="ltr">
  <head>
	<meta charset="UTF-8">
	<meta http-equiv=”Content-Type” content=”text/html; charset=UTF-8″ />
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta content="width=device-width, initial-scale=1" name="viewport" />
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<meta http-equiv="Content-Language" content="en" />
	<meta name="msapplication-TileColor" content="#2d89ef">
	<meta name="theme-color" content="#4188c9">
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="HandheldFriendly" content="True">
	<meta name="MobileOptimized" content="320">
	<link rel="shortcut icon" type="image/png" href="<?= base_url() ?>assets/images/electrocash_favicon.png" />
	<title>Electrocash</title>
	<link rel="stylesheet" href="<?= base_url() ?>assets/fonts/fontawesome/css/all.css">
	<link rel="stylesheet" href="<?= base_url() ?>assets/fonts/Be Vietnam/be_vietnam.css">
	<link rel="stylesheet" href="<?= base_url() ?>assets/fonts/Inter/inter.css">
	<link rel="stylesheet" href="<?= base_url() ?>assets/fonts/Poppins/poppins.css">
	<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/sweetalert2/sweetalert2.min.css">
	<link href="<?= base_url() ?>assets/css/dashboard.css?v=2" rel="stylesheet">
	<link href="<?= base_url() ?>assets/css/all.condensed.css" rel="stylesheet">
	<link href="<?= base_url() ?>assets/css/soda.css" rel="stylesheet">
	<link href="<?= base_url() ?>assets/css/encode-design.css?v=020425" rel="stylesheet">
	<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/iziToast/iziToast.min.css">
	<link href="<?= base_url() ?>global-code/css/vaporwave.css" rel="stylesheet">
	

	<script src="<?= base_url() ?>assets/js/vendors/jquery-3.2.1.min.js"></script>
	<script src="<?= base_url() ?>assets/js/vendors/bootstrap.bundle.min.js"></script>
	<script src="<?= base_url() ?>assets/plugins/iziToast/iziToast.min.js"></script>
  	<script src="<?= base_url() ?>assets/plugins/sweetalert2/sweetalert2.min.js"></script>
	<script src="<?= base_url() ?>assets/js/retrocash.js"></script>		
	<link href="<?= base_url() ?>assets/css/data-module.css" rel="stylesheet">
	<script src="<?= base_url() ?>global-code/js/vaporwave.js"></script>
	<!-- <script src="<?= base_url() ?>global-code/js/coffee.js"></script> -->
  </head>
  <body style="">
    <div class="page">
      <div id="dashboard-app" class="page-main">
	  	<div class="header-spring">
			<div class="logo-group">
				<a class="logo-spring" href="<?= base_url() ?>">
					<img src="<?= base_url() ?>assets/images/electrocash_logo_nuevo.png" class="header-brand-img" alt="tabler logo">
				</a>
				<div class="logo-sub-text">
					<?php echo current_date_display(); ?> <span id="testing-tag" class="tag tag-success d-none">Testing</span>
				</div>
			</div>
			<!-- d-flex order-lg-2 ml-auto -->
			<div class="date-group">
				<div class="d-flex">
					<?php if ($this->session->userdata('pres_ses_rol')==='Administrador') : ?>
						<button id="dash-btn-define-sede" class="btn btn-indigo">
							<?php echo $this->session->userdata('pres_ses_sede'); ?>
							<i class="fe fe-map-pin"></i>
						</button>
					<?php else : ?>
						<button class="btn btn-secondary" onclick="javascript:void(0)">
							<?php echo $this->session->userdata('pres_ses_sede'); ?>
							<i class="fe fe-map-pin"></i>
						</button>
					<?php endif; ?>						
				</div>
				<div class="dropdown d-none d-md-flex">
					<a class="nav-link icon" data-toggle="dropdown">
						<i class="fe fe-bell"></i>
						<span class="nav-unread"></span>
					</a>
					<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
						<a href="#" class="dropdown-item d-flex">
							<div>
								<strong>Frank</strong> Sin notificaciones.
								<div class="small text-muted">Hace 10 minutos</div>
							</div>
						</a>
						<div class="dropdown-divider"></div>
						<a href="#" class="dropdown-item text-center text-muted-dark">Marcar todo como leido.</a>
					</div>
				</div>
				<div class="dropdown">
					<a href="#" class="nav-link pr-0 leading-none user-link" data-toggle="dropdown">
						<?php if ($this->session->userdata('pres_ses_sesion')): ?>
							<?php if (strlen($this->session->userdata('pres_ses_imagen')) > 0): ?>
								<img src="<?= base_url().$this->session->userdata('pres_ses_imagen'); ?>" class="avatar" alt="">
							<?php else: ?>
								<span class="avatar text-blue">
									<?php echo $this->session->userdata('pres_ses_acronimo'); ?>
								</span>
							<?php endif ?>
							<span class="ml-2 d-none d-lg-block">
								<span class="text-default">
									<?php echo $this->session->userdata('pres_ses_nombre'); ?>
								</span>
								<small class="text-muted d-block mt-1">
									<?php echo $this->session->userdata('pres_ses_rol'); ?>
								</small>
							</span>
						<?php else: ?>
							<img src="<?= base_url() ?>uploads/default/usuario.jpg" class="avatar" alt="">
							<span class="ml-2 d-none d-lg-block">
								<span class="text-default">Sesion no iniciada</span>
								<small class="text-muted d-block mt-1">Desconocido</small>
							</span>
						<?php endif ?>
					</a>
					<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
						<a class="dropdown-item" href="<?= base_url() ?>perfil">
							<i class="dropdown-icon fe fe-user"></i> Perfil
						</a>
						<?php if (in_array($this->session->userdata('pres_ses_rol'),
						['DEV','ADMIN','Administrador'])): ?>
						<a class="dropdown-item" href="<?= base_url() ?>usuario/preferencias">							
							<i class="dropdown-icon fe fe-layout"></i>  
							Preferencias
						</a>
						<a class="dropdown-item" href="<?= base_url() ?>config/system">
							<i class="dropdown-icon fe fe-settings"></i> 
							Config Sistema
						</a>
						<?php endif ?>
						<a class="dropdown-item" href="#">
						<i class="dropdown-icon fe fe-send"></i> Mensaje
						</a>
						<div class="dropdown-divider"></div>
						<a id="sesionLink" class="dropdown-item" href="/">
						<i class="dropdown-icon fe fe-log-out"></i> Salir
						</a>
					</div>
				</div>
			</div>
		</div>

		<div class="sub-header-spring">
			<div id="toggle-btn" class="toggle-check">
				<svg class="toggle-cross" viewBox="0 0 15 15" version="1.1" xmlns="http://www.w3.org/2000/svg">
				<path d="M2.64,1.27L7.5,6.13l4.84-4.84C12.5114,1.1076,12.7497,1.0029,13,1c0.5523,0,1,0.4477,1,1&#xA;&#x9;c0.0047,0.2478-0.093,0.4866-0.27,0.66L8.84,7.5l4.89,4.89c0.1648,0.1612,0.2615,0.3796,0.27,0.61c0,0.5523-0.4477,1-1,1&#xA;&#x9;c-0.2577,0.0107-0.508-0.0873-0.69-0.27L7.5,8.87l-4.85,4.85C2.4793,13.8963,2.2453,13.9971,2,14c-0.5523,0-1-0.4477-1-1&#xA;&#x9;c-0.0047-0.2478,0.093-0.4866,0.27-0.66L6.16,7.5L1.27,2.61C1.1052,2.4488,1.0085,2.2304,1,2c0-0.5523,0.4477-1,1-1&#xA;&#x9;C2.2404,1.0029,2.4701,1.0998,2.64,1.27z"/>
				</svg>
				<svg class="toggle-bars" viewBox="-32 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M16 132h416c8.837 0 16-7.163 16-16V76c0-8.837-7.163-16-16-16H16C7.163 60 0 67.163 0 76v40c0 8.837 7.163 16 16 16zm0 160h416c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H16c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16zm0 160h416c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H16c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16z"/></svg>
			</div>
			<ul id="dashboard-menu" class="menu-spring">
				<li>
					<a href="<?= base_url() ?>" name="menu-header-item" data-ref="INICIO" class="menu-spring-item" data-modulo="Inicio">
						<i class="fe fe-home"></i> 
						Inicio
					</a>
				</li>
				<?php if ($this->session->userdata('pres_ses_menu') && !empty($this->session->userdata('pres_ses_menu'))): ?>
					<?php foreach ($this->session->userdata('pres_ses_menu') as $menu): ?>
						<li>
							<a href="<?= base_url().$menu->ruta; ?>" data-modulo="<?= $menu->titulo ?>" data-ref="<?= $menu->url_ref; ?>" class="menu-spring-item" name="menu-header-item">
								<i class="<?= $menu->icono;?>"></i> 
								<?= $menu->titulo ?>
							</a>
						</li>
					<?php endforeach ?> 
				<?php endif ?>
				<?php if ($this->session->userdata('pres_ses_rol') === 'Administrador'): ?>	
					<li>
						<a href="<?= base_url() ?>Analisis" name="menu-header-item" class="menu-spring-item">
							<i class="fe fe-database"></i> 
							Analisis
						</a>
					</li>
				<?php endif ?>
				<li>
					<a href="<?= base_url() ?>Remate" name="menu-header-item" class="menu-spring-item">
						<i class="fe fe-box"></i> 
						Remate
					</a>
				</li>
				<?php if ($this->session->userdata('pres_ses_dev')): ?>	
				<li>
					<a href="<?= base_url() ?>Admin" name="menu-header-item" class="menu-spring-item">
						<i class="fe fe-gitlab"></i> 
						Admin
					</a>
				</li>
				<?php endif ?>
			</ul>
		</div>
        
		<!-- class="my-1 my-md-2" -->
        <div id="principal" class="content-main my-1 p-0 full-content-main mob-p0">
        	<div class="container">
		  		<div id="main_notification" class="trans-slide"></div>
		  	</div>

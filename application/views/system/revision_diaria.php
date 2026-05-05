<!doctype html>
<html lang="es" dir="ltr">
  <head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
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
	<link rel="stylesheet" href="<?= base_url() ?>assets/fonts/Maven Pro/maven_pro.css">
	<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/sweetalert2/sweetalert2.min.css">
	<link href="<?= base_url() ?>assets/css/dashboard.css" rel="stylesheet">
	<link href="<?= base_url() ?>assets/css/retrocash.css" rel="stylesheet">
	<script src="<?= base_url() ?>assets/js/retrocash.js"></script>
  </head>
  <body>
  	<div class="">
		<div class="container">
			<div class="d-flex justify-content-center p-4">
				<a class="header-brand" href="<?= base_url() ?>">
	        		<img src="<?= base_url() ?>assets/images/electrocash_logo_nuevo.png" class="header-brand-img" alt="tabler logo">
	      		</a>
			</div>
			<div class="page-header">
				<h1 class="page-title">
					<i class="fe fe-grid text-blue"></i>
					Revision de Creditos
				</h1>	
			</div>
			<div class="card">
				<div class="card-header bg-blue text-light">
					<h3 class="card-title">
						<i class="fe fe-arrow-right-circle"></i>
						Progreso de la revision
					</h3>
				    <div class="card-options">
				        <!-- <div class="loader text-light"></div> -->
				    </div>
				</div>
				<div class="card-body">
					<div class="progress">
						<div id="revision_progreso" class="progress-bar bg-success" role="progressbar" style="width: 0%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
					</div>
					<span id="revision_log" class="form-label"></span>
				</div>
				<div id="revision_area" class="card-body"></div>
			</div>
		</div>
  	</div>	
  <script src="<?= base_url() ?>js_modules/app.js"></script>
  <script src="<?= base_url() ?>js_modules/system/revision_diaria.js"></script>
  </body>
</html>
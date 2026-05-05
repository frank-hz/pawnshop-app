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
	<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/sweetalert2/sweetalert2.min.css">
	<link href="<?= base_url() ?>assets/css/dashboard.css" rel="stylesheet">
	<!-- <link href="<?= base_url() ?>assets/css/retrocash.css" rel="stylesheet"> -->
    <link href="<?= base_url() ?>assets/css/revision.global.css" rel="stylesheet">
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
			<div class="page-header py-2">
				<h1 class="h3 mb-1">
					<i class="fe fe-grid text-blue"></i>
					Revision de Creditos 
				</h1>	
			</div>
            <div class="py-1">
                <div class="progress mb-1">
                    <div id="_progress" class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <small id="_progress_log">errores encontrados</small>
            </div>
            
			<div class="card">
				<div class="card-body p-5">
                    <div id="_task_verificacion" class="file-task">
                        <div class="task-title">
                            <i class="fas fa-arrow-right"></i>
                            Verificar Cuotas
                        </div>
                        <div class="task-results">
                            <div id="_value_verificacion" class="task-value"></div>
                            <div class="task-icon">
                                <!-- <span class="spinner-icon icon-progress"></span> -->
                                <i class="fas fa-check icon-success"></i>
                                <i class="fas fa-circle-notch icon-pending"></i>
                                <i class="fas fa-spinner icon-progress"></i>
                                <i class="fas fa-times icon-error"></i>
                            </div>
                        </div>
                    </div>
				</div>
                <div class="card-body p-5">
                    <div id="_task_generacion" class="file-task">
                        <div class="task-title">
                            <i class="fas fa-arrow-right"></i>
                            Generar Cuotas
                        </div>
                        <div class="task-results">
                            <div id="_value_generacion" class="task-value"></div>
                            <div class="task-icon">
                                <i class="fas fa-check icon-success"></i>
                                <i class="fas fa-circle-notch icon-pending"></i>
                                <i class="fas fa-spinner icon-progress"></i>
                                <i class="fas fa-times icon-error"></i>
                            </div>
                        </div>
                    </div>
				</div>
                <div class="card-body p-5">
                    <div id="_task_calculo" class="file-task">
                        <div class="task-title">
                            <i class="fas fa-arrow-right"></i>
                            Aplicar Calculos
                        </div>
                        <div class="task-results">
                            <div id="_value_calculo" class="task-value"></div>
                            <div class="task-icon">
                                <i class="fas fa-check icon-success"></i>
                                <i class="fas fa-circle-notch icon-pending"></i>
                                <i class="fas fa-spinner icon-progress"></i>
                                <i class="fas fa-times icon-error"></i>
                            </div>
                        </div>
                    </div>
				</div>
                <div class="card-body p-3">
                    <div id="_final_result" class="final-result text-right">
                        <!-- Revision Completa -->
                    </div>
				</div>


			</div>
		</div>
  	</div>
	
  <script src="<?= base_url() ?>js_modules/app.js"></script>
  <script src="<?= base_url() ?>js_modules/system/revision_global.js"></script>
  </body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Electrocash | Acceso</title>
	<link rel="shortcut icon" type="image/png" href="<?= base_url() ?>assets/images/electrocash_favicon.png" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/fonts/Open Sans/open_sans.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/fonts/Inter/inter.css">
    <link rel="stylesheet" href="<?= base_url() ?>global-code/css/vaporwave.css">
	<link rel="stylesheet" href="<?= base_url() ?>assets/css/login_view_1.css">
    <!-- <script src="<?= base_url() ?>js_modules/main.js"></script> -->
</head>
<body class="dark">	
    <div id="float-loading-elm" class="float-loading corner-right">
        <span></span>
        &nbsp;Redireccionando...
    </div>
    <form id="_LOGIN_FORM" class="login" autocomplete="off">
        <div class="login-header">
            <div style="display:flex;justify-content:center;margin-bottom: 4px;">
                <span id="env_tag" class=""></span>
            </div>
            <div class="login-logo">
                <!-- Electro<span>Cash</span> -->
                <img src="<?= base_url() ?>assets/images/logo-minify.webp" alt="">
            </div>
            <div class="login-subtitle">Acceso al Sistema</div>
        </div>
        <div class="login-body">
            <div class="form-group">
                <label for="">Usuario</label>
                <div class="block-input">
                    <div class="block-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><title>At</title><path d="M320 254.27c-4.5 51-40.12 80-80.55 80s-67.34-35.82-63.45-80 37.12-80 77.55-80 70.33 36 66.45 80z" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/><path d="M319.77 415.77c-28.56 12-47.28 14.5-79.28 14.5-97.2 0-169-78.8-160.49-176s94.31-176 191.51-176C381 78.27 441.19 150 432.73 246c-6.31 71.67-52.11 92.32-76.09 88.07-22.56-4-41.18-24.42-37.74-63.5l8.48-96.25" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/></svg>
                    </div>
                    <input type="text" name="_username" required>
                </div>
            </div>
            <div class="form-group">
                <label for="">Contraseña</label>
                <div class="block-input">
                    <div id="password_button" class="block-icon password-icon">
                        <svg id="lock_icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><title>Lock Closed</title><path d="M336 208v-95a80 80 0 00-160 0v95" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/><rect x="96" y="208" width="320" height="272" rx="48" ry="48" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/></svg>
                        <svg id="unlock_icon" class="d-none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><title>Lock Open</title><path d="M336 112a80 80 0 00-160 0v96" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/><rect x="96" y="208" width="320" height="272" rx="48" ry="48" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/></svg>                  
                    </div>
                    <input id="input_password" type="password" name="_password" required> 
                </div>
            </div>
            <div class="login-footer">
				<button id="_btn_send" class="btn">
					<span>Autenticar</span>
					<div class="sprite-btn"></div>
				</button>
			</div>

        </div>
    </form>

    <div id="ttoast_" class="ttoast ttoast-danger">
        <div class="ttoast-icon">
            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" 
                xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                viewBox="0 0 330 330" style="enable-background:new 0 0 330 330;" xml:space="preserve">
                <g><g><g>
                    <path d="M165,0.008C74.019,0.008,0,74.024,0,164.999c0,90.977,74.019,164.992,165,164.992s165-74.015,165-164.992
                        C330,74.024,255.981,0.008,165,0.008z M165,299.992c-74.439,0-135-60.557-135-134.992S90.561,30.008,165,30.008
                        s135,60.557,135,134.991C300,239.436,239.439,299.992,165,299.992z"/>
                    <path d="M165,130.008c-8.284,0-15,6.716-15,15v99.983c0,8.284,6.716,15,15,15s15-6.716,15-15v-99.983
                        C180,136.725,173.284,130.008,165,130.008z"/>
                    <path d="M165,70.011c-3.95,0-7.811,1.6-10.61,4.39c-2.79,2.79-4.39,6.66-4.39,10.61s1.6,7.81,4.39,10.61
                        c2.79,2.79,6.66,4.39,10.61,4.39s7.81-1.6,10.609-4.39c2.79-2.8,4.391-6.66,4.391-10.61s-1.601-7.82-4.391-10.61
                        C172.81,71.61,168.95,70.011,165,70.011z"/>                        
                </g></g></g>
            </svg>
        </div>
        <div class="ttoast-content">
            <div class="ttoast-title">** title **</div>
            <div class="ttoast-message">** message **</div>
        </div>
        <a href="javascript:void(0)" class="ttoast-close">&#10006;</a>
    </div>
    

	<script src="<?= base_url() ?>global-code/js/vaporwave.js"></script>
    <script src="<?= base_url() ?>js_modules/app.js?v=3"></script>
	<script src="<?= base_url() ?>js_modules/sesion/login.js"></script>
</body>
</html>

<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>404 Page Not Found</title>
<link href="../../assets/css/dashboard.css" rel="stylesheet">
</head>
<body class="">
	<div class="page">
	  <div class="page-content">
		    <div class="container text-center">
		      	<div class="display-1 text-muted mb-5"><i class="si si-exclamation"></i> 404</div>
		      	<h1 class="h2 mb-3"><?php echo $heading; ?></h1>
		      	<p class="h4 text-muted font-weight-normal mb-7"><?php echo $message; ?></p>
		      	<a class="btn btn-primary" href="javascript:history.back()">
		        	<i class="fe fe-arrow-left mr-2"></i>Volver Atras
		      	</a>
		    </div>
		  </div>
	</div>
</body>
</html>


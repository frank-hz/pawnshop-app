<!-- <div class="error-container">
	<div class="error_message">
		<img src="<?= base_url() ?>assets/images/__use/robot_arm_help.svg" alt="">
		<h2><?php echo $titulo; ?></h2>
		<p><?php echo $mensaje; ?></p>
		<button class="btn btn-red" onClick="javascript:history.go(-1)">
			<i class="ti-arrow-left"></i>
			Volver
		</button>
	</div>
</div> -->

<div class="page">
  <div class="page-content">
    <div class="container text-center">
      <div class="display-1 text-muted mb-5"><i class="si si-exclamation"></i> 404</div>
      <h1 class="h2 mb-3"><?php echo $titulo; ?></h1>
      <p class="h4 text-muted font-weight-normal mb-7"><?php echo $mensaje; ?></p>
      <a class="btn btn-primary" href="javascript:history.back()">
        <i class="fe fe-arrow-left mr-2"></i>Volver
      </a>
    </div>
  </div>
</div>
<div class="container">
	<div class="card">
		<div class="card-header bg-red">
			<?php if ($titulo): ?>
				<?php echo $titulo; ?>
			<?php endif ?>
		</div>
		<div class="card-body">
			<?php if ($mensaje): ?>
				<p>
					<?php echo $mensaje; ?>
				</p>
			<?php endif ?>
		</div>
	</div>
</div>
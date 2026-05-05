<input type="hidden" id="idcredito" value="<?= $credito->id; ?>">
<div class="container">
	<div class="card">
		<div class="card-header">
	    	<h4 class="card-title font-weight-bold">
				<a href="http://localhost/sistema-dev/Credito/detalle/1898">
					<span class="text-blue">#CH01-2391</span>
				</a> | CONGELAMIENTO
			</h4>
	  	</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <div class="form-label mb-0">Cliente:</div>
                        <span><?= $credito->cliente; ?></span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <div class="form-label mb-0">Capital:</div>
                        <span>S/ <?= $credito->monto; ?></span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <div class="form-label mb-0">Cuota:</div>
                        <span>S/ <?= $credito->pago; ?></span>
                    </div>
                </div>

            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th class="w-1 text-center">#</th>
                        <th>Descripcion</th>
                        <th class="text-right px-3">Monto</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>01</td>
                        <td>Capital</td>
                        <td class="text-right px-3">
                            S/ <?= $credito->monto; ?>
                        </td>
                    </tr>
                    <?php foreach($cuotas as $cuota=>$val): ?>
                    <tr>
                        <td><?= $cuota+1; ?></td>
                        <td>Cuota <?= $val->orden; ?></td>
                        <td class="text-right px-3">
                            S/ <?= $val->monto; ?>
                        </td>
                    </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
        </div>
	</div>
</div>

<script src="<?= base_url() ?>js_modules/credito/credito.congelamiento.js"></script>
<div class="container">
    <div class="row pt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <p class="h4">Configuracion de Sistema</p>
                    </div>
                    <div class="card-options">
                        <!-- <button class="btn btn-dark">
                            <i class="fe fe-edit-2"></i> Editar
                        </button> -->
                    </div>
                </div>
                <div class="card-body">
                    <label class="form-label">Diseño de Login</label>
                    <div class="row">
                    <?php if(!empty($views_login)): ?>
                        <?php foreach($views_login as $key => $login): ?>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <?= $login->file_title; ?>
                                        </div>
                                        <div class="card-options">
                                            <label class="custom-switch">
                                                <input type="radio" name="login_view_item" class="custom-switch-input" 
                                                value="<?= $login->id; ?>" 
                                                <?php 
                                                    if($login->status=='IN_USE')
                                                    echo 'checked'; 
                                                ?>
                                                >
                                                <span class="custom-switch-indicator"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <img src="<?= base_url().$login->file_preview; ?>" alt="<?= $login->file_title ?>" class="rounded">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                    <div class="col-md-12 py-3">
                        <p class="text-center">Sin Datos</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="<?= base_url() ?>js_modules/system-config/create_update.js"></script>

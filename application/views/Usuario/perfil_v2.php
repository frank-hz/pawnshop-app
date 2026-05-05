<!-- <pre><?= print_r($usuario); ?></pre> -->
<?php
    $IdUnique = uniqid();

?>
<input id="input_imagen" type="file" class="custom-file-input" data-id="<?= $usuario->empleado_id; ?>" data-url="<?= $usuario->imagen; ?>" data-nombre="<?= $usuario->nombre_full; ?>" accept="image/png, image/jpeg, image/jpg" style="visibility: hidden;">
<div class="my-3 my-md-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-4">
                <div class="card card-profile">
                  <div class="card-header" style="background-image: url(assets/images/photos/tom-barrett-318952-500.jpg);"></div>
                  <div class="card-body text-center">
                    <?php if (!empty($usuario->imagen)): ?>
                        <img src="<?= base_url().$usuario->imagen ?>" class="card-profile-img" alt="user profile">
                    <?php else: ?>
                        <img src="<?= base_url() ?>uploads/default/sin_imagen.jpg" class="card-profile-img" alt="user profile">
                    <?php endif ?>

                    <h3 class="mb-3"><?= $usuario->nombre_full; ?></h3>
                    <?php if ($usuario->id === $this->session->userdata('usuario_id')): ?>
                        <label for="input_imagen" class="btn btn-outline-primary">
                            <span class="fa fa-twitter"></span> 
                            Cambiar Imagen 
                        </label>
                    <?php endif; ?> 
                  </div>
                </div>
            </div>
            <div class="col-lg-8">
                <form autocomplete="off" spellscheck="off" class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fe fe-user text-blue"></i>
                            Mi Perfil
                        </h3>
                        <div class="card-options">
                        <?php  
                                switch ($usuario->estado) {
                                    case 'A':
                                        echo "<span class='stag stag-usu-A'>Activo</span>";
                                        break;
                                    
                                    case 'I':
                                        echo "<span class='stag stag-usu-I'>Inactivo</span>";
                                        break;
                                }
                            ?>
                        </div>
                    </div>
                    <div class="card-body">                    
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                <label class="form-label">Nombres y Apellidos</label>
                                <input type="text" class="form-control border-hidden p-1 text-left" disabled="" placeholder="Company" value="<?= $usuario->nombre_full; ?>">
                                </div>
                            </div>                        
                            <div class="col-sm-6 col-md-3">
                                <div class="form-group">
                                <label class="form-label">Rol</label>
                                <input type="text" class="form-control border-hidden p-1 text-left" disabled="" placeholder="Username" value="<?= $usuario->rol; ?>">
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="form-group">
                                <label class="form-label">Fecha de Creacion</label>
                                <input type="text" class="form-control border-hidden p-1 text-left" disabled="" placeholder="Username" value="<?= $usuario->create_at; ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6 col-md-4">
                                <div class="form-group">
                                <label class="form-label">Usuario</label>
                                <input type="text" class="form-control"  placeholder="Usuario" value="<?= $usuario->username; ?>">
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <div class="form-group">
                                <label class="form-label">Nueva Contraseña</label>
                                <input id="new_password" type="password" class="form-control" placeholder="Nueva Contraseña" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <div class="form-group">
                                <label class="form-label">Repetir Contraseña</label>
                                <input id="rep_password" type="password" class="form-control" placeholder="Repetir Contraseña" value="" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<script src="<?= base_url('js_modules/usuario/perfil_v2.js?v=34334334') ?>"></script>


<style>
    .border-hidden {
        border-color: transparent;
    }
</style>
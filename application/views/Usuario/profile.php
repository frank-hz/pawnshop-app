<script>
    window.usuario_id = <?= $usuario->id; ?>
</script>

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
                <form id="form_profile" autocomplete="off" spellscheck="off" class="card">
                    <input type="text" name="usuario_id" value="<?= $usuario->id; ?>" hidden>
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
                                <input type="text" class="form-control border-hidden p-1 text-left" disabled="" value="<?= $usuario->nombre_full; ?>">
                                </div>
                            </div>                        
                            <div class="col-sm-6 col-md-3">
                                <div class="form-group">
                                <label class="form-label">Rol</label>
                                <input type="text" class="form-control border-hidden p-1 text-left" disabled="" placeholder="Username" value="<?= $usuario->rol_nombre; ?>">
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
                            <div class="col-sm-6 col-md-5">
                                <div class="form-group">
                                <label class="form-label">Usuario</label>
                                <input type="text" class="form-control"  placeholder="Usuario" value="<?= $usuario->username; ?>" name="username">
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-5">
                                <div class="form-group">
                                <label class="form-label">Nueva Contraseña</label>
                                <input type="password" class="form-control" placeholder="Nueva Contraseña" name="password_new">
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-5">
                                <div class="form-group col-md-x mr-2">
                                    <label class="form-label">Contraseña Actual (confirmar):</label>
                                    <input id="rep_password" type="password" class="form-control" placeholder="Contraseña Actual" name="password_current" required>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-5">
                                <div class="col-mdx4 form-group">
                                    <div class="form-label">&nbsp;</div>
                                    <button type="submit" class="btn btn-primary">
                                        Confirmar y Guardar 
                                        <i class="fe fe-send"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>



<div id="MODAL_IMAGE" class="modal modal-blur fade" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                
            </div>
            <div class="modal-footer">
            <a href="#" class="btn btn-link link-secondary btn-3" data-bs-dismiss="modal"> Cancelar </a>
            <a href="#" class="btn btn-primary btn-5 ms-auto" data-bs-dismiss="modal">
                <!-- Download SVG icon from http://tabler.io/icons/icon/plus -->
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-2">
                <path d="M12 5l0 14"></path>
                <path d="M5 12l14 0"></path>
                </svg>
                Guardar
            </a>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('js_modules/controller/sesion.js?v=4087'); ?>" defer></script>
<script src="<?= base_url('js_modules/sesion/profile.js?v=5477'); ?>" defer></script>


<style>
    .border-hidden {
        border-color: transparent;
    }
</style>
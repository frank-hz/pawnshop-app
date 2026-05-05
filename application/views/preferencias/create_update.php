<div class="container">
    <div class="row pt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <p class="h4">Preferencias de Usuario</p>
                    </div>
                    <div class="card-options">
                        <!-- <button class="btn btn-dark">
                            <i class="fe fe-edit-2"></i> Editar
                        </button> -->
                    </div>
                </div>
                <div class="card-body">
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="form-label">
                                Sucursal de inicio:  
                                <small id="_feed_sede" class="text-danger"></small>                              
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="tablespace-filter-search tablespace-filter-search-right mr-1">
                                    <select id="_sede_select" class="tablespace-search-input">
                                        <option value="">- No definido -</option>
                                    </select>
                                    <div class="filter-search-icon">
                                        <i class="fe fe-chevron-down"></i>
                                    </div>
                                </div>
                                <div id="_sede_loader" class="ml-1 form-spinner-icon sm hidden"></div>
                            </div>
                            <a id="_btn_sucursal_retry" href="javascript:void(0)" class="btn-link d-none">
                                Reintentar consulta
                            </a>

                        </div>
                        
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>


<script src="<?= base_url() ?>js_modules/preferencias/create_update.js"></script>

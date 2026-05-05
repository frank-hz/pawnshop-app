<div class="container p-2">
    <div class="card">
        <div class="card-header">
            <h5 class="card-titles text-indigo" style="color:red">
                <i class="fe fe-users font-weight-bold"></i> 
                Users Manager
            </h5>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>State</th>
                        <th>Password</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(!empty($users)): ?>
                    <?php foreach($users as $key=>$user): ?>
                    <tr>
                        <td><?= $key+1; ?></td>
                        <td>
                            <a href="<?= base_url() ?>Empleado/detalle/<?= $user->id; ?>" class="btn-link text-indigo">
                                <?= $user->nombre_apellido; ?>
                            </a>
                        </td>
                        <td>
                            <?php $state = $user->estado; ?>
                            <select name="state-item" class="form-control border-rock" data-id="<?= $user->id; ?>">
                                <option value="A" <?php if($state==='A') echo 'selected'; ?> >Activo</option>
                                <option value="I" <?php if($state==='I') echo 'selected'; ?>>Inactivo</option>
                            </select>
                        </td>
                        <td>                            
                            <?= $user->password; ?>
                        </td>
                        <td>
                            <button class="btn btn-outline-dark" data-id="<?= $user->id; ?>" name="reset-item">Reset</button>
                            <button class="btn btn-outline-dark" data-id="<?= $user->id; ?>" name="encrypt-item">Encrypt</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td class="text-center" colspan="5">Sin Datos</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<script src="<?= base_url() ?>js_modules/admin/admin.users.js"></script>
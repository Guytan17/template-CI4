<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?= form_open_multipart('admin/users/save' . (isset($user) && $user ? '/' . $user->id : ''), ['id' => 'user_form']) ?>
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3"><?= esc($page_title) ?></h1>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="active" name="active" value="1"
                        <?= old('active', isset($user) && $user ? $user->active : 1) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="active">
                        Compte actif
                    </label>
                </div>
            </div>
            <?php if (session()->has('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        <?php foreach (session('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->has('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Informations
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label">Prénom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name"
                                   value="<?= old('first_name', $user->first_name ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="last_name" name="last_name"
                                   value="<?= old('last_name', $user->last_name ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="<?= old('email', $user->email ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="username" class="form-label">Nom d'utilisateur</label>
                            <input type="text" class="form-control" id="username" name="username"
                                   value="<?= old('username', $user->username ?? '') ?>">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="password" class="form-label">
                                Mot de passe <?= isset($user) ? '' : '<span class="text-danger">*</span>' ?>
                            </label>
                            <input type="password" class="form-control" id="password" name="password"
                                    <?= isset($user) ? '' : 'required' ?>>
                            <?php if (isset($user)): ?>
                                <small class="text-muted">Laisser vide pour ne pas modifier</small>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label for="birthdate" class="form-label">Date de naissance</label>
                            <input type="date" class="form-control" id="birthdate" name="birthdate"
                                   value="<?= old('birthdate', isset($user) && $user && $user->birthdate ? $user->birthdate->toDateString() : '') ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 mb-3">
            <div class="card h-100">
                <div class="card-header">
                    Avatar
                </div>
                <div class="card-body">
                    <?php
                    $hasAvatar = isset($user) && $user && $user->hasAvatar();
                    $avatarUrl = $hasAvatar ? $user->getAvatarUrl() : base_url('/assets/img/default.png');
                    ?>
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <img src="<?= $avatarUrl ?>" alt="Avatar" id="avatarPreview" class="rounded" style="width: 100px; height: 100px; object-fit: cover;">
                        <?php if ($hasAvatar): ?>
                            <button type="button" class="btn btn-danger btn-sm" id="deleteAvatarBtn" onclick="deleteAvatar(<?= $user->id ?>)">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        <?php endif; ?>
                    </div>
                    <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                    <small class="text-muted">Formats acceptés : JPG, PNG, GIF (Max: 2MB)</small>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-3">
            <div class="card h-100">
                <div class="card-header">
                    Groupes
                </div>
                <div class="card-body">
                    <?php
                    // Les groupes de l'utilisateur (array de noms de groupes)
                    $userGroups = isset($user) && !empty($user->groups) ? $user->groups : [];
                    ?>
                    <?php if (!empty($groups)): ?>
                        <?php foreach ($groups as $group): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="groups[]" value="<?= esc($group['name']) ?>" id="group_<?= esc($group['name']) ?>"
                                        <?= in_array($group['name'], $userGroups) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="group_<?= esc($group['name']) ?>">
                                    <?= esc($group['title']) ?>
                                    <?php if (!empty($group['description'])): ?>
                                        <small class="text-muted d-block"><?= esc($group['description']) ?></small>
                                    <?php endif; ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">Aucun groupe disponible</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col">
            <div class="d-flex justify-content-between">
                <a href="<?= base_url('admin/users') ?>" class="btn btn-secondary btn-lg">
                    <i class="fas fa-arrow-left"></i> Annuler
                </a>
                <button type="submit" class="btn btn-lg btn-primary">
                    <i class="fas fa-save"></i> Enregistrer
                </button>

            </div>
        </div>
    </div>
    <?php form_close() ?>
</div>

<script>
$(document).ready(function() {
    // Initialiser l'aperçu de l'avatar
    initImagePreview('#avatar', '#avatarPreview', '<?= base_url('/assets/img/default.png') ?>', 2);
});

function deleteAvatar(userId) {
    Swal.fire({
        title: 'Supprimer l\'avatar ?',
        text: "Cette action est irréversible.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Oui, supprimer',
        cancelButtonText: 'Annuler',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('admin/users/delete-avatar/') ?>' + userId,
                type: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                data: {
                    [csrfName]: csrfHash
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Mettre à jour l'aperçu avec l'image par défaut
                        $('#avatarPreview').attr('src', '<?= base_url('/assets/img/default.png') ?>');
                        // Masquer le bouton supprimer
                        $('#deleteAvatarBtn').hide();

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 2000
                        });
                    } else {
                        Swal.fire({
                            title: 'Erreur !',
                            text: response.message,
                            icon: 'error',
                            confirmButtonColor: '#d33',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        title: 'Erreur !',
                        text: 'Une erreur est survenue lors de la suppression.',
                        icon: 'error',
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
}
</script>

<?= $this->endSection() ?>

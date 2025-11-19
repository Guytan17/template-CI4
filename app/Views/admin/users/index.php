<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <?php if (session()->has('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session('success') ?>
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

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Liste des utilisateurs</h5>
                    <a href="<?= base_url('admin/users/form') ?>" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Ajouter un utilisateur
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="usersTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Avatar</th>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Username</th>
                                    <th>Statut</th>
                                    <th>Date de création</th>
                                    <th data-orderable="false">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Chargé via AJAX par DataTables -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialisation de DataTables
    const table = $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('datatable/searchdatatable') ?>',
            type: 'POST',
            data: function(d) {
                d.model = 'UserModel';
                d[csrfName] = csrfHash;
            },
            dataSrc: function(json) {
                // Mettre à jour le token CSRF après chaque requête
                csrfHash = json.csrfHash || csrfHash;
                return json.data;
            }
        },
        columns: [
            {
                data: 'id',
                width: '50px'
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    const avatarUrl = data.img_url ? '<?= base_url() ?>' + data.img_url : '<?= base_url('/assets/img/default.png') ?>';
                    return '<img src="' + avatarUrl + '" alt="Avatar" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;" onerror="this.src=\'<?= base_url('/assets/img/default.png') ?>\';">';
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    const firstName = row.first_name || '';
                    const lastName = row.last_name || '';
                    const fullName = (firstName + ' ' + lastName).trim();
                    return fullName || row.username || row.email || 'Utilisateur';
                }
            },
            { data: 'email' },
            {
                data: 'username',
                render: function(data) {
                    return data || '-';
                }
            },
            {
                data: 'active',
                render: function(data) {
                    return data == 1
                        ? '<span class="badge bg-success">Actif</span>'
                        : '<span class="badge bg-danger">Inactif</span>';
                }
            },
            {
                data: 'created_at',
                render: function(data) {
                    if (!data) return '-';
                    const date = new Date(data);
                    return date.toLocaleDateString('fr-FR');
                }
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    const isActive = row.active == 1;
                    const toggleBtn = isActive
                        ? `<button type="button" class="btn btn-sm btn-success" onclick="toggleActive(${row.id})" title="Désactiver">
                               <i class="fas fa-toggle-on"></i>
                           </button>`
                        : `<button type="button" class="btn btn-sm btn-secondary" onclick="toggleActive(${row.id})" title="Activer">
                               <i class="fas fa-toggle-off"></i>
                           </button>`;

                    return `
                        <a href="<?= base_url('admin/users/form/') ?>${row.id}" class="btn btn-sm btn-warning" title="Modifier">
                            <i class="fas fa-edit"></i>
                        </a>
                        ${toggleBtn}
                    `;
                }
            }
        ],
        language: {
            url: base_url + 'assets/js/datatable/datatable-2.1.4-fr-FR.json',
        },
        order: [[0, 'desc']], // Tri par ID décroissant par défaut
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Tous"]]
    });
});

function toggleActive(userId) {
    // Effectuer la requête AJAX
    $.ajax({
        url: '<?= base_url('admin/users/toggle-active/') ?>' + userId,
        type: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        data: {
            [csrfName]: csrfHash
        },
        dataType: 'json',
        success: function(response) {
            // Mettre à jour le token CSRF
            if (response.csrfHash) {
                csrfHash = response.csrfHash;
            }

            if (response.success) {
                // Recharger le DataTable pour voir le changement
                $('#usersTable').DataTable().ajax.reload(null, false);

                // Notification toast
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
                text: 'Une erreur est survenue.',
                icon: 'error',
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
        }
    });
}
</script>

<?= $this->endSection() ?>

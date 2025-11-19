<?= $this->extend('layouts/front') ?>

<?= $this->section('content') ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <h2 class="mb-3"><?= esc($page_title) ?></h2>

                <?php if (session()->has('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

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

                <?= form_open_multipart('account/profile' ,['id' => 'user_form']) ?>
                <div class="card shadow">
                    <div class="card-body p-3">
                        <!-- Section Affichage du profil actuel -->
                        <div class="row">
                            <div class="col-md-3 text-center">
                                <img src="<?= $user->getAvatarUrl() ?>" alt="Avatar" id="avatarPreview" class="rounded-circle border border-secondary border-2 mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                                <?php if ($user->hasAvatar()): ?>
                                    <button type="button" class="btn btn-danger btn-sm d-block w-100" id="deleteAvatarBtn" onclick="deleteAvatar(<?= $user->id ?>)">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </button>
                                <?php endif; ?>

                            </div>
                            <div class="col-md-9">
                                <h4><?= esc($user->getFullName()) ?></h4>
                                <p class="text-muted mb-2">
                                    <i class="fas fa-envelope"></i> <?= esc($user->email) ?>
                                </p>
                                <?php if ($user->username): ?>
                                    <p class="text-muted mb-2">
                                        <i class="fas fa-user"></i> <?= esc($user->username) ?>
                                    </p>
                                <?php endif; ?>
                                <?php if ($user->birthdate): ?>
                                    <p class="text-muted mb-2">
                                        <i class="fas fa-birthday-cake"></i> <?= $user->birthdate->toDateString() ?>
                                    </p>
                                <?php endif; ?>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-calendar"></i> Membre depuis <?= $user->created_at?->toLocalizedString('d MMMM Y') ?? '-' ?>
                                </p>
                            </div>
                        </div>
                        <hr class="my-3">
                        <div class="row">
                            <div class="col">
                                <h5 class="mb-3">Modifier mon avatar</h5>

                                <label for="avatar" class="form-label">Sélectionner un nouveau fichier</label>
                                <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                                <small class="text-muted d-block mt-2">
                                    <strong>Formats acceptés :</strong> JPG, PNG, GIF<br>
                                    <strong>Taille max :</strong> 2MB<br>
                                    <strong>Dimensions recommandées :</strong> 150x150 pixels
                                </small>
                            </div>
                        </div>
                        <hr class="my-3">

                        <!-- Formulaire de modification -->
                        <h5 class="mb-3">Modifier mes informations</h5>

                        <!-- Section Informations personnelles -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">Prénom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="first_name" name="first_name"
                                       value="<?= old('first_name', $user->first_name) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Nom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="last_name" name="last_name"
                                       value="<?= old('last_name', $user->last_name) ?>" required>
                            </div>
                        </div>

                        <div class="">
                            <label for="birthdate" class="form-label">Date de naissance</label>
                            <input type="date" class="form-control" id="birthdate" name="birthdate"
                                   value="<?= old('birthdate', $user->birthdate?->toDateString() ?? '') ?>">
                        </div>
                        <hr class="my-3">
                        <!-- Section Modification du mot de passe -->
                        <h5 class="my-3">Changer mon mot de passe</h5>
                        <p class="text-muted small">Laisser vide si vous ne souhaitez pas modifier votre mot de passe</p>

                        <div class="mb-3">
                            <label for="password" class="form-label">Nouveau mot de passe</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div id="passwordStrength" class="mt-2" style="display: none;">
                                <div class="progress" style="height: 5px;">
                                    <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                </div>
                                <small id="passwordStrengthText" class="text-muted"></small>
                            </div>
                            <small class="text-muted d-block mt-1">
                                <strong>Exigences :</strong>
                                <ul class="mb-0 mt-1" id="passwordRequirements" style="display: none;">
                                    <li id="req-length">Au moins 8 caractères</li>
                                    <li id="req-lowercase">Au moins une lettre minuscule</li>
                                    <li id="req-uppercase">Au moins une lettre majuscule</li>
                                    <li id="req-number">Au moins un chiffre</li>
                                    <li id="req-special">Au moins un caractère spécial</li>
                                </ul>
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirm" class="form-label">Confirmer le nouveau mot de passe</label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm">
                        </div>


                        <!-- Boutons d'action -->
                        <hr class="my-4">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                <i class="fas fa-save"></i> Enregistrer les modifications
                            </button>
                            <a href="<?= base_url('account/dashboard') ?>" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                        </div>
                        <?= form_close() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        #passwordRequirements li {
            color: #dc3545;
        }
        #passwordRequirements li.valid {
            color: #28a745;
        }
        #passwordRequirements li.valid::before {
            content: '✓ ';
            font-weight: bold;
        }


        #submitBtn:disabled {
            cursor: not-allowed;
            opacity: 0.65;
        }
    </style>


    <script>
        /**
         * Initialisation au chargement du DOM
         */
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const passwordConfirmInput = document.getElementById('password_confirm');
            const togglePasswordBtn = document.getElementById('togglePassword');

            // Initialiser l'aperçu de l'avatar
            initImagePreview('#avatar', '#avatarPreview', '<?= $user->getAvatarUrl() ?>', 2);

            // Écouteurs pour le mot de passe
            passwordInput.addEventListener('input', validatePasswordInput);
            passwordConfirmInput.addEventListener('input', validatePasswordInput);
            togglePasswordBtn.addEventListener('click', togglePasswordVisibility);
        });

        /**
         * Alterne la visibilité du champ mot de passe
         */
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('togglePassword').querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        /**
         * Valide le mot de passe et affiche les critères
         * Désactive/Active le bouton de soumission selon la validation
         */
        function validatePasswordInput() {
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirm').value;
            const progressBar = document.querySelector('#passwordStrength .progress-bar');
            const strengthText = document.getElementById('passwordStrengthText');
            const passwordStrength = document.getElementById('passwordStrength');
            const passwordRequirements = document.getElementById('passwordRequirements');
            const submitBtn = document.getElementById('submitBtn');

            // Si le champ password est vide, masquer les critères et activer le bouton
            if (password.length === 0) {
                passwordStrength.style.display = 'none';
                passwordRequirements.style.display = 'none';
                submitBtn.disabled = false;
                return;
            }

            // Afficher les critères si l'utilisateur commence à entrer un mot de passe
            passwordStrength.style.display = 'block';
            passwordRequirements.style.display = 'block';

            // Vérifier les critères
            const requirements = {
                length: password.length >= 8,
                lowercase: /[a-z]/.test(password),
                uppercase: /[A-Z]/.test(password),
                number: /[0-9]/.test(password),
                special: /[^A-Za-z0-9]/.test(password)
            };

            // Mettre à jour la liste des critères
            updateRequirementUI('req-length', requirements.length);
            updateRequirementUI('req-lowercase', requirements.lowercase);
            updateRequirementUI('req-uppercase', requirements.uppercase);
            updateRequirementUI('req-number', requirements.number);
            updateRequirementUI('req-special', requirements.special);

            // Calculer la force
            let strength = Object.values(requirements).filter(Boolean).length;

            // Mettre à jour la barre de progression
            const percentage = (strength / 5) * 100;
            progressBar.style.width = percentage + '%';

            // Mettre à jour les couleurs et le texte
            updatePasswordStrengthUI(progressBar, strengthText, strength);

            // Vérifier la correspondance des mots de passe
            const allRequirementsMet = Object.values(requirements).every(req => req === true);
            const passwordsMatch = password === passwordConfirm;

            // Désactiver le bouton si les critères ne sont pas tous validés ou si les mots de passe ne correspondent pas
            submitBtn.disabled = !(allRequirementsMet && passwordsMatch);
        }

        /**
         * Met à jour l'affichage d'un critère (valide ou non)
         * @param {string} elementId - L'ID de l'élément
         * @param {boolean} isValid - Si le critère est validé
         */
        function updateRequirementUI(elementId, isValid) {
            const element = document.getElementById(elementId);
            if (isValid) {
                element.classList.add('valid');
            } else {
                element.classList.remove('valid');
            }
        }

        /**
         * Met à jour l'affichage de la force du mot de passe
         * @param {HTMLElement} progressBar - La barre de progression
         * @param {HTMLElement} strengthText - L'élément de texte
         * @param {number} strength - Le niveau de force (0-5)
         */
        function updatePasswordStrengthUI(progressBar, strengthText, strength) {
            progressBar.classList.remove('bg-danger', 'bg-warning', 'bg-info', 'bg-success');

            const strengthLevels = {
                0: { class: 'bg-danger', text: 'Très faible', textClass: 'text-danger' },
                1: { class: 'bg-danger', text: 'Très faible', textClass: 'text-danger' },
                2: { class: 'bg-warning', text: 'Faible', textClass: 'text-warning' },
                3: { class: 'bg-info', text: 'Moyen', textClass: 'text-info' },
                4: { class: 'bg-success', text: 'Bon', textClass: 'text-success' },
                5: { class: 'bg-success', text: 'Excellent', textClass: 'text-success' }
            };

            const level = strengthLevels[strength];
            progressBar.classList.add(level.class);
            strengthText.textContent = level.text;
            strengthText.className = level.textClass;
        }


        /**
         * Supprime l'avatar de l'utilisateur
         * @param {number} userId - L'ID de l'utilisateur
         */
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
                    const formData = new FormData();

                    // Ajouter le token CSRF
                    const csrfToken = document.querySelector('input[name="<?= csrf_token() ?>"]');
                    if (csrfToken) {
                        formData.append(csrfToken.name, csrfToken.value);
                    }

                    fetch('<?= base_url('account/delete-avatar') ?>', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Mettre à jour l'aperçu avec l'image par défaut
                                document.getElementById('avatarPreview').src = "<?= base_url('/assets/img/default.png'); ?>";

                                // Masquer le bouton supprimer
                                const deleteBtn = document.getElementById('deleteAvatarBtn');
                                if (deleteBtn) {
                                    deleteBtn.classList.remove('d-block');
                                    deleteBtn.classList.add('d-none');
                                }

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Avatar supprimé',
                                    text: data.message,
                                    confirmButtonColor: '#28a745'
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erreur',
                                    text: data.message,
                                    confirmButtonColor: '#d33'
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erreur',
                                text: 'Une erreur est survenue lors de la suppression.',
                                confirmButtonColor: '#d33'
                            });
                        });
                }
            });
        }
    </script>

<?= $this->endSection() ?>
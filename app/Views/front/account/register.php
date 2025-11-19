<?= $this->extend('layouts/front') ?>

<?= $this->section('content') ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0 text-center"><?= esc($title) ?></h4>
                    </div>
                    <div class="card-body p-4">
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

                        <form action="<?= base_url('account/register') ?>" method="post" id="registerForm">
                            <?= csrf_field() ?>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label">Prénom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="first_name" name="first_name"
                                           value="<?= old('first_name') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="last_name" class="form-label">Nom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="last_name" name="last_name"
                                           value="<?= old('last_name') ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="<?= old('email') ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="username" class="form-label">Nom d'utilisateur</label>
                                <input type="text" class="form-control" id="username" name="username"
                                       value="<?= old('username') ?>">
                                <small class="text-muted">Optionnel - Utilisé pour la connexion</small>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div id="passwordStrength" class="mt-2">
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                    </div>
                                    <small id="passwordStrengthText" class="text-muted"></small>
                                </div>
                                <small class="text-muted d-block mt-2">
                                    <strong>Exigences :</strong>
                                    <ul class="mb-0 mt-1" id="passwordRequirements">
                                        <li id="req-length">Au moins 8 caractères</li>
                                        <li id="req-lowercase">Au moins une lettre minuscule</li>
                                        <li id="req-uppercase">Au moins une lettre majuscule</li>
                                        <li id="req-number">Au moins un chiffre</li>
                                        <li id="req-special">Au moins un caractère spécial</li>
                                    </ul>
                                </small>
                            </div>

                            <div class="mb-3">
                                <label for="password_confirm" class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div id="passwordMatchContainer" class="mt-2">
                                    <small id="passwordMatchText" class="text-muted"></small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="birthdate" class="form-label">Date de naissance</label>
                                <input type="date" class="form-control" id="birthdate" name="birthdate"
                                       value="<?= old('birthdate') ?>">
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn" disabled>
                                    <i class="fas fa-user-plus"></i> Créer mon compte
                                </button>
                                <a href="<?= base_url('login') ?>" class="btn btn-link">
                                    J'ai déjà un compte - Se connecter
                                </a>
                            </div>
                        </form>
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

        .password-match-error {
            color: #dc3545;
            display: none;
        }

        .password-match-error.show {
            display: block;
        }

        .password-match-success {
            color: #28a745;
            display: none;
        }

        .password-match-success.show {
            display: block;
        }

        .btn:disabled {
            cursor: not-allowed;
            opacity: 0.65;
        }
    </style>

    <script>
        /**
         * Initialisation des validations du formulaire
         */
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const passwordConfirmInput = document.getElementById('password_confirm');
            const submitBtn = document.getElementById('submitBtn');
            const togglePasswordBtn = document.getElementById('togglePassword');
            const togglePasswordConfirmBtn = document.getElementById('togglePasswordConfirm');

            // Écouteurs d'événement
            passwordInput.addEventListener('input', validatePassword);
            passwordConfirmInput.addEventListener('input', validatePassword);
            togglePasswordBtn.addEventListener('click', togglePasswordVisibility);
            togglePasswordConfirmBtn.addEventListener('click', togglePasswordConfirmVisibility);
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
         * Alterne la visibilité du champ confirmation du mot de passe
         */
        function togglePasswordConfirmVisibility() {
            const passwordConfirmInput = document.getElementById('password_confirm');
            const icon = document.getElementById('togglePasswordConfirm').querySelector('i');

            if (passwordConfirmInput.type === 'password') {
                passwordConfirmInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordConfirmInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        /**
         * Valide les critères du mot de passe et la correspondance entre les deux champs
         */
        function validatePassword() {
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirm').value;
            const progressBar = document.querySelector('#passwordStrength .progress-bar');
            const strengthText = document.getElementById('passwordStrengthText');
            const submitBtn = document.getElementById('submitBtn');
            const passwordMatchText = document.getElementById('passwordMatchText');

            // Vérification des critères du mot de passe
            const requirements = {
                length: password.length >= 8,
                lowercase: /[a-z]/.test(password),
                uppercase: /[A-Z]/.test(password),
                number: /[0-9]/.test(password),
                special: /[^A-Za-z0-9]/.test(password)
            };

            // Mise à jour de la liste des critères
            updateRequirementUI('req-length', requirements.length);
            updateRequirementUI('req-lowercase', requirements.lowercase);
            updateRequirementUI('req-uppercase', requirements.uppercase);
            updateRequirementUI('req-number', requirements.number);
            updateRequirementUI('req-special', requirements.special);

            // Calcul de la force du mot de passe
            let strength = Object.values(requirements).filter(Boolean).length;

            // Mise à jour de la barre de progression
            const percentage = (strength / 5) * 100;
            progressBar.style.width = percentage + '%';

            // Mise à jour de la couleur et du texte de la barre
            updatePasswordStrengthUI(progressBar, strengthText, strength);

            // Vérification de la correspondance des mots de passe
            let passwordsMatch = false;
            if (passwordConfirm.length > 0) {
                passwordsMatch = password === passwordConfirm;
                updatePasswordMatchUI(passwordMatchText, passwordsMatch);
            } else {
                passwordMatchText.textContent = '';
            }

            // Activation/Désactivation du bouton d'envoi
            const allRequirementsMet = Object.values(requirements).every(req => req === true);
            const formIsValid = allRequirementsMet && passwordsMatch && passwordConfirm.length > 0;

            submitBtn.disabled = !formIsValid;
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
         * Met à jour l'affichage de la correspondance des mots de passe
         * @param {HTMLElement} matchText - L'élément de texte
         * @param {boolean} match - Si les mots de passe correspondent
         */
        function updatePasswordMatchUI(matchText, match) {
            if (match) {
                matchText.textContent = '✓ Les mots de passe correspondent';
                matchText.className = 'text-success';
            } else {
                matchText.textContent = '✗ Les mots de passe ne correspondent pas';
                matchText.className = 'text-danger';
            }
        }
    </script>

<?= $this->endSection() ?>
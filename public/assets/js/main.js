$(document).ready(function() {
    const $html = $('html');
    const $themeToggle = $('#themeToggle');


// Initialisation au chargement
    if(localStorage.getItem('theme')) {
        $html.attr('data-bs-theme', localStorage.getItem('theme'));
        const theme = localStorage.getItem('theme');
        const title = theme === 'dark' ? 'Basculer en thème clair' : 'Basculer en thème sombre';
        updateTooltipTitle($themeToggle, title);
    }

// Changement de thème
    $themeToggle.on('click', function() {
        const currentTheme = $html.attr('data-bs-theme');
        const newTheme = (currentTheme === 'dark') ? 'light' : 'dark';

        $html.attr('data-bs-theme', newTheme);
        localStorage.setItem('theme', newTheme);

        const newTitle = newTheme === 'dark' ? 'Basculer en thème clair' : 'Basculer en thème sombre';
        updateTooltipTitle($themeToggle, newTitle);
    });

    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
    const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl))
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

    // Configuration de jQuery pour ajouter le token à chaque requête AJAX
    $.ajaxSetup({
        beforeSend: function (xhr) {
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfHash);
        }
    });

    // Remise à jour du token CSRF après chaque réponse
    $(document).on('ajaxSuccess', function (event, xhr, settings) {
        var response = xhr.responseJSON;
        if (response && response.csrf_token) {
            csrfHash = response.csrf_token; // Mise à jour du token
        }
    });
});


/**
 * Initialise un Select2 avec recherche AJAX générique
 *
 * @param {string} selector - Sélecteur CSS de l'élément (ex: '#mon-select' ou '.ma-classe')
 * @param {object} options - Configuration personnalisée
 *
 * Exemple d'utilisation :
 * initAjaxSelect2('#ingredient-select', {
 *     url: '/admin/ingredient/search',
 *     placeholder: 'Choisir un ingrédient...',
 *     searchFields: 'name,description'
 * });
 */
function initAjaxSelect2(selector, options) {
    // Valeur par défaut pour options si rien n'est passé
    options = options || {};

    // Configuration par défaut - ces valeurs seront utilisées si non spécifiées
    var defaultConfig = {
        placeholder: 'Rechercher...', // Texte affiché quand rien n'est sélectionné
        allowClear: true,            // Permet de vider la sélection avec un "X"
        theme: 'bootstrap-5',        // Utilise le thème Bootstrap 5 (si disponible)
        minimumInputLength: 0,       // 0 = charge dès l'ouverture, 1+ = attend X caractères
        delay: 300,                  // Délai en ms avant de lancer la recherche (évite trop de requêtes)
        url: '',                     // URL pour la recherche AJAX (OBLIGATOIRE)
        searchFields: '',            // Champs à rechercher (optionnel, pour info)
        cache: true,                 // Met en cache les résultats pour éviter les requêtes répétées
        showDescription: false       // Affiche ou non la description dans les résultats
    };

    // Fusion de la configuration par défaut avec les options personnalisées
    // jQuery.extend() combine les deux objets : les options écrasent les valeurs par défaut
    var config = $.extend({}, defaultConfig, options);

    // Vérification : on ne peut pas continuer sans URL
    if (!config.url) {
        console.error('Erreur initAjaxSelect2 : L\'URL est obligatoire pour la recherche AJAX');
        return false;
    }

    // Initialisation de Select2 avec la configuration
    $(selector).select2({
        // APPARENCE ET COMPORTEMENT
        placeholder: config.placeholder,
        allowClear: config.allowClear,
        theme: config.theme,
        minimumInputLength: config.minimumInputLength,

        // CONFIGURATION AJAX
        ajax: {
            url: config.url,           // URL de l'endpoint de recherche
            dataType: 'json',         // Type de données attendu en retour
            delay: config.delay,      // Délai avant d'envoyer la requête

            /**
             * Fonction qui prépare les données à envoyer au serveur
             * @param {object} params - Paramètres de Select2 (terme de recherche, page, etc.)
             * @returns {object} - Données à envoyer en GET
             */
            data: function (params) {
                return {
                    search: params.term || '',  // Terme tapé par l'utilisateur (ou vide)
                    page: params.page || 1      // Numéro de page pour la pagination
                };
            },

            /**
             * Fonction qui traite les données reçues du serveur
             * @param {object} data - Réponse JSON du serveur
             * @param {object} params - Paramètres de la requête
             * @returns {object} - Format attendu par Select2
             */
            processResults: function (data, params) {
                // Gestion de la pagination
                params.page = params.page || 1;

                // Retour au format Select2
                return {
                    results: data.results,           // Tableau des résultats
                    pagination: {
                        more: data.pagination.more   // true s'il y a d'autres pages
                    }
                };
            },

            cache: config.cache  // Active/désactive le cache
        },

        /**
         * Template pour afficher chaque résultat dans la liste déroulante
         * @param {object} item - Un élément du tableau results
         * @returns {jQuery|string} - HTML à afficher
         */
        templateResult: function(item) {
            // Pendant le chargement, afficher le texte de chargement
            if (item.loading) {
                return item.text;
            }

            // Construction du HTML pour un résultat
            var html = "<div class='select2-result-item'>" +
                "<div class='select2-result-item__title'>" +
                (item.text || 'Sans nom') +
                "</div>";

            // Ajout de la description si elle existe et si configurée
            if (config.showDescription && item.description) {
                html += "<div class='select2-result-item__description'>" +
                    item.description +
                    "</div>";
            }

            html += "</div>";

            // Retour de l'élément jQuery
            return $(html);
        },

        /**
         * Template pour afficher l'élément sélectionné
         * @param {object} item - L'élément sélectionné
         * @returns {string} - Texte à afficher dans le select
         */
        templateSelection: function(item) {
            // On affiche seulement le nom/titre
            return item.text || 'Sélection sans nom';
        }
    });

    // Confirmation en console (pour le développement)
    //console.log('Select2 initialisé sur :', selector, 'avec URL :', config.url);

    return true; // Succès
}

function initTinymce(selector) {
    tinymce.init({
        selector: selector,
        height : "200",
        language: 'fr_FR',
        menubar: false,
        plugins: [
            'preview', 'code', 'fullscreen','wordcount', 'link','lists',
        ],
        license_key: 'gpl',
        skin: 'oxide',
        content_encoding: 'text',
        toolbar: 'undo redo | formatselect | ' +
            'bold italic link forecolor backcolor removeformat | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist outdent indent | ' +' fullscreen  preview code'
    });
}

/**
 * Initialise l'aperçu des images (réutilisable pour d'autres formulaires)
 * @param {string} inputSelector - Sélecteur du champ input file
 * @param {string} previewSelector - Sélecteur de l'élément d'aperçu
 * @param {string} defaultImageUrl - URL de l'image par défaut
 * @param {number} maxSizeInMB - Taille max en MB
 */
function initImagePreview(inputSelector, previewSelector, defaultImageUrl, maxSizeInMB) {
    const input = document.querySelector(inputSelector);
    const preview = document.querySelector(previewSelector);

    if (!input) return;

    input.addEventListener('change', function(event) {
        const file = event.target.files[0];

        if (!file) {
            preview.src = defaultImageUrl;
            return;
        }

        // Vérifier le type de fichier
        if (!isValidImageType(file)) {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Veuillez sélectionner un fichier image valide (JPG, PNG, GIF)',
                confirmButtonColor: '#d33'
            });
            input.value = '';
            return;
        }

        // Vérifier la taille du fichier
        const maxSizeInBytes = maxSizeInMB * 1024 * 1024;
        if (file.size > maxSizeInBytes) {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: `La taille du fichier ne doit pas dépasser ${maxSizeInMB}MB`,
                confirmButtonColor: '#d33'
            });
            input.value = '';
            return;
        }

        // Afficher l'aperçu
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });
}

/**
 * Vérifie si le fichier est une image valide
 * @param {File} file - Le fichier à vérifier
 * @returns {boolean} - True si valide
 */
function isValidImageType(file) {
    const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
    return validTypes.includes(file.type);
}


/**
 * Met à jour le titre d'un tooltip
 * @param {string|Element} element - Sélecteur CSS ou élément jQuery/DOM
 * @param {string} newTitle - Nouveau titre du tooltip
 */
function updateTooltipTitle(element, newTitle) {
    // Convertir en jQuery si c'est un sélecteur string
    const $element = typeof element === 'string' ? $(element) : $(element);

    // Récupérer l'instance du tooltip
    let tooltip = $element.data('bs.tooltip');

    if (tooltip) {
        tooltip.dispose();
    }

    // Supprimer l'élément du tooltip du DOM
    removeFromDOM('.tooltip');

    // Mettre à jour l'attribut
    $element.attr('data-bs-title', newTitle);

    // Réinitialiser le tooltip
    tooltip = new bootstrap.Tooltip($element[0]);
}

/**
 * Supprime tous les éléments du DOM correspondant au sélecteur
 * @param {string} selector - Sélecteur CSS (ex: '.tooltip', '#myId', 'div.class')
 */
function removeFromDOM(selector) {
    document.querySelectorAll(selector).forEach(el => el.remove());
}
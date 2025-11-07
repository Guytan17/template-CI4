<?php

if (!function_exists('generate_slug')) {
    function generateSlug($string)
    {
        // Normaliser la chaîne pour enlever les accents
        $string = \Normalizer::normalize($string, \Normalizer::FORM_D);
        $string = preg_replace('/[\p{Mn}]/u', '', $string);

        // Convertir les caractères spéciaux en minuscules et les espaces en tirets
        $string = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));

        return $string;
    }
}

if (!function_exists('upload_file')) {
    /**
     * Upload d’un fichier média avec gestion de l’Entity Media
     *
     * @param \CodeIgniter\Files\File $file - Fichier à uploader
     * @param string $subfolder - Sous-dossier (ex: avatars, recipes)
     * @param string|null $customName - Nom personnalisé du fichier
     * @param array|null $mediaData - Données associées (entity_id, entity_type, title, alt)
     * @param bool $isMultiple - Si false, remplace l’ancien média lié
     * @param array $acceptedMimeTypes - Types MIME autorisés
     * @param int $maxSize - Taille max en Ko
     * @return \App\Entities\Media|array - L’Entity Media ou un tableau d’erreur
     */
    function upload_file(
        \CodeIgniter\Files\File $file,
        string $subfolder = '',
        string $customName = null,
        array $mediaData = null,
        bool $isMultiple = false,
        array $acceptedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
        int $maxSize = 2048
    ) {
        // 1️⃣ Vérification du fichier
        if ($file->getError() !== UPLOAD_ERR_OK) {
            return ['status' => 'error', 'message' => getUploadErrorMessage($file->getError())];
        }

        if ($file->hasMoved()) {
            return ['status' => 'error', 'message' => 'Le fichier a déjà été déplacé.'];
        }

        if (!in_array($file->getMimeType(), $acceptedMimeTypes)) {
            return ['status' => 'error', 'message' => 'Type de fichier non accepté.'];
        }

        if ($file->getSizeByUnit('kb') > $maxSize) {
            return ['status' => 'error', 'message' => 'Fichier trop volumineux.'];
        }

        // 2️⃣ Définir le dossier de destination
        $year  = date('Y');
        $month = date('m');
        $uploadPath = FCPATH . 'uploads/' . trim($subfolder, '/') . '/' . $year . '/' . $month;

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0775, true);
        }

        // 3️⃣ Générer un nom propre
        helper('text');
        $baseName = $customName ? url_title($customName, '-', true) : pathinfo($file->getClientName(), PATHINFO_FILENAME);
        $ext = $file->getExtension();
        $newName = $baseName . '-' . uniqid() . '.' . $ext;

        // 4️⃣ Déplacer le fichier
        $file->move($uploadPath, $newName);
        $relativePath = 'uploads/' . trim($subfolder, '/') . '/' . $year . '/' . $month . '/' . $newName;

        // 5️⃣ Enregistrer ou mettre à jour le média
        $mediaModel = model('MediaModel');

        if (!$isMultiple && isset($mediaData['entity_id'], $mediaData['entity_type'])) {
            $existing = $mediaModel
                ->where('entity_id', $mediaData['entity_id'])
                ->where('entity_type', $mediaData['entity_type'])
                ->first();

            if ($existing) {
                // Supprimer l’ancien fichier
                if ($existing->fileExists()) {
                    @unlink($existing->getAbsolutePath());
                }

                // Mettre à jour l’existant
                $mediaModel->update($existing->id, ['file_path' => $relativePath] + $mediaData);
                return $mediaModel->find($existing->id);
            }
        }

        // 6️⃣ Insertion d’un nouveau média
        $data = array_merge(['file_path' => $relativePath], $mediaData ?? []);
        $mediaId = $mediaModel->insert($data, true);

        return $mediaModel->find($mediaId);
    }
}

if (!function_exists('getUploadErrorMessage')) {
    /**
     * Convertit le code d'erreur d'upload en message explicite.
     *
     * @param int $errorCode - Le code d'erreur
     * @return string - Le message d'erreur correspondant
     */
    function getUploadErrorMessage(int $errorCode): string
    {
        switch ($errorCode) {
            case UPLOAD_ERR_OK:
                return 'Aucune erreur, le fichier est valide.';
            case UPLOAD_ERR_INI_SIZE:
                return 'Le fichier dépasse la taille maximale autorisée par la configuration PHP.';
            case UPLOAD_ERR_FORM_SIZE:
                return 'Le fichier dépasse la taille maximale autorisée par le formulaire HTML.';
            case UPLOAD_ERR_PARTIAL:
                return 'Le fichier n\'a été que partiellement téléchargé.';
            case UPLOAD_ERR_NO_FILE:
                return 'Aucun fichier n\'a été téléchargé.';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Le dossier temporaire est manquant.';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Échec de l\'écriture du fichier sur le disque.';
            case UPLOAD_ERR_EXTENSION:
                return 'Une extension PHP a arrêté l\'upload du fichier.';
            default:
                return 'Une erreur inconnue est survenue lors de l\'upload.';
        }
    }

}
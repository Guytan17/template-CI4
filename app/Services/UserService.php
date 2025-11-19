<?php

namespace App\Services;

use App\Models\UserModel;
use App\Models\MediaModel;
use App\Models\UserProfileModel;

/**
 * Service de gestion des utilisateurs
 * Centralise la logique métier de sauvegarde d'utilisateur
 * Réutilisable par les contrôleurs Admin et Front
 *
 * Pédagogique: Permet de séparer la logique métier de la présentation
 */
class UserService
{
    protected $userModel;
    protected $mediaModel;
    protected $profileModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->mediaModel = new MediaModel();
        $this->profileModel = model('UserProfileModel');
    }

    /**
     * Sauvegarde ou met à jour un utilisateur avec tous ses éléments associés
     * (identité, profil, groupes, avatar)
     *
     * Cette méthode contient TOUTE la logique métier
     * Elle peut être appelée indifféremment par l'admin ou le front
     *
     * @param array $data Données à sauvegarder:
     *        - id: (optionnel) ID de l'utilisateur pour mise à jour
     *        - email: (requis) Email de l'utilisateur
     *        - username: (optionnel) Nom d'utilisateur
     *        - password: (optionnel) Mot de passe
     *        - active: (optionnel, admin) Statut actif/inactif
     *        - first_name: (optionnel) Prénom
     *        - last_name: (optionnel) Nom
     *        - birthdate: (optionnel) Date de naissance
     *        - groups: (optionnel, admin) Tableau de groupes
     *        - avatar: (optionnel) Fichier uploadé
     *
     * @return array ['success' => bool, 'user_id' => int, 'message' => string, 'data' => array]
     * @throws \Exception
     */
    public function saveUser(array $data): array
    {
        $users = auth()->getProvider();
        $id = $data['id'] ?? null;
        $isNew = ($id === null);

        try {
            if ($isNew) {
                return $this->createUser($data, $users);
            } else {
                return $this->updateUser($id, $data, $users);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Crée un nouvel utilisateur
     *
     * @param array $data Les données du nouvel utilisateur
     * @param mixed $users Le provider d'authentification Shield
     * @return array Résultat de la création
     */
    private function createUser(array $data, $users): array
    {
        // Créer l'utilisateur avec email et username
        $user = new \App\Entities\User([
            'email'    => $data['email'],
            'username' => $data['username'] ?? null,
            'active'   => $data['active'] ?? 1,
        ]);

        // Le password est obligatoire pour la création
        if (empty($data['password'])) {
            throw new \Exception('Le mot de passe est requis pour la création');
        }
        $user->password = $data['password'];

        // Sauvegarder - Shield valide et crée l'identité automatiquement
        if (!$users->save($user)) {
            throw new \Exception('Erreur lors de la création de l\'utilisateur');
        }

        // Récupérer l'ID du nouvel utilisateur
        $id = $users->getInsertID();
        $user = $users->findById($id);

        // Ajouter aux groupes sélectionnés (user par défaut si aucun groupe)
        $groups = $data['groups'] ?? ['user'];
        foreach ($groups as $group) {
            $user->addGroup($group);
        }

        // Sauvegarder le profil
        $this->saveProfile($id, $data);

        // Gérer l'avatar si fourni
        if (!empty($data['avatar'])) {
            $this->handleAvatar($id, $data['avatar'], $user);
        }

        return [
            'success' => true,
            'user_id' => $id,
            'message' => 'Utilisateur créé avec succès',
            'data' => [
                'id' => $id,
                'email' => $user->email,
                'username' => $user->username
            ]
        ];
    }

    /**
     * Met à jour un utilisateur existant
     *
     * @param int $id L'ID de l'utilisateur
     * @param array $data Les données à mettre à jour
     * @param mixed $users Le provider d'authentification Shield
     * @return array Résultat de la mise à jour
     */
    private function updateUser(int $id, array $data, $users): array
    {
        $user = $users->findById($id);
        if (!$user) {
            throw new \Exception('Utilisateur non trouvé');
        }

        // Préparer les données à mettre à jour
        $updateData = [];

        // Email - mettre à jour seulement si modifié
        if (isset($data['email']) && !empty($data['email']) && $data['email'] !== $user->email) {
            $updateData['email'] = $data['email'];
        }

        // Username - mettre à jour seulement si modifié
        if (isset($data['username']) && !empty($data['username']) && $data['username'] !== $user->username) {
            $updateData['username'] = $data['username'];
        }

        // Active - seulement en admin
        if (isset($data['active'])) {
            $updateData['active'] = $data['active'] ? 1 : 0;
        }

        // Password - seulement si fourni et non vide
        $passwordChanged = false;
        if (isset($data['password']) && !empty($data['password'])) {
            $user->password = $data['password'];
            $passwordChanged = true;
        }

        if(!empty($updateData)){
            $user->fill($updateData);
        }

        // Remplir et sauvegarder
        if (!empty($updateData) || $passwordChanged || $user->hasChanged()) {
            if (!$users->save($user)) {
                $errors = $users->errors();
                $errorMsg = !empty($errors) ? implode(', ', $errors) : 'Erreur lors de la mise à jour';
                throw new \Exception($errorMsg);
            }
        }

        /* Synchroniser les groupes (seulement si fournis)
         * cours :
         * ... = Spread operator
            Déplie un tableau en arguments individuels
            ['a', 'b'] devient 'a', 'b'
         */
        if (isset($data['groups'])) {
            $user->syncGroups(...($data['groups'] ?: []));
        }

        // Mettre à jour le profil
        $this->saveProfile($id, $data);

        // Gérer l'avatar si fourni
        if (!empty($data['avatar'])) {
            $this->handleAvatar($id, $data['avatar'], $user);
        }

        return [
            'success' => true,
            'user_id' => $id,
            'message' => 'Utilisateur mis à jour avec succès',
            'data' => [
                'id' => $id,
                'email' => $user->email,
                'username' => $user->username
            ]
        ];
    }

    /**
     * Sauvegarde les informations du profil utilisateur
     *
     * @param int $userId L'ID de l'utilisateur
     * @param array $data Les données du profil
     */
    private function saveProfile(int $userId, array $data): void
    {
        $profileData = [];

        if (isset($data['first_name'])) {
            $profileData['first_name'] = $data['first_name'] ?: null;
        }

        if (isset($data['last_name'])) {
            $profileData['last_name'] = $data['last_name'] ?: null;
        }

        if (isset($data['birthdate'])) {
            $profileData['birthdate'] = $data['birthdate'] ?: null;
        }

        if (!empty($profileData)) {
            $this->profileModel->saveProfile($userId, $profileData);
        }
    }

    /**
     * Gère le téléchargement et la sauvegarde de l'avatar
     *
     * @param int $userId L'ID de l'utilisateur
     * @param object $avatarFile Le fichier uploadé
     * @param object $user L'objet utilisateur
     */
    private function handleAvatar(int $userId, $avatarFile, $user): void
    {

        if ($avatarFile && $avatarFile->isValid() && !$avatarFile->hasMoved()) {
            $result = upload_file(
                $avatarFile,
                'avatars',
                $user->username,
                [
                    'entity_id' => $userId,
                    'entity_type' => 'user',
                    'title' => 'Avatar de ' . $user->username,
                    'alt' => 'Photo de profil de ' . $user->username
                ]
            );

            if (!($result instanceof \App\Entities\Media)) {
                throw new \Exception($result['message'] ?? 'Erreur lors de l\'upload de l\'avatar');
            }
        }
    }

    /**
     * Active ou désactive un utilisateur
     *
     * @param int $id L'ID de l'utilisateur
     * @return array Résultat de l'opération
     */
    public function toggleActive(int $id): array
    {
        $users = auth()->getProvider();
        $user = $users->findById($id);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ];
        }

        try {
            $user->active = $user->active ? 0 : 1;
            $users->save($user);

            return [
                'success' => true,
                'message' => 'Utilisateur ' . ($user->active ? 'activé' : 'désactivé') . ' avec succès',
                'active' => $user->active
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Supprime un utilisateur et toutes ses données associées
     *
     * @param int $id L'ID de l'utilisateur
     * @return array Résultat de la suppression
     */
    public function deleteUser(int $id): array
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ];
        }

        try {
            // Supprimer l'avatar si existe
            $user->deleteAvatar();

            // Supprimer l'utilisateur (CASCADE supprime aussi le profil)
            $this->userModel->delete($id);

            return [
                'success' => true,
                'message' => 'Utilisateur supprimé avec succès'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Supprime uniquement l'avatar d'un utilisateur
     *
     * @param int $id L'ID de l'utilisateur
     * @return array Résultat de la suppression
     */
    public function deleteAvatar(int $id): array
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ];
        }

        try {
            if ($user->deleteAvatar()) {
                return [
                    'success' => true,
                    'message' => 'Avatar supprimé avec succès'
                ];
            }

            return [
                'success' => false,
                'message' => 'Erreur lors de la suppression de l\'avatar'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ];
        }
    }
}
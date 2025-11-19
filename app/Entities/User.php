<?php

namespace App\Entities;

use CodeIgniter\Shield\Entities\User as ShieldUser;

class User extends ShieldUser
{
    /**
     * Attributs supplémentaires pour le profil utilisateur
     * Ces données viennent de la table user_profiles via JOIN
     */
    protected $datamap = [];

    protected $casts = [
        'first_name'    => 'string',
        'last_name'     => 'string',
        'birthdate'     => 'datetime',
        'profile_id'    => 'int',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'groups'        => 'array',
    ];

    protected $dates = ['birthdate', 'created_at', 'updated_at'];

    /**
     * Retourne le nom complet de l'utilisateur
     *
     * @return string
     */
    public function getFullName(): string
    {
        $firstName = $this->first_name ?? '';
        $lastName = $this->last_name ?? '';

        $fullName = trim($firstName . ' ' . $lastName);

        if (empty($fullName)) {
            return $this->username ?? $this->email ?? 'Utilisateur';
        }

        return $fullName;
    }

    /**
     * Vérifie si l'utilisateur est admin
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->inGroup('admin');
    }

    /**
     * Récupère l'avatar de l'utilisateur (entité Media)
     *
     * @return Media|null L'instance Media de l'avatar ou null
     */
    public function getAvatar(): ?Media
    {
        $mediaModel = model('MediaModel');

        $avatar = $mediaModel
            ->where('entity_type', 'user')
            ->where('entity_id', $this->id)
            ->first();

        return $avatar;
    }

    /**
     * Retourne l'URL de l'avatar ou une image par défaut
     *
     * @param string $default URL de l'image par défaut
     * @return string URL de l'avatar
     */
    public function getAvatarUrl(string $default = '/assets/img/default.png'): string
    {
        $avatar = $this->getAvatar();

        if ($avatar && $avatar->fileExists()) {
            return $avatar->getUrl();
        }

        return base_url($default);
    }

    /**
     * Vérifie si l'utilisateur a un avatar valide
     *
     * @return bool
     */
    public function hasAvatar(): bool
    {
        $avatar = $this->getAvatar();
        return $avatar !== null && $avatar->fileExists();
    }

    /**
     * Supprime l'avatar de l'utilisateur
     *
     * @return bool Succès de la suppression
     */
    public function deleteAvatar(): bool
    {
        $avatar = $this->getAvatar();

        if ($avatar === null) {
            return false;
        }

        return $avatar->delete();
    }
}
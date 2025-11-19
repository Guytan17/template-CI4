<?php

namespace App\Models;

use CodeIgniter\Shield\Models\UserModel as ShieldUserModel;
use App\Entities\User;
use App\Traits\DataTableTrait;

class UserModel extends ShieldUserModel
{
    use DataTableTrait;

    protected function initialize(): void
    {
        parent::initialize();

        $this->returnType = User::class;
    }

    /**
     * Configuration pour DataTable
     */
    protected function getDataTableConfig(): array
    {
        return [
            'searchable_fields' => [
                'users.id',
                'users.username',
                'auth_identities.secret',
                'user_profiles.first_name',
                'user_profiles.last_name',
            ],
            'joins' => [
                [
                    'table' => 'user_profiles',
                    'condition' => 'user_profiles.user_id = users.id',
                    'type' => 'left'
                ],
                [
                    'table' => 'auth_identities',
                    'condition' => 'auth_identities.user_id = users.id',
                    'type' => 'left'
                ],
                [
                    'table' => 'media',
                    'condition' => 'media.entity_id = users.id AND media.entity_type =\'user\'',
                    'type' => 'left'
                ]
            ],
            'select' => 'users.*, user_profiles.first_name, user_profiles.last_name, user_profiles.birthdate, auth_identities.secret as email, media.file_path as img_url',
            'with_deleted' => false,
        ];
    }

    /**
     * Surcharge find() pour charger automatiquement le profil (eager loading)
     */
    public function find($id = null)
    {
        $tempReturnType = $this->tempReturnType;
        $this->tempReturnType = $this->returnType;

        // Faire un LEFT JOIN avec user_profiles pour charger les données du profil
        $this->select('users.*, user_profiles.id as profile_id, user_profiles.first_name, user_profiles.last_name, user_profiles.birthdate')
             ->join('user_profiles', 'user_profiles.user_id = users.id', 'left');

        $result = parent::find($id);

        $this->tempReturnType = $tempReturnType;

        return $result;
    }

    /**
     * Surcharge findAll() pour charger automatiquement les profils (eager loading)
     */
    public function findAll(?int $limit = null, int $offset = 0)
    {
        $tempReturnType = $this->tempReturnType;
        $this->tempReturnType = $this->returnType;

        // Faire un LEFT JOIN avec user_profiles pour charger les données des profils
        $this->select('users.*, user_profiles.id as profile_id, user_profiles.first_name, user_profiles.last_name, user_profiles.birthdate')
             ->join('user_profiles', 'user_profiles.user_id = users.id', 'left');

        $result = parent::findAll($limit, $offset);

        $this->tempReturnType = $tempReturnType;

        return $result;
    }

    /**
     * Récupère tous les utilisateurs avec leurs groupes et profils
     * Utilise un nouveau builder pour éviter les conflits avec find()/findAll() surchargés
     *
     * @return array
     */
    public function getAllWithGroups(): array
    {
        $builder = $this->builder();

        $result = $builder->select('users.*, user_profiles.id as profile_id, user_profiles.first_name, user_profiles.last_name, user_profiles.birthdate, GROUP_CONCAT(auth_groups_users.group) as groups')
                    ->join('user_profiles', 'user_profiles.user_id = users.id', 'left')
                    ->join('auth_groups_users', 'auth_groups_users.user_id = users.id', 'left')
                    ->where('users.deleted_at IS NULL')
                    ->groupBy('users.id')
                    ->get()
                    ->getResult($this->returnType);

        // Normaliser les groupes : convertir la string GROUP_CONCAT en array
        foreach ($result as $user) {
            if (!empty($user->groups) && is_string($user->groups)) {
                $user->groups = explode(',', $user->groups);
            } elseif (empty($user->groups)) {
                $user->groups = [];
            }
        }

        return $result;
    }

    /**
     * Récupère un utilisateur avec ses groupes et profil
     * Utilise un nouveau builder pour éviter les conflits avec find()/findAll() surchargés
     *
     * @param int $id
     * @return User|null
     */
    public function getUserWithGroups(int $id): ?User
    {
        $builder = $this->builder();

        $result = $builder->select('users.*, user_profiles.id as profile_id, user_profiles.first_name, user_profiles.last_name, user_profiles.birthdate, GROUP_CONCAT(auth_groups_users.group) as groups')
                    ->join('user_profiles', 'user_profiles.user_id = users.id', 'left')
                    ->join('auth_groups_users', 'auth_groups_users.user_id = users.id', 'left')
                    ->where('users.id', $id)
                    ->where('users.deleted_at IS NULL')
                    ->groupBy('users.id')
                    ->get()
                    ->getFirstRow($this->returnType);

        // Normaliser les groupes : convertir la string GROUP_CONCAT en array
        if ($result) {
            if (!empty($result->groups) && is_string($result->groups)) {
                $result->groups = explode(',', $result->groups);
            } elseif (empty($result->groups)) {
                $result->groups = [];
            }
        }

        return $result;
    }

}

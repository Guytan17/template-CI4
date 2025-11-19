<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\UserProfile;

class UserProfileModel extends Model
{
    protected $table            = 'user_profiles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = UserProfile::class;
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'first_name',
        'last_name',
        'birthdate',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'user_id'    => 'required|integer',
        'first_name' => 'permit_empty|max_length[255]',
        'last_name'  => 'permit_empty|max_length[255]',
        'birthdate'  => 'permit_empty|valid_date',
    ];

    protected $validationMessages = [];

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Récupère le profil d'un utilisateur par user_id
     *
     * @param int $userId
     * @return UserProfile|null
     */
    public function getByUserId(int $userId): ?UserProfile
    {
        return $this->where('user_id', $userId)->first();
    }

    /**
     * Crée ou met à jour le profil d'un utilisateur
     *
     * @param int $userId
     * @param array $data
     * @return bool
     */
    public function saveProfile(int $userId, array $data): bool
    {
        $profile = $this->getByUserId($userId);

        $profileData = [
            'first_name' => $data['first_name'] ?? null,
            'last_name'  => $data['last_name'] ?? null,
            'birthdate'  => $data['birthdate'] ?? null,
        ];

        if ($profile) {
            // Mettre à jour
            return $this->update($profile->id, $profileData);
        } else {
            // Créer
            $profileData['user_id'] = $userId;
            return (bool) $this->insert($profileData);
        }
    }
}

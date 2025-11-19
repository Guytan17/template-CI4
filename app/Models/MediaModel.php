<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\Media;

class MediaModel extends Model
{
    protected $table            = 'media';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = Media::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'file_path',
        'entity_id',
        'entity_type',
        'title',
        'alt',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'file_path'   => 'required',
        'entity_id'   => 'required|numeric',
        'entity_type' => 'required|in_list[user,recipe,recipe_mea,step,ingredient,brand]',
    ];

    protected $validationMessages = [
        'file_path' => [
            'required' => 'Le chemin du fichier est requis.',
        ],
        'entity_id' => [
            'required' => 'L\'ID de l\'entité est requis.',
            'numeric'  => 'L\'ID de l\'entité doit être un nombre.',
        ],
        'entity_type' => [
            'required' => 'Le type d\'entité est requis.',
            'in_list'  => 'Le type d\'entité n\'est pas valide.',
        ],
    ];

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = ['deletePhysicalFile'];
    protected $afterDelete    = [];

    /**
     * Supprime le fichier physique avant de supprimer l'enregistrement
     *
     * @param array $data
     * @return array
     */
    protected function deletePhysicalFile(array $data): array
    {
        if (isset($data['id'])) {
            $ids = is_array($data['id']) ? $data['id'] : [$data['id']];

            foreach ($ids as $id) {
                $media = $this->find($id);

                if ($media && $media->fileExists()) {
                    @unlink($media->getAbsolutePath());
                }
            }
        }

        return $data;
    }

    /**
     * Supprime un média (enregistrement + fichier physique)
     *
     * @param int $id
     * @return bool
     */
    public function deleteMedia(int $id): bool
    {
        $media = $this->find($id);

        if ($media === null) {
            return false;
        }

        $this->db->transStart();

        // Suppression de l'enregistrement (le callback va supprimer le fichier)
        $result = $this->delete($id);

        $this->db->transComplete();

        return $this->db->transStatus() && $result;
    }



    /**
     * Récupère tous les médias d'une entité
     *
     * @param int $entityId
     * @param string $entityType
     * @return array
     */
    public function getEntityMedia(int $entityId, string $entityType): array
    {
        return $this->where('entity_id', $entityId)
                    ->where('entity_type', $entityType)
                    ->findAll();
    }
}

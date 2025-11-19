<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class UserProfile extends Entity
{
    protected $attributes = [
        'id'         => null,
        'user_id'    => null,
        'first_name' => null,
        'last_name'  => null,
        'birthdate'  => null,
        'created_at' => null,
        'updated_at' => null,
        'deleted_at' => null,
    ];

    protected $casts = [
        'id'         => 'integer',
        'user_id'    => 'integer',
        'first_name' => 'string',
        'last_name'  => 'string',
        'birthdate'  => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = ['birthdate', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * Retourne le nom complet
     *
     * @return string
     */
    public function getFullName(): string
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }
}

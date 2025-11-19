<?php

namespace App\Validation;

class CustomRules
{
    /**
     * Vérifie si le mot de passe est fort
     * Doit contenir au moins :
     * - 8 caractères
     * - 1 lettre minuscule
     * - 1 lettre majuscule
     * - 1 chiffre
     * - 1 caractère spécial
     *
     * @param string|null $str
     * @param string|null $fields
     * @param array $data
     * @return bool
     */
    public function strong_password(?string $str = null, ?string &$error = null): bool
    {
        if ($str === null || $str === '') {
            return true; // Si vide, laissez 'required' gérer
        }

        if (strlen($str) < 8) {
            $error = 'Le mot de passe doit contenir au moins 8 caractères.';
            return false;
        }

        if (!preg_match('/[a-z]/', $str)) {
            $error = 'Le mot de passe doit contenir au moins une lettre minuscule.';
            return false;
        }

        if (!preg_match('/[A-Z]/', $str)) {
            $error = 'Le mot de passe doit contenir au moins une lettre majuscule.';
            return false;
        }

        if (!preg_match('/[0-9]/', $str)) {
            $error = 'Le mot de passe doit contenir au moins un chiffre.';
            return false;
        }

        if (!preg_match('/[^A-Za-z0-9]/', $str)) {
            $error = 'Le mot de passe doit contenir au moins un caractère spécial.';
            return false;
        }

        return true;
    }
}

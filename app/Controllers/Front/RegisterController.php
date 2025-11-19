<?php

namespace App\Controllers\Front;

use App\Controllers\SiteController;

class RegisterController extends SiteController
{
    /**
     * Affiche le formulaire d'inscription
     * Redirige vers notre page personnalisée
     */
    public function registerView()
    {
        return redirect()->to('/account/register');
    }

    /**
     * Traite le formulaire d'inscription
     * Redirige vers notre page personnalisée qui gère tout
     */
    public function registerAction()
    {
        return redirect()->to('/account/register');
    }
}

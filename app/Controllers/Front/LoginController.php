<?php

namespace App\Controllers\Front;

use App\Controllers\SiteController;
use CodeIgniter\HTTP\RedirectResponse;

class LoginController extends SiteController
{
    /**
     * Affiche le formulaire de connexion
     */
    public function loginView()
    {
        if (auth()->loggedIn()) {
            return redirect()->to(config('Auth')->loginRedirect());
        }

        $this->title = 'Connexion';

        return $this->render('front/auth/login');
    }

    /**
     * Traite le formulaire de connexion
     */
    public function loginAction(): RedirectResponse
    {
        // Règles de validation
        $rules = $this->getValidationRules();

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $credentials             = $this->request->getPost(setting('Auth.validFields')) ?? [];
        $credentials             = array_filter($credentials);
        $credentials['password'] = $this->request->getPost('password');
        $remember                = (bool) $this->request->getPost('remember');

        // Tentative de connexion
        $result = auth()->remember($remember)->attempt($credentials);

        if (!$result->isOK()) {
            return redirect()->route('login')->withInput()->with('error', $result->reason());
        }

        // Vérifier s'il y a une action requise (2FA, activation email, etc.)
        // Seulement si les actions sont configurées
        if ($result->extraInfo() && config('Auth')->actions['login'] !== null) {
            return redirect()->route('auth-action-show')->with('error', $result->extraInfo());
        }

        return redirect()->to(config('Auth')->loginRedirect())->with('message', lang('Auth.successLogin'));
    }

    /**
     * Déconnexion
     */
    public function logoutAction(): RedirectResponse
    {
        auth()->logout();

        return redirect()->to(config('Auth')->logoutRedirect())->with('message', lang('Auth.successLogout'));
    }

    /**
     * Retourne les règles de validation
     */
    protected function getValidationRules(): array
    {
        $rules = [
            'password' => [
                'label'  => 'Auth.password',
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Auth.errorPasswordEmpty',
                ],
            ],
        ];

        $validFields = setting('Auth.validFields');

        // Construire les règles pour les champs valides (email, username, etc.)
        foreach ($validFields as $field) {
            $rules[$field] = [
                'label' => 'Auth.' . $field,
                'rules' => 'permit_empty',
            ];
        }

        return $rules;
    }
}

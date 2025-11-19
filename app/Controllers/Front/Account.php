<?php

namespace App\Controllers\Front;

use App\Controllers\SiteController;
use App\Models\UserModel;
use App\Models\MediaModel;
use App\Services\UserService;

class Account extends SiteController
{
    protected $userModel;
    protected $mediaModel;
    protected $userService;
    public function __construct()
    {
        $this->userModel  = new UserModel();
        $this->mediaModel = new MediaModel();
        $this->userService = new UserService();

    }


    /**
     * Affiche le formulaire d'inscription publique
     * Redirige vers la page d'accueil si l'utilisateur est déjà connecté
     */
    public function register()
    {
        // Vérifier si l'utilisateur est déjà connecté
        // Si oui, pas besoin de s'inscrire à nouveau
        if (auth()->loggedIn()) {
            return redirect()->to('/');
        }

        $this->title = 'Créer un compte';

        return $this->render('front/account/register', ['page_title' => $this->title]);
    }

    /**
     * Traite l'inscription d'un nouvel utilisateur
     * Shield gère automatiquement: validation, création user + identité
     * L'utilisateur est automatiquement ajouté au groupe 'user'
     */
    public function doRegister()
    {
        $users = auth()->getProvider();

        try {
            // Créer une nouvelle entité User
            $user = new \App\Entities\User([
                'email'    => $this->request->getPost('email'),
                'username' => $this->request->getPost('username') ?: null,
                'active'   => 1,
            ]);

            // Définir le password - Shield créera automatiquement l'identité email_password
            $user->password = $this->request->getPost('password');

            // Sauvegarder l'utilisateur - Shield valide et crée l'identité automatiquement
            if (!$users->save($user)) {
                throw new \Exception('Erreur lors de la création de votre compte');
            }

            // Récupérer l'ID du dernier insert
            $userId = $users->getInsertID();

            // Récupérer l'utilisateur complet avec son ID
            $user = $users->findById($userId);

            // Ajouter au groupe 'user' par défaut (l'utilisateur ne choisit pas son groupe)
            $user->addGroup('user');

            // Créer le profil custom
            model('UserProfileModel')->saveProfile($userId, [
                'first_name' => $this->request->getPost('first_name'),
                'last_name'  => $this->request->getPost('last_name'),
                'birthdate'  => $this->request->getPost('birthdate') ?: null,
            ]);

            // Connexion automatique
            auth()->login($user);

            $this->success('Votre compte a été créé avec succès.');
            return $this->redirect('/');

        } catch (\Exception $e) {
            $this->error($e->getMessage());
            session()->setFlashdata('messages', $this->messages);
            return redirect()->back()->withInput();
        }
    }

    /**
     * Affiche la page de profil de l'utilisateur connecté
     * Permet de voir et modifier ses informations personnelles
     */
    public function profile()
    {
        // Vérifier que l'utilisateur est bien connecté
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }

        $this->title = 'Mon profil';

        // Récupérer l'utilisateur avec son profil complet
        $user = $this->userModel->getUserWithGroups(auth()->id());

        return $this->render('front/account/profile', [
            'user' => $user,
            'page_title' => $this->title
        ]);
    }

    /**
     * Met à jour le profil de l'utilisateur connecté
     * Shield save() gère automatiquement: validation + mise à jour user + identité
     */
    public function updateProfile()
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->to('login');
        }

        try {
            $data = [
                'id' => $user->id,
                'password' => $this->request->getPost('password'), // Optionnel
                'first_name' => $this->request->getPost('first_name'),
                'last_name' => $this->request->getPost('last_name'),
                'birthdate' => $this->request->getPost('birthdate'),
                'avatar' => $this->request->getFile('avatar'),
            ];
            // Appel service
            $result = $this->userService->saveUser($data);

            // Redirection simple (pas d'exit)
            return redirect()->to('account/profile')
                ->with('success', $result['message']);

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Supprime l'avatar de l'utilisateur connecté (appelé en AJAX)
     * L'utilisateur conserve son profil mais l'image d'avatar est supprimée
     */
    public function deleteAvatar()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Requête non autorisée'
            ]);
        }

        $user = auth()->user();

        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Utilisateur non connecté'
            ]);
        }

        // Appel le service
        $result = $this->userService->deleteAvatar($user->id);

        if ($result['success']) {
            $result['defaultAvatarUrl'] = base_url('/assets/img/default-avatar.png');
        }

        return $this->response->setJSON($result);
    }
}

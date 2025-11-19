<?php

namespace App\Controllers\Admin;

use App\Models\UserModel;
use App\Models\MediaModel;
use App\Services\UserService;

class Users extends AdminController
{
    protected $userModel;
    protected $mediaModel;
    protected $userService;
    protected $breadcrumb = [['text' => 'Dashboard', 'url' => '/admin/dashboard']];
    public function __construct()
    {
        $this->userModel  = new UserModel();
        $this->mediaModel = new MediaModel();
        $this->userService = new UserService();

    }

    /**
     * Affiche la liste de tous les utilisateurs avec leurs groupes et profils
     * Les données sont chargées via AJAX avec DataTables
     */
    public function index()
    {
        $data = [
            'title' => 'Gestion des utilisateurs',
        ];
        $this->addBreadcrumb('Utilisateurs','');
        return $this->render('admin/users/index', $data);
    }

    /**
     * Affiche le formulaire de création ou d'édition d'un utilisateur
     *
     * @param int|null $id ID de l'utilisateur (null pour création)
     */
    public function form($id = null)
    {
        $this->addBreadcrumb('Utilisateurs','/admin/users');
        $user = null;

        // Si un ID est fourni, récupérer l'utilisateur pour édition
        if ($id !== null) {
            $this->addBreadcrumb('Modifier l\'utilisateur');
            $user = $this->userModel->getUserWithGroups($id);

            if (!$user) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        } else {
            $this->addBreadcrumb('Ajouter un utilisateur');
        }

        $data = [
            'page_title'  => $id ? 'Modifier l\'utilisateur' : 'Créer un utilisateur',
            'user'   => $user,
            'groups' => get_auth_groups(),
        ];

        return $this->render('admin/users/form', $data);
    }

    /**
     * Enregistre ou met à jour un utilisateur avec son profil, ses groupes et son avatar
     * Shield gère automatiquement: validation, création/mise à jour user + identité email/password
     *
     * @param int|null $id ID de l'utilisateur (null pour création)
     */
    public function save($id = null)
    {
        try {
            // Récupérer les données
            $data = [
                'id' => $id,
                'email' => $this->request->getPost('email'),
                'username' => $this->request->getPost('username'),
                'password' => $this->request->getPost('password'),
                'active' => $this->request->getPost('active'),
                'first_name' => $this->request->getPost('first_name'),
                'last_name' => $this->request->getPost('last_name'),
                'birthdate' => $this->request->getPost('birthdate'),
                'groups' => $this->request->getPost('groups'),
                'avatar' => $this->request->getFile('avatar'),
            ];

            // Appel le service
            $result = $this->userService->saveUser($data);

            // Redirection
            $this->success($result['message']);
            return $this->redirect('/admin/users');

        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Active ou désactive un utilisateur (toggle)
     * Appelé en AJAX depuis la liste des utilisateurs
     */
    public function toggleActive($id)
    {
        $users = auth()->getProvider();
        $user = $users->find($id);

        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Utilisateur non trouvé.',
                'csrfHash' => csrf_hash()
            ]);
        }

        try {
            // Inverser le statut actif
            $user->active = $user->active ? 0 : 1;
            $users->save($user);

            $status = $user->active ? 'activé' : 'désactivé';

            return $this->response->setJSON([
                'success' => true,
                'message' => "Utilisateur {$status} avec succès.",
                'active' => $user->active,
                'csrfHash' => csrf_hash()
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage(),
                'csrfHash' => csrf_hash()
            ]);
        }
    }

    /**
     * Supprime un utilisateur et toutes ses données associées
     * Supporte les requêtes AJAX (retourne du JSON) ou les requêtes normales (redirection)
     */
    public function delete($id)
    {
        // Vérifier que l'utilisateur existe
        $user = $this->userModel->find($id);

        if (!$user) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Utilisateur non trouvé.'
                ]);
            }
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        try {
            // Supprimer l'avatar si existe
            $user->deleteAvatar();

            // Supprimer l'utilisateur
            // Grâce au CASCADE sur user_profiles, le profil sera aussi supprimé
            // Shield gère la suppression des groupes et identités
            $this->userModel->delete($id);

            // Si requête AJAX, retourner JSON
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Utilisateur supprimé avec succès.'
                ]);
            }

            // Sinon, redirection classique
            $this->success('Utilisateur supprimé avec succès.');
            return $this->redirect('/admin/users');

        } catch (\Exception $e) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
                ]);
            }

            $this->error('Erreur lors de la suppression: ' . $e->getMessage());
            return $this->redirect('/admin/users');
        }
    }

    /**
     * Supprime uniquement l'avatar d'un utilisateur (appelé en AJAX)
     */
    public function deleteAvatar($id)
    {
        // Vérifier que l'utilisateur existe
        $user = $this->userModel->find($id);

        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'Utilisateur non trouvé.']);
        }

        // Supprimer l'avatar
        if ($user->deleteAvatar()) {
            return $this->response->setJSON(['success' => true, 'message' => 'Avatar supprimé avec succès.']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Erreur lors de la suppression de l\'avatar.']);
    }
}

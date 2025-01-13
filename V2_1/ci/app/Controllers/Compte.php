<?php
namespace App\Controllers;
use App\Models\Db_model;
use CodeIgniter\Exceptions\PageNotFoundException;

use CodeIgniter\Session\Session;
use CodeIgniter\Log\Log;

class Compte extends BaseController
{
    protected $session;

public function __construct()
{
    helper('form');
    $this->model = model(Db_model::class);
    $this->session = \Config\Services::session();
//...
}

 
public function lister()
{
    
    if (!$this->verifierConnexion() || $this->session->get('user_type') !== 'admin') {
        return redirect()->to(base_url('/index.php/compte/connecter'))->with('message', 'Accès non autorisé.');
    }

    $model = model(Db_model::class);

    // Récupérer les données détaillées des comptes
    $comptes = $model->get_all_comptes_details();

    // Séparer les comptes par rôle
    $data['admins'] = array_filter($comptes, fn($compte) => $compte['prs_role'] === 'admin');
    $data['juries'] = array_filter($comptes, fn($compte) => $compte['prs_role'] === 'jury');

    $data['titre'] = "Liste des comptes";
    $data['user_type'] = $this->session->get('user_type'); 

    
    return view('templates/haut_admin', $data)
        . view('affichage_comptes', $data)
        . view('templates/bas_admin');
}


public function creer()
{
    if (!$this->verifierRole('admin')) {
        return redirect()->to(base_url('/index.php/compte/connecter'))
                         ->with('error', 'Accès interdit : seuls les administrateurs peuvent créer des comptes.');
    }

    // Récupération du rôle de l'utilisateur connecté
    $data['user_type'] = $this->session->get('user_type');




    // L'utilisateur a validé le formulaire
    if ($this->request->getMethod() == "post") {
        $validationRules = [
            'pseudo' => 'required|max_length[255]|min_length[2]|is_unique[T_Personne_prs.prs_login]',
            'mdp' => 'required|max_length[255]|min_length[8]',
            'role' => 'required|in_list[admin,jury]',
            'nom' => 'required|max_length[255]|min_length[2]',
            'prenom' => 'required|max_length[255]|min_length[2]',
            'mdp_confirmation' => 'required|matches[mdp]',
        ];






        $validationMessages = [
            'pseudo' => [
                'required' => 'Veuillez entrer un pseudo pour le compte !',
                'min_length' => 'Le pseudo doit avoir au moins 2 caractères !',
                'max_length' => 'Le pseudo ne peut pas dépasser 255 caractères !',
                'is_unique' => 'Ce pseudo est déjà utilisé !'
            ],
            'mdp' => [
                'required' => 'Veuillez entrer un mot de passe !',
                'min_length' => 'Le mot de passe doit avoir au moins 8 caractères !',
                'max_length' => 'Le mot de passe ne peut pas dépasser 255 caractères !'
            ],
            'role' => [
                'required' => 'Veuillez choisir un rôle pour le compte !',
                'in_list' => 'Le rôle doit être admin ou jury.'
            ],
            'nom' => [
                'required' => 'Veuillez entrer un nom !',
                'min_length' => 'Le nom doit avoir au moins 2 caractères !',
                'max_length' => 'Le nom ne peut pas dépasser 255 caractères !'
            ],
            'prenom' => [
                'required' => 'Veuillez entrer un prénom !',
                'min_length' => 'Le prénom doit avoir au moins 2 caractères !',
                'max_length' => 'Le prénom ne peut pas dépasser 255 caractères !'
            ],
            'mdp_confirmation' => [
        'required' => 'Veuillez confirmer le mot de passe !',
        'matches' => 'Les mots de passe ne correspondent pas !'
    ]
        ];



        
        if ($this->request->getPost('role') === 'admin') {
            $validationRules['slogan'] = 'max_length[255]';
            $validationMessages['slogan'] = [
                'max_length' => 'Le slogan ne peut pas dépasser 255 caractères !'
            ];
        } elseif ($this->request->getPost('role') === 'jury') {
            $validationRules['biographie'] = 'required|max_length[500]';
            $validationRules['url'] = 'valid_url|max_length[255]';
            $validationRules['droit'] = 'max_length[255]';
            $validationRules['expertise'] = 'required|max_length[255]';

            $validationMessages['biographie'] = [
                'required' => 'Veuillez saisir une biographie !',
                'max_length' => 'La biographie ne peut pas dépasser 500 caractères !'
            ];
            $validationMessages['url'] = [
                'valid_url' => 'Veuillez entrer une URL valide !',
                'max_length' => 'L\'URL ne peut pas dépasser 255 caractères !'
            ];
            $validationMessages['droit'] = [
                'max_length' => 'Le champ droit ne peut pas dépasser 255 caractères !'
            ];
            $validationMessages['expertise'] = [
                'required' => 'Veuillez saisir un domaine d\'expertise !',
                'max_length' => 'Le domaine d\'expertise ne peut pas dépasser 255 caractères !'
            ];
        }

        if (!$this->validate($validationRules, $validationMessages)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }





        // Validation spécifique pour chaque rôle
        if ($this->request->getPost('role') === 'admin') {
            $validationRules['slogan'] = 'max_length[255]';
        } elseif ($this->request->getPost('role') === 'jury') {
            $validationRules['biographie'] = 'required|max_length[500]';
            $validationRules['url'] = 'valid_url|max_length[255]';
            $validationRules['droit'] = 'max_length[255]';
            $validationRules['expertise'] = 'required|max_length[255]';
        }

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }







        $recuperation = [
            'pseudo' => $this->request->getPost('pseudo'),
            'mdp' => $this->request->getPost('mdp'),
            'role' => $this->request->getPost('role'),
            'nom' => $this->request->getPost('nom'),
            'prenom' => $this->request->getPost('prenom'),
        ];
        
        // Ajouter les données spécifiques au rôle
        if ($recuperation['role'] === 'admin') {
            $recuperation['slogan'] = $this->request->getPost('slogan');
        } elseif ($recuperation['role'] === 'jury') {
            $recuperation['biographie'] = $this->request->getPost('biographie');
            $recuperation['url'] = $this->request->getPost('url');
            $recuperation['droit'] = $this->request->getPost('droit');
            $recuperation['expertise'] = $this->request->getPost('expertise');
        }
        
        // Appeler le modèle pour insérer les données
        if ($this->model->set_compte($recuperation)) {
            return redirect()->to(base_url('/index.php/admin/lister'))
                             ->with('success', 'Compte créé avec succès.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Une erreur est survenue lors de la création du compte.');
        }
    }

    $data['titre'] = 'Créer un compte';
    return view('templates/haut_admin', $data)
         . view('compte/compte_creer', $data)
         . view('templates/bas_admin');
}









public function connecter()
    {
        
        if ($this->request->getMethod() === "post") {
            if (!$this->validate([
                'pseudo' => 'required|min_length[2]|max_length[255]',
                'mdp' => 'required|min_length[8]|max_length[255]'
            ], [
                'pseudo' => [
                    'required' => 'Veuillez entrer un pseudo !',
                    'min_length' => 'Le pseudo est trop court (minimum 2 caractères).',
                    'max_length' => 'Le pseudo est trop long (maximum 255 caractères).'
                ],
                'mdp' => [
                    'required' => 'Veuillez entrer un mot de passe !',
                    'min_length' => 'Le mot de passe est trop court (minimum 8 caractères).',
                    'max_length' => 'Le mot de passe est trop long (maximum 255 caractères).'
                ]
            ])) {
                return $this->afficher_formulaire_connexion();
            }




            // Récupération des données du formulaire
            $username = $this->request->getVar('pseudo');
            $password = $this->request->getVar('mdp');

            // Vérification dans la base de données
            $user = $this->model->connect_compte($username, $password);
            
            if (!$user) {
                 return redirect()->back()->with('error', 'Pseudo ou mot de passe incorrect.');
            }


                
                $this->session->set([
                    'user_id' => $user->prs_id_personne,
                    'username' => $user->prs_login,
                    'nom' => $user->prs_nom,
                    'prenom' => $user->prs_prenom,
                    'user_type' => $user->user_type,
                    'logged_in' => true
                ]);
 

                // Redirection vers le tableau de bord selon le rôle
                return $this->chargerTableauDeBord($user->user_type);
            }

            // Échec de la connexion
            return $this->afficher_formulaire_connexion();
        }


    private function chargerTableauDeBord($role)
    {
        log_message('info', 'Chargement du tableau de bord pour le rôle: ' . $role);


        // Définir les menus pour chaque rôle
        $menus = [
            'admin' => [
                'Dashboard',
                'Gestion des utilisateurs',
                'Gestion des concours',
                'Paramètres'
            ],
            'jury' => [
                'Évaluer les candidats',
                'Liste des évaluations',
                'Résultats des concours'
            ]
        ];

        // Vérifier si le rôle est valide
        if (!array_key_exists($role, $menus)) {
            log_message('error', 'Rôle utilisateur invalide : ' . $role);
            throw new \Exception('Rôle utilisateur invalide.');
        }

        


 
        log_message('info', 'Affichage du menu pour le rôle: ' . $role);
 
            return view('templates/haut_admin', ['user_type' => $role])
    . view('connexion/compte_accueil', ['role' => $role])
    . view('templates/bas_admin', ['role' => $role]);

    }



    //fonction utilitaire pour afficher le formulaire de connexion
    private function afficher_formulaire_connexion($data = [])
    {
        $data['titre'] = 'Se connecter';
        return view('templates/haut', $data)
            . view('connexion/compte_connecter')
            . view('templates/bas');
    }







    //fonction utilitaire pour verifier la connexion
    private function verifierConnexion() {
        $isLoggedIn = $this->session->get('logged_in') === true;
        $username = $this->session->get('username');
        
        if ($isLoggedIn) {
            log_message('debug', 'Utilisateur vérifié comme connecté: ' . $username);
            return true;
        }
        
        log_message('debug', 'Tentative d\'accès non autorisée');
        return false;
    }




//fonction utilitaire pour verifier le role
    private function verifierRole($role)
    {
        return $this->verifierConnexion() && $this->session->get('user_type') === $role;
    }



    public function deconnecter()
    {
    $session=session();
    $session->destroy();
    return view('templates/haut', ['titre' => 'Se connecter'])
    . view('connexion/compte_connecter')
    . view('templates/bas');
    }





public function afficher_profil()
{
 
    if (!$this->verifierConnexion()) {
        return redirect()->to(base_url('/index.php/compte/connecter'));
    }

     $username = $this->session->get('username');
    $user_type = $this->session->get('user_type'); // admin ou jury

    // Récupérer les données du profil en fonction du type d'utilisateur
    $profile_data = $this->model->get_profile_data($username, $user_type);

    // Vérifier si des données ont été récupérées
    if (!$profile_data) {
         return redirect()->to(base_url('/index.php/compte/erreur'))->with('error', 'Utilisateur introuvable');
    }

     $data = [
        'profile_data' => $profile_data,
        'user_type' => $user_type
    ];

     return view('templates/haut_admin', $data) . 
           view('connexion/compte_profil', $data) . 
           view('templates/bas_admin', $data);
}




    
public function modifier_profil()
{
    // Vérification de la connexion
    if (!$this->verifierConnexion()) {
        return redirect()->to(base_url('/index.php/compte/connecter'));
    }

    $username = $this->session->get('username');
    $role = $this->session->get('user_type');

    // Récupération des données actuelles de l'utilisateur
    $profile_data = $this->model->get_profile_data($username, $role);
    if (!$profile_data) {
        log_message('error', 'Données du profil non trouvées pour ' . $username);
        return redirect()->to(base_url('/index.php/compte/connecter'));
    }

    if ($this->request->getMethod() === 'post') {
        // Validation uniquement pour le mot de passe
        $validationRules = [
            'mdp' => 'required|min_length[8]',
            'confirm_mdp' => 'required|matches[mdp]'
        ];

        $validationMessages = [
            'mdp' => [
                'required' => 'Le mot de passe est obligatoire.',
                'min_length' => 'Le mot de passe doit contenir au moins 8 caractères.'
            ],
            'confirm_mdp' => [
                'required' => 'La confirmation du mot de passe est obligatoire.',
                'matches' => 'Les mots de passe ne correspondent pas.'
            ]
        ];

        if (!$this->validate($validationRules, $validationMessages)) {
            return view('templates/haut_admin', ['role' => $role, 'user_type' => $role])
                . view('connexion/compte_modifier', [
                    'user_data' => $profile_data,
                    'role' => $role,
                    'validation_errors' => $this->validator->getErrors()
                ])
                . view('templates/bas_admin');
        }

        // Préparation des données pour la mise à jour du mot de passe
        $password = $this->request->getVar('mdp');
        $sel = bin2hex(random_bytes(16));
        $mot_de_passe_hache = hash('sha256', $password . $sel);
        
        $update_data = [
            'prs_MDP' => $mot_de_passe_hache,
            'prs_salt' => $sel
        ];

        // Appel de la méthode de mise à jour du mot de passe
        $update_success = $this->model->update_password($username, $update_data, $role);

        if ($update_success) {
            return redirect()->to(base_url('/index.php/compte/afficher_profil'))
                ->with('success', 'Mot de passe mis à jour avec succès.');
        } else {
            return redirect()->to(base_url('/index.php/compte/modifier_profil'))
                ->with('error', 'Erreur lors de la mise à jour du mot de passe.');
        }
    }

     return view('templates/haut_admin', ['role' => $role, 'user_type' => $role])
        . view('connexion/compte_modifier', [
            'user_data' => $profile_data,
            'role' => $role
        ])
        . view('templates/bas_admin');
}
    

   
//---------------------------------------------------------------------------------------

public function liste_concours_adm()
{
    // Vérification de la connexion et du rôle
    if (!$this->verifierConnexion() || $this->session->get('user_type') !== 'admin') {
        return redirect()->to(base_url('/index.php/compte/connecter'));
    }

    // Récupérer l'ID de l'utilisateur connecté
    $admin_id = $this->session->get('user_id');  

    $model = model(Db_model::class);
    //$data['concours'] = $model->get_admin_concours($admin_id);
    $data['concours'] = $model->get_all_concours();
    
    // Si aucun concours n'est trouvé
    if (empty($data['concours'])) {
        $data['message'] = "Aucun concours pour l'instant !";
    }

    // Ajout des données de session nécessaires
    $data['user_type'] = $this->session->get('user_type');
    
     return view('templates/haut_admin', $data)
        . view('connexion/liste_concours_adm', $data)
        . view('templates/bas_admin');
}

//-----------------------------------------------------Jury
public function afficherConcoursJury()
{
     
    if (!$this->verifierRole('jury')) {
        return redirect()->to(base_url('/index.php/compte/connecter'));
    }

     $juryId = $this->session->get('user_id');

    // Récupérer les concours liés au jury
    $concours = $this->model->getConcoursPourJury($juryId);

     $data = [
        'titre' => 'Mes Concours',
        'user_type' => $this->session->get('user_type'),
        'concours' => $concours
    ];

    return view('templates/haut_admin', $data)
        . view('connexion/liste_concours_jry', $data)
        . view('templates/bas_admin');
}




public function afficherGalerieJury($con_id_concours)
{
     if (!$this->verifierRole('jury')) {
        return redirect()->to(base_url('/index.php/compte/connecter'));
    }

    $model = model(Db_model::class);
    
    // Récupérer les détails du concours
    $concours = $model->db->table('T_CONCOURS_con')
        ->where('con_id_concours', $con_id_concours)
        ->get()
        ->getFirstRow();

    // Récupérer les candidats pré-sélectionnés pour ce concours
    $data['candidats'] = $model->getCandidatsPreselectionnes($con_id_concours);
    $data['concours'] = $concours;
    $data['titre'] = 'Galerie des Candidats';
    $data['user_type'] = $this->session->get('user_type');

     return view('templates/haut_admin', $data)
        . view('connexion/galerie_candidats_jry', $data)
        . view('templates/bas_admin');
}

 

//-----------------------------------------------------------------------------
public function ajout_concours()
{
 
    if (!$this->verifierConnexion() || $this->session->get('user_type') !== 'admin') {
        return redirect()->to(base_url('/index.php/compte/connecter'));
    }

     
    helper(['form', 'url']);

    
    $validation = \Config\Services::validation();
    $validation->setRules([
        'nom_concours' => [
            'rules' => 'required|min_length[3]|max_length[255]',
            'errors' => [
                'required' => 'Le nom du concours est obligatoire.',
                'min_length' => 'Le nom du concours doit avoir au moins 3 caractères.',
                'max_length' => 'Le nom du concours ne peut pas dépasser 255 caractères.'
            ]
        ],
        'date_debut' => [
    'rules' => 'required|valid_date|check_future_date',
    'errors' => [
        'required' => 'La date de début est obligatoire.',
        'valid_date' => 'La date de début n\'est pas valide.',
        'check_future_date' => 'La date de début doit être dans le futur.'
    ]
],

        'temps_candidature' => [
            'rules' => 'required|integer|greater_than[0]',
            'errors' => [
                'required' => 'Le temps de candidature est obligatoire.',
                'integer' => 'Le temps de candidature doit être un nombre entier.',
                'greater_than' => 'Le temps de candidature doit être supérieur à zéro.'
            ]
        ],
        'temps_pre_select' => [
            'rules' => 'required|integer|greater_than[0]',
            'errors' => [
                'required' => 'Le temps de présélection est obligatoire.',
                'integer' => 'Le temps de présélection doit être un nombre entier.',
                'greater_than' => 'Le temps de présélection doit être supérieur à zéro.'
            ]
        ],
        'temps_select' => [
            'rules' => 'required|integer|greater_than[0]',
            'errors' => [
                'required' => 'Le temps de sélection est obligatoire.',
                'integer' => 'Le temps de sélection doit être un nombre entier.',
                'greater_than' => 'Le temps de sélection doit être supérieur à zéro.'
            ]
            ],
            'discipline' => [
    'rules' => 'required|min_length[3]|max_length[255]',
    'errors' => [
        'required' => 'La discipline est obligatoire.',
        'min_length' => 'La discipline doit avoir au moins 3 caractères.',
        'max_length' => 'La discipline ne peut pas dépasser 255 caractères.'
    ]
]
    ]);

    // Validation personnalisée pour vérifier la date future
    if (!function_exists('check_future_date')) {
        function check_future_date($date)
        {
            return strtotime($date) > strtotime('today');
        }
    }

     
    if ($this->request->getMethod() === 'post') {
       
        if ($validation->withRequest($this->request)->run()) {
             
            $data = [
                'con_nom_concours' => $this->request->getPost('nom_concours'),
                'con_date_debut' => $this->request->getPost('date_debut'),
                'con_tps_candidature' => $this->request->getPost('temps_candidature'),
                'con_tps_pre_select' => $this->request->getPost('temps_pre_select'),
                'con_tps_select' => $this->request->getPost('temps_select'),
                'con_discipline' => $this->request->getPost('discipline'),
                'adm_id_admin' => $this->session->get('user_id')
            ];

            // Appeler le modèle pour insérer les données
            $model = model(Db_model::class);
            $result = $model->add_concours($data);

            if ($result) {
                 
                return redirect()->to(base_url('/index.php/admin/concours'))
                    ->with('success', 'Le concours a été ajouté avec succès.');
            } else {
                 
                return redirect()->back()->with('error', 'Erreur lors de l\'ajout du concours.');
            }
        }
    }

    
    $data = [
        'validation' => $validation,
        'user_type' => $this->session->get('user_type')
    ];

     
    return view('templates/haut_admin', $data)
        . view('connexion/ajout_concours', $data)
        . view('templates/bas_admin');
}









public function supprimer_concours($id_concours = null)
{
    
    if ($id_concours === null) {
        return redirect()->back()->with('error', 'ID du concours non spécifié.');
    }

    // Vérification de la connexion et du rôle
    if (!$this->verifierConnexion() || $this->session->get('user_type') !== 'admin') {
        return redirect()->to(base_url('/index.php/compte/connecter'));
    }

    // Récupérer l'ID de l'utilisateur connecté
    $admin_id = $this->session->get('user_id');

    $model = model(Db_model::class);
    
   
    
    // Vérifier si le concours existe et appartient à l'administrateur connecté
    $concours_existe = $model->verifier_concours_admin($id_concours, $admin_id);
    
    if (!$concours_existe) {
        log_message('error', 'Concours non trouvé ou non autorisé. ID: ' . $id_concours);
        return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à supprimer ce concours.');
    }

    // Vérifier que le concours n\'a pas commencé
    $est_a_venir = $model->est_concours_a_venir($id_concours);
    
    if (!$est_a_venir) {
     
        return redirect()->back()->with('error', 'Impossible de supprimer un concours qui a déjà commencé.');
    }

    // Supprimer le concours
    $result = $model->delete_concours($id_concours);

    if ($result) {
        
        return redirect()->to(base_url('/index.php/admin/concours'))
            ->with('success', 'Le concours a été supprimé avec succès.');
    } else {
        
        return redirect()->back()->with('error', 'Erreur lors de la suppression du concours.');
    }
}




}






<?php
namespace App\Models;
use CodeIgniter\Model;
class Db_model extends Model
{
protected $db;
protected $table = 'T_Personne_prs';

public function __construct()
{
    parent::__construct(); 
$this->db = db_connect(); //charger la base de données
// ou
// $this->db = \Config\Database::connect();
}




//fonction pour recuperer tout les comptes
public function get_all_compte()
{
    $resultat = $this->db->query("SELECT prs_login, prs_role AS user_type FROM T_Personne_prs;");
    return $resultat->getResultArray();
}







//fonction pour recuperer tout les comptes et leurs details
public function get_all_comptes_details()
{
    $query = "
        SELECT 
            p.prs_id_personne, 
            p.prs_nom, 
            p.prs_prenom, 
            p.prs_login, 
            p.prs_role, 
            p.prs_profil_actif, 
            p.prs_salt,
            j.jry_biographie, 
            j.jry_URL, 
            j.jry_droit, 
            j.jry_domaine_Expertise,
            a.adm_solgan
        FROM T_Personne_prs p
        LEFT JOIN T_Juy_jry j ON p.prs_id_personne = j.prs_id_personne
        LEFT JOIN T_Admin_adm a ON p.prs_id_personne = a.prs_id_personne
        ORDER BY p.prs_id_personne DESC;
    ";

    $resultat = $this->db->query($query);
    return $resultat->getResultArray();
}

//fonction pour recuperer une actu
public function get_actualite($numero)
{
    $requete = "SELECT act.act_id_actu,
                       act.act_titre,
                       act.act_date,
                       act.act_description,
                       act.actu_etat,
                       prs.prs_nom
                FROM T_ACTU_act act
                JOIN T_Admin_adm adm ON act.adm_id_admin = adm.prs_id_personne
                LEFT JOIN T_Personne_prs prs ON prs.prs_id_personne = adm.prs_id_personne
                WHERE act.act_id_actu = ?";
    $resultat = $this->db->query($requete, [$numero]);
    return $resultat->getRow(); // Retourne un seul objet
}

   
      
//fonction pour recuperer toutes le actus VIA UNE VUE des actus active
public function get_all_actualites()
{
    $sql = "SELECT * FROM v_actualites";
    $resultat = $this->db->query($sql);
    return $resultat->getResultArray();
}



//fonction pour recuperer le nombre de comptes
public function get_nb_comptes()
{

$resultat=$this->db->query("SELECT COUNT(*) as nb FROM T_Personne_prs;");
return $resultat->getRow();
}





//creation d'un compte(admin/jury) par un admin
public function set_compte($saisie)
{
    $login = $saisie['pseudo'];
    $mot_de_passe = $saisie['mdp'];
    $role = $saisie['role'];
    $nom = $saisie['nom'];
    $prenom = $saisie['prenom'];

    // Générer un sel unique
    $sel = bin2hex(random_bytes(16));
    // Hacher le mot de passe avec SHA-256
    $mot_de_passe_hache = hash('sha256', $mot_de_passe . $sel);

    // Commencer une transaction
    $this->db->transStart();

    try {
        // Insertion dans T_Personne_prs
        $sql_personne = "INSERT INTO T_Personne_prs(prs_nom, prs_prenom, prs_login, prs_MDP, prs_salt, prs_role, prs_profil_actif)
                         VALUES(?, ?, ?, ?, ?, ?, 1)";
        $this->db->query($sql_personne, [$nom, $prenom, $login, $mot_de_passe_hache, $sel, $role]);
        
        

        // Récupérer l'ID de la personne nouvellement insérée
        $prs_id_personne = $this->db->insertID();
        
        
        if (!$prs_id_personne) {
            throw new Exception('Échec de l\'insertion dans T_Personne_prs.');
        }

        // Insérer dans la table spécifique selon le rôle
        if ($role === 'admin') {
            $slogan = $saisie['slogan'] ?? '';
            
            $sql_role = "INSERT INTO T_Admin_adm(prs_id_personne, adm_solgan) VALUES (?, ?)";
            $this->db->query($sql_role, [$prs_id_personne, $slogan]);
            
        } elseif ($role === 'jury') {
            $biographie = $saisie['biographie'] ?? '';
            $url = $saisie['url'] ?? '';
            $droit = $saisie['droit'] ?? '';
            $expertise = $saisie['expertise'] ?? '';
 
  

            $sql_role = "INSERT INTO T_Juy_jry(prs_id_personne, jry_biographie, jry_URL, jry_droit, jry_domaine_Expertise)
                         VALUES (?, ?, ?, ?, ?)";
            $this->db->query($sql_role, [$prs_id_personne, $biographie, $url, $droit, $expertise]);


        }

        // Si tout est correct : Valider la transaction
        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw new Exception('Transaction échouée.');
        }

        return true;

    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        $this->db->transRollback();
        log_message('error', 'Erreur lors de la création de compte : ' . $e->getMessage());
        return false;
    }
}


    



//connexion
public function connect_compte($username, $password)
{
    // Récupérer les informations de l'utilisateur
    $sql = "SELECT prs_id_personne, prs_login, prs_nom, prs_prenom, 
                   prs_profil_actif, prs_role as user_type, prs_MDP, prs_salt
            FROM T_Personne_prs
            WHERE prs_login = ? AND prs_profil_actif = 1";

    $query = $this->db->query($sql, [$username]);

    if ($query->getNumRows() > 0) {
        $user = $query->getRow();

        // Calculer le hachage du mot de passe saisi avec le sel
        $mot_de_passe_hache = hash('sha256', $password . $user->prs_salt);

        // Comparer avec le hachage stocké
        if (hash_equals($user->prs_MDP, $mot_de_passe_hache)) {
            return $user; // Mot de passe valide, retour des infos utilisateur
        }
    }

    return false; // Échec de la connexion
}















// Fonction pour obtenir tous les concours
public function get_all_concours()
{
    $sql = "
        SELECT 
            con.con_id_concours, 
            con.con_nom_concours, 
            con.con_date_debut, 
            con.con_tps_candidature, 
            con.con_tps_pre_select, 
            con.con_tps_select, 
            adm.prs_nom AS resp_nom, 
            adm.prs_prenom AS resp_prenom, 
            donner_categorie(con.con_id_concours) AS categories,
            donner_jury(con.con_id_concours) AS juges,
            get_concours_dates(con.con_id_concours) AS dates,
            phase_actuelle(con.con_id_concours) AS phase_actuelle
        FROM T_CONCOURS_con con 
        JOIN T_Personne_prs adm ON con.adm_id_admin = adm.prs_id_personne
        ORDER BY 
    CASE phase_actuelle
        WHEN 'à venir' THEN 1
        WHEN 'inscription' THEN 2
        WHEN 'selection' THEN 3
        WHEN 'finale' THEN 4
        WHEN 'terminé' THEN 5
    END

    ";
    
    $query = $this->db->query($sql);
    return $query->getResultArray(); // Retourne les concours sous forme de tableau associatif
}





//fonction pour recuperer les details d'une candidature
public function get_candidature_details($cnd_CODEINSCRIT)
{
    // Requête pour récupérer les détails de la candidature en fonction de `cnd_CODEINSCRIT`
    $requete = "
    SELECT 
        cnd.cnd_idCANDIDATURE,
        cnd.cnd_CODECAND,
        cnd.cnd_CODEINSCRIT,
        cnd.cnd_Nom,
        cnd.cnd_Prenom,
        cnd.cnd_Presentation,
        cnd.cnd_MAIL,
        cnd.cnd_retenue,
        cnd.cnd_etat,
        cat.cat_nom_cat AS categorie,
        con.con_nom_concours AS concours
    FROM 
        T_CANDIDATURE_cnd cnd
    JOIN 
        T_Categorie_cat cat ON cnd.cat_id_cat = cat.cat_id_cat
    JOIN 
        T_CONCOURS_con con ON cnd.con_id_concours = con.con_id_concours
    WHERE 
        cnd.cnd_CODEINSCRIT = ?" ;

    $resultat = $this->db->query($requete, [$cnd_CODEINSCRIT]);
    $candidature = $resultat->getRow(); //fleshes
    //concatener les nom es docs avec les balises
    // Requête pour récupérer les documents associés à la candidature
    if ($candidature) {
        $requete_documents = "
        SELECT 
            doc.doc_idDocument,
            doc.doc_nomDocument,
            doc.doc_descriptionDocument
        FROM 
            T_DOCUMENT_doc doc
        WHERE 
            doc.cnd_idCANDIDATURE = " . $candidature->cnd_idCANDIDATURE;

        $documents_resultat = $this->db->query($requete_documents);
        $documents = $documents_resultat->getResultArray(); //crochets dans une boucle

        // Ajouter les documents à la candidature
        $candidature->documents = $documents;
    }

    return $candidature;
}



public function checkCandidature($cnd_CODECAND, $cnd_CODEINSCRIT)
{
    $requete = "SELECT cnd_CODEINSCRIT FROM T_CANDIDATURE_cnd 
                WHERE cnd_CODECAND = ? AND cnd_CODEINSCRIT = ?";
    $resultat = $this->db->query($requete, [$cnd_CODECAND, $cnd_CODEINSCRIT]);
    return $resultat->getRow();
}






//fonction pour supprimer la candidature (les documents seront supprimés automatiquement par le trigger)
public function delete_Candidature($cnd_idCANDIDATURE) {
   
    $result = $this->db->query("DELETE FROM T_CANDIDATURE_cnd WHERE cnd_idCANDIDATURE = ?", [$cnd_idCANDIDATURE]);
    
    return $result;
}

//fonction qui recureper les données des 2 profils
public function get_profile_data($username, $user_type) {
    if ($user_type === 'admin') {
        $sql = "SELECT p.*, a.adm_solgan
                FROM T_Personne_prs p
                JOIN T_Admin_adm a ON p.prs_id_personne = a.prs_id_personne
                WHERE p.prs_login = ?";
    } else {
        $sql = "SELECT p.*, j.jry_biographie, j.jry_URL, 
                       j.jry_droit, j.jry_domaine_Expertise
                FROM T_Personne_prs p
                JOIN T_Juy_jry j ON p.prs_id_personne = j.prs_id_personne
                WHERE p.prs_login = ?";
    }
    
    $query = $this->db->query($sql, [$username]);
    return $query->getRow();
}

//fonction qui modifie les données des 2 profils 
public function update_profile($username, $data, $role)
{
    // En fonction du rôle, on appelle la méthode appropriée
    if ($role === 'admin') {
        return $this->update_admin_profile($username, $data);
    } elseif ($role === 'jury') {
        return $this->update_jury_profile($username, $data);
    }
    
    // Si le rôle n'est pas reconnu, on retourne false
    log_message('error', 'Tentative de mise à jour avec un rôle non reconnu: ' . $role);
    return false;
}



//fonction qui modifie les données du profil admin
public function update_admin_profile($username, $data)
{
    
    $sql_update_person = "
        UPDATE T_Personne_prs
        SET prs_nom = ?, 
            prs_prenom = ?, 
            prs_MDP = ?,
            prs_salt = ?  /* Ajout du champ prs_salt */
        WHERE prs_login = ?
    ";

    $sql_update_admin = "
        UPDATE T_Admin_adm
        SET adm_solgan = ?
        WHERE prs_id_personne = (
            SELECT prs_id_personne FROM T_Personne_prs WHERE prs_login = ?
        )
    ";

    $this->db->transStart();

    
    $this->db->query($sql_update_person, [
        $data['prs_nom'],
        $data['prs_prenom'],
        $data['prs_MDP'] ?? null,
        $data['prs_salt'] ?? null,  
        $username
    ]);

    
    $this->db->query($sql_update_admin, [
        $data['adm_solgan'],
        $username
    ]);

    $this->db->transComplete();
    
 

    return $this->db->transStatus();
}

//fonction qui modifie les données du profil jury
public function update_jury_profile($username, $data)
{
    $sql_update_person = "
        UPDATE T_Personne_prs
        SET prs_nom = ?, 
            prs_prenom = ?, 
            prs_MDP = ?,
            prs_salt = ?  /* Ajout du champ prs_salt */
        WHERE prs_login = ?
    ";

    $sql_update_jury = "
        UPDATE T_Juy_jry
        SET jry_biographie = ?, 
            jry_URL = ?, 
            jry_droit = ?, 
            jry_domaine_Expertise = ?
        WHERE prs_id_personne = (
            SELECT prs_id_personne FROM T_Personne_prs WHERE prs_login = ?
        )
    ";

    $this->db->transStart();

 
    $this->db->query($sql_update_person, [
        $data['prs_nom'],
        $data['prs_prenom'],
        $data['prs_MDP'] ?? null,
        $data['prs_salt'] ?? null,   
        $username
    ]);

     $this->db->query($sql_update_jury, [
        $data['jry_biographie'],
        $data['jry_URL'],
        $data['jry_droit'],
        $data['jry_domaine_Expertise'],
        $username
    ]);

    $this->db->transComplete();
    


    return $this->db->transStatus();

}





//fonction qui modifie le mot de passe
public function update_password($username, $data, $role)
{
    $sql_update_person = "
        UPDATE T_Personne_prs
        SET prs_MDP = ?,
            prs_salt = ?
        WHERE prs_login = ?
    ";

    $this->db->transStart();
    
    $this->db->query($sql_update_person, [
        $data['prs_MDP'],
        $data['prs_salt'],
        $username
    ]);

    $this->db->transComplete();
    
 
    return $this->db->transStatus();
}

//fonction pour recuperer les concours d'un admin
public function get_admin_concours($admin_id = null) 
{
    $sql = "
        SELECT 
            con.con_id_concours,
            con.con_nom_concours,
            con.con_lieu,
            con.con_date_debut,
            con.con_discipline,
            con.con_tps_candidature,
            con.con_tps_pre_select,
            con.con_tps_select,
            adm.prs_id_personne AS admin_id,
            adm.prs_nom AS resp_nom,
            adm.prs_prenom AS resp_prenom,
            donner_categorie(con.con_id_concours) AS categories,
            donner_jury(con.con_id_concours) AS juges,
            get_concours_dates(con.con_id_concours) AS dates,
            phase_actuelle(con.con_id_concours) AS phase_actuelle
        FROM 
            T_CONCOURS_con con
            JOIN T_Personne_prs adm ON con.adm_id_admin = adm.prs_id_personne
    ";

     if ($admin_id) {
        $sql .= " WHERE adm.prs_id_personne = ?";
    }

    $sql .= " ORDER BY 
        con.con_date_debut ASC,
        CASE phase_actuelle(con.con_id_concours)
            WHEN 'à venir' THEN 1
            WHEN 'inscription' THEN 2
            WHEN 'selection' THEN 3
            WHEN 'finale' THEN 4
            WHEN 'terminé' THEN 5
        END";

     $query = $admin_id ? 
        $this->db->query($sql, [$admin_id]) : 
        $this->db->query($sql);

    return $query->getResultArray();
}


//fonction pour recuperer les concours d'un jury
public function getConcoursPourJury($juryId)
{
    $sql = "
    SELECT
    con.con_id_concours,
    con.con_nom_concours,
    con.con_date_debut,
    con.con_tps_candidature,
    con.con_tps_pre_select,
    con.con_tps_select,
    adm.prs_nom AS resp_nom,
    adm.prs_prenom AS resp_prenom,
    donner_categorie(con.con_id_concours) AS categories,
    donner_jury(con.con_id_concours) AS juges,
    get_concours_dates(con.con_id_concours) AS dates,
    phase_actuelle(con.con_id_concours) AS phase_actuelle
    FROM T_CONCOURS_con con
    JOIN T_Juy_jry_has_T_CONCOURS_con jc ON con.con_id_concours = jc.con_id_concours
    JOIN T_Personne_prs adm ON con.adm_id_admin = adm.prs_id_personne
    WHERE jc.jry_id_jury = ?
    ORDER BY
    CASE phase_actuelle
    WHEN 'à venir' THEN 1
    WHEN 'inscription' THEN 2
    WHEN 'selection' THEN 3
    WHEN 'finale' THEN 4
    WHEN 'terminé' THEN 5
    END
    ";
    $query = $this->db->query($sql, [$juryId]);
    return $query->getResultArray();  
}



//fonction pour recuperer les candidatures pre selectionnes
public function getCandidatsPreselectionnes($con_id_concours)
{
    $requete = "
    SELECT 
        cnd.cnd_idCANDIDATURE,
        cnd.cnd_CODECAND,
        cnd.cnd_CODEINSCRIT,
        cnd.cnd_Nom,
        cnd.cnd_Prenom,
        cnd.cnd_Presentation,
        cnd.cnd_MAIL,
        cnd.cnd_retenue,
        cnd.cnd_etat,
        cat.cat_nom_cat AS categorie,
        con.con_nom_concours AS concours
    FROM 
        T_CANDIDATURE_cnd cnd
    JOIN 
        T_Categorie_cat cat ON cnd.cat_id_cat = cat.cat_id_cat
    JOIN 
        T_CONCOURS_con con ON cnd.con_id_concours = con.con_id_concours
    WHERE 
        cnd.con_id_concours = ?
    AND 
        cnd.cnd_retenue = 1
        ORDER BY categorie";  

    $resultat = $this->db->query($requete, [$con_id_concours]);
    $candidats = $resultat->getResult();

     
    foreach ($candidats as $candidat) {
        $requete_documents = "
        SELECT 
            doc.doc_idDocument,
            doc.doc_nomDocument,
            doc.doc_descriptionDocument
        FROM 
            T_DOCUMENT_doc doc
        WHERE 
            doc.cnd_idCANDIDATURE = " . $candidat->cnd_idCANDIDATURE;

        $documents_resultat = $this->db->query($requete_documents);
        $candidat->documents = $documents_resultat->getResultArray();
    }

    return $candidats;
}










//fonction qui ajoute un concours


public function add_concours($data)
{
     $this->db->transStart();

     $result = $this->db->table('T_CONCOURS_con')->insert($data);

     if ($this->db->transComplete() === FALSE) {
 
        return false;
    }

    return $result;
}


//fonction qui verifie si le concours est de l'admin connecté
public function verifier_concours_admin($id_concours, $admin_id)
{
    $result = $this->db->table('T_CONCOURS_con')
        ->where('con_id_concours', $id_concours)
        ->where('adm_id_admin', $admin_id)
        ->countAllResults();
    
    return $result > 0;
}
//fonction qui verifie si le concours est a venir
public function est_concours_a_venir($id_concours)
{
    $sql = "SELECT phase_actuelle(?) = 'à venir' AS est_a_venir";
    $query = $this->db->query($sql, [$id_concours]);
    $result = $query->getRow();
    
     
    
    return $result ? $result->est_a_venir : false;
}

 //fonction qui supprime un concours
public function delete_concours($id_concours)
{
   // Appel de la procédure
   $sql = "CALL sp_supprimer_concours(?)";
   $this->db->query($sql, [$id_concours]);

   // Vérifier le nombre de lignes affectées
   return $this->db->affectedRows() > 0;
}











 





}
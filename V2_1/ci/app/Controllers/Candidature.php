<?php
namespace App\Controllers;
use App\Models\Db_model;

class Candidature extends BaseController
{
    public function rechercherCandidature()
    {
        $cnd_CODECAND = $this->request->getPost('cnd_CODECAND');
        $cnd_CODEINSCRIT = $this->request->getPost('cnd_CODEINSCRIT');
    
        $model = model(Db_model::class);
        $candidature = $model->checkCandidature($cnd_CODECAND, $cnd_CODEINSCRIT);
    
        if ($candidature) {
            $data['candidature'] = $model->get_candidature_details($cnd_CODEINSCRIT);


           return view('templates/haut', $data)
           . view('affichage_candidature', $data)
           . view('templates/bas');
            
            
        } else {
            return redirect()->back()->with('error', 'Candidature non trouvée');
        }
    }
    
    public function details()
    {
        $model = model(Db_model::class);
        
        // Récupérer l'ID de candidature depuis la session
        $cnd_CODEINSCRIT = session()->get('candidature_id');
        
        if (!$cnd_CODEINSCRIT) {
            $data['error_message'] = 'Accès non autorisé.';
        } else {
            // Récupérer les détails de la candidature
            $data['candidature'] = $model->get_candidature_details($cnd_CODEINSCRIT);
            
            // Supprimer l'ID de session après utilisation
            session()->remove('candidature_id');
        }
        
        // Passer les données à la vue
        return view('templates/haut', $data)
             . view('affichage_candidature', $data)
             . view('templates/bas');
    }
public function formulaireCandidature()
{
    return view('templates/haut')
         . view('formulaire_recherche_candidature')
         . view('templates/bas');
}    

public function supprimer() {
    $cnd_idCANDIDATURE = $this->request->getPost('cnd_idCANDIDATURE');
    $cnd_CODECAND = $this->request->getPost('cnd_CODECAND');
    $cnd_CODEINSCRIT = $this->request->getPost('cnd_CODEINSCRIT');

    // Vérifier que l'utilisateur a les bons codes
    $model = model(Db_model::class);
    $candidature = $model->checkCandidature($cnd_CODECAND, $cnd_CODEINSCRIT);

    if ($candidature) {
        // Supprimer la candidature (les documents seront supprimés par le trigger)
        $result = $model->delete_Candidature($cnd_idCANDIDATURE);

        if ($result) {
            return redirect()->to(base_url('/index.php/candidature/rechercher'))
                ->with('success', 'Votre candidature a été supprimée avec succès.');
        } else {
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la suppression.');
        }
    } else {
        return redirect()->back()
            ->with('error', 'Codes invalides. Suppression non autorisée.');
    }
}

public function galerieCandidats($con_id_concours)
{
    $model = model(Db_model::class);
    
    // Récupérer les détails du concours
    $concours = $model->db->table('T_CONCOURS_con')
        ->where('con_id_concours', $con_id_concours)
        ->get()
        ->getFirstRow();
    
    // Récupérer les candidats pré-sélectionnés
    $data['candidats'] = $model->getCandidatsPreselectionnes($con_id_concours);
    $data['concours'] = $concours;
    
    // Passer les données à la vue
    return view('templates/haut', $data)
         . view('galerie_candidats', $data)
         . view('templates/bas');
}
    
}





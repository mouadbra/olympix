<?php
namespace App\Controllers;
use App\Models\Db_model;
use CodeIgniter\Exceptions\PageNotFoundException;
class Actualite extends BaseController
{
public function __construct()
{
//...
}



public function afficher($numero = 0)
{
    $model = model(Db_model::class);

    // Redirige vers la liste si aucun ID n'est fourni
    if ($numero == 0) {
        return redirect()->to(base_url('/'));
    }

    // Récupère l'actualité spécifique
    $actualite = $model->get_actualite($numero);

    // Vérifie si l'actualité existe
    if (!$actualite) {
        return view('templates/haut', ['titre' => 'Erreur'])
            . "<p>Cette actualité n'existe pas.</p>"
            . view('templates/bas');
    }

    // Charge la vue avec les données
    $data = [
        'titre' => 'Détail de l\'actualité',
        'actualite' => $actualite,
    ];

    return view('templates/haut', $data)
        . view('affichage_actualite', $data)
        . view('templates/bas');
}








/*public function afficher($numero = 0)
{
    $model = model(Db_model::class);
    
    // Si aucun numéro n'est fourni, redirigez vers l'accueil
    if ($numero == 0) {
        return redirect()->to('/');
    }

    // Récupérer l'actualité spécifique
    $news = $model->get_actualite($numero);

    if (!$news) {
        $data['titre'] = 'Pas d’actualité trouvée !';
        $data['news'] = null;
    } else {
        $data['titre'] = 'Actualité :';
        $data['news'] = $news;
    }

    // Récupérer toutes les actualités (facultatif)
    $data['news2'] = $model->get_all_actualites();

    return view('templates/haut', $data)
         . view('affichage_actualite', $data)
         . view('templates/bas');
}

/*public function afficher($numero = 25)
{
$model = model(Db_model::class);
if ($numero == 0)
{
return redirect()->to('/');
}
else{
$data['titre'] = 'Actualité :';
$data['news'] = $model->get_actualite($numero);

$data['news2'] = $model->get_all_actualites();







return view('templates/haut', $data)
. view('affichage_actualite')
. view('templates/bas');
}
}*/










}
<?php
use App\Controllers\Accueil;
$routes->get('accueil/afficher', [Accueil::class, 'afficher']);
$routes->get('/', [Accueil::class, 'afficher']);

use App\Controllers\NouvellePage;
//url                                  code
//avec get
$routes->get('nouvellepage/afficher', [NouvellePage::class, 'afficher']);

use App\Controllers\Compte;
//$routes->get('compte/lister', [Compte::class, 'lister']);
$routes->get('admin/lister', [Compte::class, 'lister']);
//$routes->get('compte/creer', [Compte::class, 'creer']);
//$routes->post('compte/creer', [Compte::class, 'creer']);

$routes->get('compte/connecter', [Compte::class, 'connecter']);
$routes->post('compte/connecter', [Compte::class, 'connecter']);
$routes->get('compte/erreur', [Compte::class, 'connecter']);

$routes->get('compte/afficher_profil', 'Compte::afficher_profil');
$routes->get('compte/modifier_profil', 'Compte::modifier_profil');
$routes->post('compte/modifier_profil', 'Compte::modifier_profil');

$routes->get('admin/concours', 'Compte::liste_concours_adm');
$routes->get('jury/concours', 'Compte::afficherConcoursJury');
$routes->get('jury/galerie/(:num)', [Compte::class, 'afficherGalerieJury']);

//$routes->get('admin/details_concours/(:num)', 'Compte::details_concours/$1');
$routes->get('admin/creer_compte', 'Compte::creer');
$routes->post('admin/creer_compte', 'Compte::creer');
$routes->get('admin/ajout_concours', 'Compte::ajout_concours');
$routes->post('admin/ajout_concours', 'Compte::ajout_concours');
//$routes->post('admin/supprimer_concours/(:num)', 'Compte::supprimer_concours/$1');
$routes->post('admin/supprimer_concours/(:num)', 'Compte::supprimer_concours/$1');

$routes->get('compte/deconnecter', 'Compte::deconnecter');


use App\Controllers\Actualite;
$routes->get('actualite/afficher', [Actualite::class, 'afficher']);
$routes->get('actualite/afficher/(:num)', [Actualite::class, 'afficher']);


use App\Controllers\Concours;
$routes->get('concours/afficher', 'Concours::index');

use App\Controllers\Candidature;
$routes->get('candidature/afficher', [Candidature::class, 'details']);
//$routes->get('candidature/afficher/(:alphanum)', [Candidature::class, 'details']);
$routes->get('candidature/rechercher', [Candidature::class, 'formulaireCandidature']);
$routes->post('candidature/rechercher', [Candidature::class, 'rechercherCandidature']);
$routes->post('candidature/supprimer', [Candidature::class, 'supprimer']);
$routes->get('candidature/galerie/(:num)', [Candidature::class, 'galerieCandidats']);

<?php
namespace App\Controllers;

class NouvellePage extends BaseController
{
    public function afficher()
    {
        return view('templates/haut_admin') . view('nouvelle_page') . view('templates/bas_admin');
    }
    
}

<?php
namespace App\Controllers;
use App\Models\Db_model;
use CodeIgniter\Session\Session;
class Concours extends BaseController
{     protected $session;

    public function __construct()
{
    helper('form');
    $this->model = model(Db_model::class);
    $this->session = \Config\Services::session();
//...
}

    public function index()
    {
        
        $model = model(Db_model::class);
        
        
        $data['concours'] = $model->get_all_concours();

        
        return view('templates/haut', $data) 
             . view('affichage_concours', $data)       
             . view('templates/bas');        
    }













 
}
?>
<?php namespace Akravets\Survey\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Answers extends Controller
{
    public $implement = [        'Backend\Behaviors\ListController'    ];
    
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Akravets.Survey', 'main-menu-item', 'side-menu-item2');
    }
}

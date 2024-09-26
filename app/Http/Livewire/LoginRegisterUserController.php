<?php

namespace App\Http\Livewire;

use Livewire\Component;


class LoginRegisterUserController extends Component 
{
    public $vista = 1;
    
    public function render()
    {        
        if($this->vista == 1) return view ('auth.login');
        else return view ('auth.register');
    }
    public function CambiarVista()
    {
        $this->vista = 2;
    }
    public function doAction($action)
	{
        $this->vista = $action;
    }

}
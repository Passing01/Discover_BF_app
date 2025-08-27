<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Auth;

trait UsesLayout
{
    /**
     * Retourne le layout à utiliser en fonction du rôle de l'utilisateur
     *
     * @return string
     */
    protected function getLayout()
    {
        if (Auth::check() && Auth::user()->hasRole('admin')) {
            return 'layouts.admin';
        }
        
        return 'layouts.tourist';
    }
    
    /**
     * Affiche une vue avec le layout approprié
     *
     * @param string $view
     * @param array $data
     * @return \Illuminate\View\View
     */
    protected function view($view, $data = [])
    {
        return view($view, $data)->layout($this->getLayout());
    }
}

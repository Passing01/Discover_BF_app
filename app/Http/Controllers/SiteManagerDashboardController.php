<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SiteManagerDashboardController extends Controller
{
    protected $profileController;

    /**
     * Constructeur avec injection de dépendance
     */
    public function __construct(SiteManagerProfileController $profileController)
    {
        $this->profileController = $profileController;
    }

    /**
     * Affiche le tableau de bord du gestionnaire de sites
     * Redirige vers la méthode dashboard du SiteManagerProfileController
     */
    public function index()
    {
        return $this->profileController->dashboard();
    }
}

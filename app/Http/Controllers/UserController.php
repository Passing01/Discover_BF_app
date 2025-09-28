<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Afficher les informations d'un utilisateur spécifique
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        // Vérifier que l'utilisateur connecté a le droit de voir ces informations
        if (auth()->id() !== $user->id && !auth()->user()->hasRole('hotel_manager')) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        // Charger les relations nécessaires
        $user->load('profile');

        return response()->json($user);
    }
}

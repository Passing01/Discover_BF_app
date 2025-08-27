<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Community\CommunityPostController;
use App\Http\Controllers\Community\ReactionController;

// Routes de la communauté (accessibles uniquement aux utilisateurs connectés)
Route::middleware(['auth', 'verified'])->group(function () {
    // Routes pour les publications
    Route::resource('community/posts', CommunityPostController::class)->names('community.posts');
    
    // Routes pour les réactions
    Route::post('community/posts/{post}/react', [ReactionController::class, 'react'])
        ->name('community.posts.react');
    
    // Autres routes de la communauté...
});

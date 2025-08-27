<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Models\CommunityPost;
use App\Models\CommunityLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    /**
     * Basculer le like sur une publication
     */
    public function toggleLike(CommunityPost $post)
    {
        $user = Auth::user();
        
        // Vérifier si l'utilisateur a déjà aimé la publication
        $existingLike = CommunityLike::where('user_id', $user->id)
            ->where('post_id', $post->id)
            ->first();

        if ($existingLike) {
            // Supprimer le like
            $existingLike->delete();
            $post->decrement('likes_count');
            $liked = false;
        } else {
            // Ajouter un like
            $like = new CommunityLike([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'user_id' => $user->id,
                'post_id' => $post->id
            ]);
            $like->save();
            
            $post->increment('likes_count');
            $liked = true;
        }

        // Retourner la réponse au format JSON pour les requêtes AJAX
        if (request()->wantsJson()) {
            return response()->json([
                'likes_count' => $post->likes_count,
                'liked' => $liked
            ]);
        }

        return redirect()->back()
            ->with('success', $liked ? 'Publication aimée !' : 'Like retiré.');
    }
}

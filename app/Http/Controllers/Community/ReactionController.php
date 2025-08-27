<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Models\CommunityPost;
use App\Models\CommunityLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReactionController extends Controller
{
    /**
     * Gérer une réaction à une publication
     */
    public function react(CommunityPost $post, Request $request)
    {
        $request->validate([
            'reaction' => 'required|string|in:like,love,haha,wow,sad,angry',
        ]);

        $user = Auth::user();
        $reaction = $request->input('reaction');

        // Vérifier si l'utilisateur a déjà réagi à cette publication
        $existingReaction = $post->likes()
            ->where('user_id', $user->id)
            ->first();

        if ($existingReaction) {
            // Si la même réaction, on la supprime (toggle)
            if ($existingReaction->reaction === $reaction) {
                $existingReaction->delete();
                
                return response()->json([
                    'likes_count' => $post->likes()->count(),
                    'message' => 'Réaction supprimée avec succès',
                    'removed' => true
                ]);
            } else {
                // Sinon, on met à jour la réaction
                $existingReaction->update(['reaction' => $reaction]);
                
                return response()->json([
                    'likes_count' => $post->likes()->count(),
                    'message' => 'Réaction mise à jour avec succès',
                    'reaction' => $reaction
                ]);
            }
        } else {
            // Nouvelle réaction
            $like = new CommunityLike([
                'user_id' => $user->id,
                'reaction' => $reaction,
            ]);
            
            $post->likes()->save($like);
            
            return response()->json([
                'likes_count' => $post->likes()->count(),
                'message' => 'Réaction ajoutée avec succès',
                'reaction' => $reaction
            ]);
        }
    }
}

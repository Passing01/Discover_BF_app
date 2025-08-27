<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Models\CommunityPost;
use App\Models\CommunityComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, CommunityPost $post)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment = new CommunityComment([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'user_id' => Auth::id(),
            'post_id' => $post->id,
            'content' => $validated['content']
        ]);
        $comment->save();

        // Mettre à jour le compteur de commentaires
        $post->increment('comments_count');

        return redirect()->back()
            ->with('success', 'Commentaire ajouté avec succès !');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CommunityComment $comment)
    {
        $this->authorize('delete', $comment);
        
        $post = $comment->post;
        $comment->delete();

        // Mettre à jour le compteur de commentaires
        $post->decrement('comments_count');

        return redirect()->back()
            ->with('success', 'Commentaire supprimé avec succès !');
    }
}

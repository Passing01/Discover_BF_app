<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Models\CommunityPost;
use App\Models\CommunityLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CommunityPostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = CommunityPost::with(['user', 'comments.user'])
            ->withCount(['likes', 'comments'])
            ->latest();
            
        // Si l'utilisateur n'est pas admin, ne montrer que les publications actives
        if (!auth()->user() || !auth()->user()->hasRole('admin')) {
            $query->where('is_active', true);
        }

        $posts = $query->paginate(10);
        
        // Vérifier si la requête vient de la route tourist.community
        if (request()->routeIs('tourist.community')) {
            return view('tourist.community', compact('posts'));
        }

        return view('community.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('community.posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:2000',
            'image' => 'nullable|image|max:5120', // 5MB max
        ]);

        $post = new CommunityPost([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'user_id' => Auth::id(),
            'content' => $validated['content']
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('community/posts', 'public');
            $post->image = $path;
        }

        $post->save();

        return redirect()->route('community.posts.index')
            ->with('success', 'Publication créée avec succès !');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = CommunityPost::with(['user', 'comments.user', 'likes.user'])
            ->withCount(['likes', 'comments'])
            ->findOrFail($id);
            
        // Récupérer la réaction de l'utilisateur connecté s'il y en a une
        $userReaction = null;
        if (Auth::check()) {
            $userLike = $post->likes()->where('user_id', Auth::id())->first();
            if ($userLike) {
                $userReaction = $userLike->reaction;
            }
        }

        return view('community.posts.show', compact('post', 'userReaction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $post = CommunityPost::where('user_id', Auth::id())
            ->findOrFail($id);

        return view('community.posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $post = CommunityPost::where('user_id', Auth::id())
            ->findOrFail($id);

        $validated = $request->validate([
            'content' => 'required|string|max:2000',
            'image' => 'nullable|image|max:5120',
        ]);

        $post->content = $validated['content'];

        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            
            $path = $request->file('image')->store('community/posts', 'public');
            $post->image = $path;
        }

        $post->save();

        return redirect()->route('community.posts.show', $post->id)
            ->with('success', 'Publication mise à jour avec succès !');
    }

    /**
     * Supprimer définitivement une publication (uniquement pour les admins)
     */
    public function destroy(CommunityPost $post)
    {
        // Vérifier que l'utilisateur est un administrateur
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Action non autorisée. Seuls les administrateurs peuvent supprimer définitivement des publications.');
        }

        // Supprimer l'image si elle existe
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        $post->forceDelete();

        return redirect()->route('admin.community.posts.index')
            ->with('success', 'Publication supprimée définitivement avec succès !');
    }
    
    /**
     * Désactiver une publication (pour les modérateurs et administrateurs)
     */
    public function deactivate(CommunityPost $post)
    {
        // Vérifier que l'utilisateur est un administrateur
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Action non autorisée. Seuls les administrateurs peuvent désactiver des publications.');
        }
        
        // Vérifier que la publication n'est pas déjà désactivée
        if (!$post->is_active) {
            return back()->with('warning', 'Cette publication est déjà désactivée.');
        }
        
        $post->deactivate(auth()->id());
        
        return back()->with('success', 'La publication a été désactivée avec succès.');
    }
    
    /**
     * Réactiver une publication (pour les administrateurs)
     */
    public function activate(CommunityPost $post)
    {
        // Vérifier que l'utilisateur est un administrateur
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Action non autorisée. Seuls les administrateurs peuvent réactiver des publications.');
        }
        
        // Vérifier que la publication n'est pas déjà active
        if ($post->is_active) {
            return back()->with('warning', 'Cette publication est déjà active.');
        }
        
        $post->activate();
        
        return back()->with('success', 'La publication a été réactivée avec succès.');
    }
    
    /**
     * Afficher les publications désactivées (pour les administrateurs)
     */
    public function trashed()
    {
        // Vérifier que l'utilisateur est un administrateur
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Action non autorisée.');
        }
        
        $posts = CommunityPost::with(['user', 'deletedBy'])
            ->where('is_active', false)
            ->latest()
            ->paginate(10);
            
        return $this->view('admin.community.posts.trashed', compact('posts'));
    }
}

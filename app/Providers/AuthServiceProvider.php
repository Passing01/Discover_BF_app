<?php

namespace App\Providers;

use App\Models\CommunityPost;
use App\Models\CommunityComment;
use App\Policies\CommunityPostPolicy;
use App\Policies\CommunityCommentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        CommunityPost::class => CommunityPostPolicy::class,
        CommunityComment::class => CommunityCommentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}

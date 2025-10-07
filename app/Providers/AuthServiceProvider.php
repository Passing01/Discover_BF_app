<?php

namespace App\Providers;

use App\Models\CommunityPost;
use App\Models\CommunityComment;
use App\Models\Hotel;
use App\Models\Restaurant;
use App\Models\RestaurantReservation;
use App\Policies\CommunityPostPolicy;
use App\Policies\CommunityCommentPolicy;
use App\Policies\HotelCustomerPolicy;
use App\Policies\ClientPolicy;
use App\Policies\RestaurantPolicy;
use App\Policies\ReservationPolicy;
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
        \App\Models\Site\Event::class => \App\Policies\EventPolicy::class,
        Hotel::class => HotelCustomerPolicy::class,
        User::class => ClientPolicy::class,
        Restaurant::class => RestaurantPolicy::class,
        RestaurantReservation::class => ReservationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}

<?php

namespace App\Http\Controllers\landing_page;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Models\Event;
use App\Models\Ad;
use App\Models\User;
use App\Models\Guide;
use App\Models\HotelBooking;
use App\Models\EventBooking;
use App\Models\SiteBooking;
use App\Models\CommunityPost;

class LandingPageController extends Controller
{
    public function index()
    {
        $popularActivities = Site::query()
            ->where('is_active', true)
            ->latest()
            ->take(4)
            ->get();

        $popularDestinations = Site::query()
            ->where('is_active', true)
            ->inRandomOrder()
            ->take(4)
            ->get();

        $upcomingEvents = Event::query()
            ->orderBy('start_date')
            ->take(3)
            ->get();

        $clientAds = Ad::activeFor('clients')
            ->orderByDesc('weight')
            ->take(6)
            ->get();

        $featureSites = Site::query()
            ->where('is_active', true)
            ->latest()
            ->take(4)
            ->get();

        $serviceSites = Site::query()
            ->where('is_active', true)
            ->inRandomOrder()
            ->take(6)
            ->get();

        $aboutSite = Site::query()
            ->where('is_active', true)
            ->latest()
            ->first();

        $stats = [
            'happy_clients' => User::count(),
            'projects' => Event::count(),
            'hours_support' => (HotelBooking::count() + EventBooking::count() + SiteBooking::count()),
            'hard_workers' => Guide::count(),
        ];

        $blogPosts = CommunityPost::query()
            ->active()
            ->latest()
            ->take(3)
            ->get();

        return view('welcome', [
            'popularActivities' => $popularActivities,
            'popularDestinations' => $popularDestinations,
            'upcomingEvents' => $upcomingEvents,
            'clientAds' => $clientAds,
            'featureSites' => $featureSites,
            'serviceSites' => $serviceSites,
            'aboutSite' => $aboutSite,
            'stats' => $stats,
            'blogPosts' => $blogPosts,
        ]);
    }
}

@extends('layouts.site')

@section('content')
<div class="container py-4">
    <h1 class="mb-3">{{ __('Dashboard') }}</h1>
    <div class="card shadow-sm">
        <div class="card-body">
            {{ __("You're logged in!") }}
        </div>
    </div>
    
</div>
@endsection

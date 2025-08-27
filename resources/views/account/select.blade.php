@extends('layouts.tourist')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Select an Account</span>
                    <a href="{{ route('account.create') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-plus me-1"></i> New Account
                    </a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    @if ($accounts->isEmpty())
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-building fa-4x text-muted"></i>
                            </div>
                            <h4>No accounts found</h4>
                            <p class="text-muted">You don't have access to any professional accounts yet.</p>
                            <a href="{{ route('account.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Create New Account
                            </a>
                        </div>
                    @else
                        <div class="list-group">
                            @foreach ($accounts as $account)
                                <div class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <div>
                                            <h5 class="mb-1">{{ $account->business_name }}</h5>
                                            <p class="mb-1 text-muted">
                                                <i class="fas fa-map-marker-alt me-1"></i> 
                                                {{ $account->city }}, {{ $account->country }}
                                                <span class="mx-2">•</span>
                                                <i class="fas fa-users me-1"></i>
                                                {{ $account->users_count }} member{{ $account->users_count > 1 ? 's' : '' }}
                                            </p>
                                            <div class="mt-2">
                                                @foreach($account->features as $feature)
                                                    @if($feature->is_active)
                                                        <span class="badge bg-primary me-1 mb-1">
                                                            {{ ucfirst($feature->feature) }}
                                                        </span>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="mb-2">
                                                @if($account->status === 'active')
                                                    <span class="badge bg-success">Active</span>
                                                @elseif($account->status === 'pending')
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                @elseif($account->status === 'suspended')
                                                    <span class="badge bg-danger">Suspended</span>
                                                @endif
                                            </div>
                                            <div class="btn-group">
                                                @if(auth()->user()->current_account_id === $account->id)
                                                    <span class="btn btn-sm btn-outline-success disabled">
                                                        <i class="fas fa-check-circle me-1"></i> Current
                                                    </span>
                                                @else
                                                    <form action="{{ route('account.switch', $account) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-sign-in-alt me-1"></i> Switch
                                                        </button>
                                                    </form>
                                                @endif
                                                
                                                <div class="btn-group
                                                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-cog"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    @can('update', $account)
                                                        <li>
                                                            <a class="dropdown-item" 
                                                               href="{{ route('account.edit', $account) }}">
                                                                <i class="fas fa-edit me-2"></i> Edit Account
                                                            </a>
                                                        </li>
                                                    @endcan
                                                    @can('manageUsers', $account)
                                                        <li>
                                                            <a class="dropdown-item" 
                                                               href="{{ route('account.users', $account) }}">
                                                                <i class="fas fa-users me-2"></i> Team Members
                                                            </a>
                                                        </li>
                                                    @endcan
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="#" 
                                                           onclick="event.preventDefault(); 
                                                           if(confirm('Are you sure you want to leave this account?')) { 
                                                               document.getElementById('leave-account-{{ $account->id }}').submit(); 
                                                           }">
                                                            <i class="fas fa-sign-out-alt me-2"></i> Leave Account
                                                        </a>
                                                        <form id="leave-account-{{ $account->id }}" 
                                                              action="{{ route('account.leave', $account) }}" 
                                                              method="POST" style="display: none;">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $accounts->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

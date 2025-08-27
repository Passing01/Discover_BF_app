@extends('layouts.tourist')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Account: {{ $account->business_name }}</span>
                    <div>
                        @can('update', $account)
                            <a href="{{ route('account.edit', $account) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit me-1"></i> Edit Account
                            </a>
                        @endcan
                        <a href="{{ route('account.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Accounts
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Account Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <h5 class="mb-1">{{ $account->business_name }}</h5>
                                        @if($account->legal_name && $account->legal_name !== $account->business_name)
                                            <p class="text-muted mb-2">{{ $account->legal_name }}</p>
                                        @endif
                                        
                                        @if($account->description)
                                            <p class="mb-3">{{ $account->description }}</p>
                                        @endif
                                        
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="me-2">
                                                <i class="fas fa-circle {{ $account->is_active ? 'text-success' : 'text-danger' }}"></i>
                                            </span>
                                            <span class="text-muted">
                                                {{ $account->is_active ? 'Active' : 'Inactive' }} Account
                                            </span>
                                        </div>
                                        
                                        @if($account->created_at)
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="me-2">
                                                    <i class="far fa-calendar-alt text-muted"></i>
                                                </span>
                                                <span class="text-muted">
                                                    Member since {{ $account->created_at->format('M d, Y') }}
                                                </span>
                                            </div>
                                        @endif
                                        
                                        @if($account->website)
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="me-2">
                                                    <i class="fas fa-globe text-muted"></i>
                                                </span>
                                                <a href="{{ Str::startsWith($account->website, 'http') ? $account->website : 'https://' . $account->website }}" 
                                                   target="_blank" class="text-decoration-none">
                                                    {{ $account->website }}
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="mt-4">
                                        <h6 class="border-bottom pb-2 mb-3">Contact Information</h6>
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="me-2">
                                                <i class="fas fa-envelope text-muted"></i>
                                            </span>
                                            <a href="mailto:{{ $account->email }}" class="text-decoration-none">
                                                {{ $account->email }}
                                            </a>
                                        </div>
                                        
                                        @if($account->phone)
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="me-2">
                                                    <i class="fas fa-phone text-muted"></i>
                                                </span>
                                                <a href="tel:{{ $account->phone }}" class="text-decoration-none">
                                                    {{ $account->phone }}
                                                </a>
                                            </div>
                                        @endif
                                        
                                        <div class="d-flex align-items-start mb-2">
                                            <span class="me-2 mt-1">
                                                <i class="fas fa-map-marker-alt text-muted"></i>
                                            </span>
                                            <div>
                                                <div>{{ $account->address }}</div>
                                                <div>
                                                    @if($account->city)
                                                        {{ $account->city }},
                                                    @endif
                                                    @if($account->state)
                                                        {{ $account->state }}
                                                    @endif
                                                    @if($account->postal_code)
                                                        {{ $account->postal_code }}
                                                    @endif
                                                </div>
                                                <div>
                                                    {{ $account->country ? \App\Helpers\Countries::getCountryName($account->country) : '' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Account Status</h6>
                                </div>
                                <div class="card-body">
                                    @if($account->verification)
                                        @php
                                            $verification = $account->verification;
                                            $statusClass = [
                                                'pending' => 'warning',
                                                'verified' => 'success',
                                                'rejected' => 'danger',
                                                'expired' => 'secondary'
                                            ][$verification->status] ?? 'secondary';
                                        @endphp
                                        
                                        <div class="alert alert-{{ $statusClass }} mb-4">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>Verification Status: </strong>
                                                    <span class="text-capitalize">{{ $verification->status }}</span>
                                                    
                                                    @if($verification->status === 'verified' && $verification->reviewed_at)
                                                        <div class="mt-1">
                                                            <small>
                                                                Verified on {{ $verification->reviewed_at->format('M d, Y') }}
                                                            </small>
                                                        </div>
                                                    @elseif($verification->status === 'rejected')
                                                        <div class="mt-2">
                                                            <strong>Reason: </strong> 
                                                            {{ $verification->rejection_reason ?? 'No reason provided' }}
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                                @if($verification->status === 'rejected' || $verification->status === 'expired')
                                                    <a href="{{ route('account.verification.create', $account) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        Resubmit Verification
                                                    </a>
                                                @elseif(!$verification->status === 'verified')
                                                    <a href="{{ route('account.verification.show', $account) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        View Details
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-info mb-4">
                                            <p>Your account has not been verified yet. Please submit the required documents to verify your business.</p>
                                            <a href="{{ route('account.verification.create', $account) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-check-circle me-1"></i> Start Verification
                                            </a>
                                        </div>
                                    @endif
                                    
                                    <div class="mb-4">
                                        <h6 class="border-bottom pb-2 mb-3">Active Modules</h6>
                                        @if($account->features->where('is_active', true)->isNotEmpty())
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach($account->features->where('is_active', true) as $feature)
                                                    <span class="badge bg-primary">
                                                        {{ ucfirst($feature->feature) }}
                                                        @if($feature->expires_at)
                                                            (Expires: {{ $feature->expires_at->format('M d, Y') }})
                                                        @endif
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-muted mb-0">No active modules. Please contact support to activate modules.</p>
                                        @endif
                                    </div>
                                    
                                    <div>
                                        <h6 class="border-bottom pb-2 mb-3">Billing Information</h6>
                                        @if($account->billing)
                                            <div class="mb-2">
                                                <strong>Billing Cycle:</strong> 
                                                {{ ucfirst($account->billing->billing_cycle) }}
                                            </div>
                                            <div class="mb-2">
                                                <strong>Payment Method:</strong> 
                                                {{ ucfirst(str_replace('_', ' ', $account->billing->payment_method)) }}
                                            </div>
                                            @if($account->billing->next_billing_date)
                                                <div class="mb-2">
                                                    <strong>Next Billing Date:</strong> 
                                                    {{ $account->billing->next_billing_date->format('M d, Y') }}
                                                </div>
                                            @endif
                                            <div class="mt-3">
                                                <a href="{{ route('account.billing', $account) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-credit-card me-1"></i> Manage Billing
                                                </a>
                                            </div>
                                        @else
                                            <p class="text-muted mb-0">No billing information available.</p>
                                            @can('update', $account)
                                                <a href="{{ route('account.billing', $account) }}" class="btn btn-sm btn-primary mt-2">
                                                    <i class="fas fa-plus me-1"></i> Add Billing Information
                                                </a>
                                            @endcan
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Team Members</h6>
                                </div>
                                <div class="card-body">
                                    @if($account->users->isNotEmpty())
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Email</th>
                                                        <th>Role</th>
                                                        <th>Status</th>
                                                        <th>Joined</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($account->users as $user)
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    @if($user->profile_photo_path)
                                                                        <img src="{{ asset('storage/' . $user->profile_photo_path) }}" 
                                                                             class="rounded-circle me-2" 
                                                                             width="32" 
                                                                             height="32" 
                                                                             alt="{{ $user->name }}">
                                                                    @else
                                                                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" 
                                                                             style="width: 32px; height: 32px;">
                                                                            {{ substr($user->name, 0, 1) }}
                                                                        </div>
                                                                    @endif
                                                                    <div>
                                                                        <div>{{ $user->name }}</div>
                                                                        @if($user->id === $account->owner_id)
                                                                            <small class="text-muted">Account Owner</small>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>{{ $user->email }}</td>
                                                            <td>
                                                                @php
                                                                    $role = $user->pivot->role;
                                                                    $roleBadgeClass = [
                                                                        'owner' => 'bg-primary',
                                                                        'admin' => 'bg-success',
                                                                        'manager' => 'bg-info',
                                                                        'member' => 'bg-secondary'
                                                                    ][$role] ?? 'bg-secondary';
                                                                @endphp
                                                                <span class="badge {{ $roleBadgeClass }}">
                                                                    {{ ucfirst($role) }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                @if($user->is_active)
                                                                    <span class="badge bg-success">Active</span>
                                                                @else
                                                                    <span class="badge bg-secondary">Inactive</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $user->pivot->created_at->format('M d, Y') }}</td>
                                                            <td>
                                                                @can('update', $account)
                                                                    @if($user->id !== $account->owner_id)
                                                                        <div class="btn-group">
                                                                            <button type="button" class="btn btn-sm btn-outline-secondary" 
                                                                                    data-bs-toggle="modal" 
                                                                                    data-bs-target="#editMemberModal{{ $user->id }}"
                                                                                    title="Edit Member">
                                                                                <i class="fas fa-edit"></i>
                                                                            </button>
                                                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                                    onclick="if(confirm('Are you sure you want to remove this member?')) { document.getElementById('remove-member-{{ $user->id }}').submit(); }"
                                                                                    title="Remove Member">
                                                                                <i class="fas fa-user-minus"></i>
                                                                            </button>
                                                                            <form id="remove-member-{{ $user->id }}" 
                                                                                  action="{{ route('account.members.remove', [$account, $user]) }}" 
                                                                                  method="POST" 
                                                                                  style="display: none;">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                            </form>
                                                                        </div>
                                                                    @else
                                                                        <span class="text-muted">Owner</span>
                                                                    @endif
                                                                @endcan
                                                            </td>
                                                        </tr>
                                                        
                                                        <!-- Edit Member Modal -->
                                                        @can('update', $account)
                                                            @if($user->id !== $account->owner_id)
                                                                <div class="modal fade" id="editMemberModal{{ $user->id }}" tabindex="-1" 
                                                                     aria-labelledby="editMemberModalLabel{{ $user->id }}" aria-hidden="true">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content">
                                                                            <form action="{{ route('account.members.update', [$account, $user]) }}" method="POST">
                                                                                @csrf
                                                                                @method('PUT')
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title" id="editMemberModalLabel{{ $user->id }}">
                                                                                        Edit Team Member
                                                                                    </h5>
                                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <div class="mb-3">
                                                                                        <label for="role{{ $user->id }}" class="form-label">Role</label>
                                                                                        <select class="form-select" id="role{{ $user->id }}" name="role" required>
                                                                                            <option value="admin" {{ $user->pivot->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                                                                            <option value="manager" {{ $user->pivot->role === 'manager' ? 'selected' : '' }}>Manager</option>
                                                                                            <option value="member" {{ $user->pivot->role === 'member' ? 'selected' : '' }}>Member</option>
                                                                                        </select>
                                                                                    </div>
                                                                                    
                                                                                    <div class="form-check form-switch">
                                                                                        <input class="form-check-input" type="checkbox" 
                                                                                               id="is_active{{ $user->id }}" name="is_active" 
                                                                                               value="1" {{ $user->is_active ? 'checked' : '' }}>
                                                                                        <label class="form-check-label" for="is_active{{ $user->id }}">
                                                                                            Account is active
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                                    <button type="submit" class="btn btn-primary">
                                                                                        <i class="fas fa-save me-1"></i> Save Changes
                                                                                    </button>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endcan
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted mb-0">No team members found.</p>
                                    @endif
                                    
                                    @can('update', $account)
                                        <div class="mt-3">
                                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#inviteMemberModal">
                                                <i class="fas fa-user-plus me-1"></i> Invite Team Member
                                            </button>
                                        </div>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Invite Member Modal -->
@can('update', $account)
<div class="modal fade" id="inviteMemberModal" tabindex="-1" aria-labelledby="inviteMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('account.members.invite', $account) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="inviteMemberModalLabel">Invite Team Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address *</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="form-text">An invitation will be sent to this email address.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="role" class="form-label">Role *</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="admin">Admin</option>
                            <option value="manager" selected>Manager</option>
                            <option value="member">Member</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="message" class="form-label">Personal Message (Optional)</label>
                        <textarea class="form-control" id="message" name="message" rows="3" 
                                  placeholder="Add a personal message to include in the invitation"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-1"></i> Send Invitation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

@push('scripts')
<script>
    // Enable tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
@endsection

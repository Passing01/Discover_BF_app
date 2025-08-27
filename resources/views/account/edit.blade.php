@extends('layouts.tourist')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Edit Account: {{ $account->business_name }}</span>
                    <div>
                        <a href="{{ route('account.show', $account) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Account
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('account.update', $account) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <ul class="nav nav-tabs mb-4" id="accountTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="business-tab" data-bs-toggle="tab" 
                                        data-bs-target="#business" type="button" role="tab" 
                                        aria-controls="business" aria-selected="true">
                                    Business Info
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="billing-tab" data-bs-toggle="tab" 
                                        data-bs-target="#billing" type="button" role="tab" 
                                        aria-controls="billing" aria-selected="false">
                                    Billing
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="modules-tab" data-bs-toggle="tab" 
                                        data-bs-target="#modules" type="button" role="tab" 
                                        aria-controls="modules" aria-selected="false">
                                    Modules
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="verification-tab" data-bs-toggle="tab" 
                                        data-bs-target="#verification" type="button" role="tab" 
                                        aria-controls="verification" aria-selected="false">
                                    Verification
                                </button>
                            </li>
                        </ul>
                        
                        <div class="tab-content" id="accountTabsContent">
                            <!-- Business Information Tab -->
                            @include('account.partials.business-info')
                            
                            <!-- Billing Information Tab -->
                            @include('account.partials.billing-info')
                            
                            <!-- Modules Tab -->
                            @include('account.partials.modules')
                            
                            <!-- Verification Tab -->
                            @include('account.partials.verification')
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary" onclick="window.history.back()">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </button>
                            
                            <div>
                                <button type="reset" class="btn btn-secondary me-2">
                                    <i class="fas fa-undo me-1"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Handle same as business checkbox for billing info
    document.addEventListener('DOMContentLoaded', function() {
        const sameAsBusinessCheckbox = document.getElementById('sameAsBusiness');
        const billingSameAsBusinessInput = document.getElementById('billingSameAsBusiness');
        const billingInfoSection = document.getElementById('billingInfo');
        
        if (sameAsBusinessCheckbox) {
            sameAsBusinessCheckbox.addEventListener('change', function() {
                const isChecked = this.checked;
                billingSameAsBusinessInput.value = isChecked ? '1' : '0';
                
                // Toggle disabled state of all inputs in billing info
                const billingInputs = billingInfoSection.querySelectorAll('input, select, textarea');
                billingInputs.forEach(input => {
                    input.disabled = isChecked;
                    if (input.required) {
                        input.required = !isChecked;
                    }
                });
                
                // If checked, copy business info to billing info
                if (isChecked) {
                    document.getElementById('billing_name').value = document.getElementById('business_name').value;
                    document.getElementById('billing_email').value = document.getElementById('email').value;
                    document.getElementById('billing_phone').value = document.getElementById('phone').value;
                    document.getElementById('billing_address').value = document.getElementById('address').value;
                    document.getElementById('billing_city').value = document.getElementById('city').value;
                    document.getElementById('billing_state').value = document.getElementById('state').value;
                    document.getElementById('billing_postal_code').value = document.getElementById('postal_code').value;
                    document.getElementById('billing_country').value = document.getElementById('country').value;
                }
            });
            
            // Trigger change on load in case the checkbox is pre-checked
            sameAsBusinessCheckbox.dispatchEvent(new Event('change'));
        }
    });
</script>
@endpush
@endsection

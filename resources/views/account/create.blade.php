@extends('layouts.tourist')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Create Professional Account</div>

                <div class="card-body">
                    <p class="lead">Set up your professional account to get started with our platform.</p>
                    
                    <form method="POST" action="{{ route('account.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">Business Information</h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="business_name" class="form-label">Business Name *</label>
                                    <input type="text" class="form-control @error('business_name') is-invalid @enderror" 
                                           id="business_name" name="business_name" 
                                           value="{{ old('business_name') }}" required>
                                    @error('business_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="legal_name" class="form-label">Legal Name *</label>
                                    <input type="text" class="form-control @error('legal_name') is-invalid @enderror" 
                                           id="legal_name" name="legal_name" 
                                           value="{{ old('legal_name') }}" required>
                                    <small class="text-muted">Official business name as registered</small>
                                    @error('legal_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="tax_number" class="form-label">Tax Identification Number</label>
                                    <input type="text" class="form-control @error('tax_number') is-invalid @enderror" 
                                           id="tax_number" name="tax_number" 
                                           value="{{ old('tax_number') }}">
                                    @error('tax_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="registration_number" class="form-label">Registration Number</label>
                                    <input type="text" class="form-control @error('registration_number') is-invalid @enderror" 
                                           id="registration_number" name="registration_number" 
                                           value="{{ old('registration_number') }}">
                                    @error('registration_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="website" class="form-label">Website</label>
                                <div class="input-group">
                                    <span class="input-group-text">https://</span>
                                    <input type="text" class="form-control @error('website') is-invalid @enderror" 
                                           id="website" name="website" 
                                           value="{{ old('website') }}">
                                    @error('website')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">Contact Information</h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" 
                                           value="{{ old('email', auth()->user()->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone *</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" 
                                           value="{{ old('phone', auth()->user()->phone) }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="address" class="form-label">Address *</label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                       id="address" name="address" 
                                       value="{{ old('address') }}" required>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="city" class="form-label">City *</label>
                                    <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                           id="city" name="city" 
                                           value="{{ old('city') }}" required>
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-3 mb-3">
                                    <label for="state" class="form-label">State/Province</label>
                                    <input type="text" class="form-control @error('state') is-invalid @enderror" 
                                           id="state" name="state" 
                                           value="{{ old('state') }}">
                                    @error('state')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-3 mb-3">
                                    <label for="postal_code" class="form-label">Postal Code *</label>
                                    <input type="text" class="form-control @error('postal_code') is-invalid @enderror" 
                                           id="postal_code" name="postal_code" 
                                           value="{{ old('postal_code') }}" required>
                                    @error('postal_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="country" class="form-label">Country *</label>
                                <select class="form-select @error('country') is-invalid @enderror" 
                                        id="country" name="country" required>
                                    <option value="">Select Country</option>
                                    @foreach(\App\Helpers\Countries::getCountries() as $code => $name)
                                        <option value="{{ $code }}" {{ old('country') == $code ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">Billing Information</h5>
                            
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="sameAsBusiness" checked>
                                <label class="form-check-label" for="sameAsBusiness">Same as business information</label>
                            </div>
                            
                            <div id="billingInfo">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="billing_name" class="form-label">Billing Name *</label>
                                        <input type="text" class="form-control @error('billing_name') is-invalid @enderror" 
                                               id="billing_name" name="billing_name" 
                                               value="{{ old('billing_name') }}" required>
                                        @error('billing_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="billing_email" class="form-label">Billing Email *</label>
                                        <input type="email" class="form-control @error('billing_email') is-invalid @enderror" 
                                               id="billing_email" name="billing_email" 
                                               value="{{ old('billing_email', auth()->user()->email) }}" required>
                                        @error('billing_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="billing_address" class="form-label">Billing Address *</label>
                                    <input type="text" class="form-control @error('billing_address') is-invalid @enderror" 
                                           id="billing_address" name="billing_address" 
                                           value="{{ old('billing_address') }}" required>
                                    @error('billing_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="billing_city" class="form-label">City *</label>
                                        <input type="text" class="form-control @error('billing_city') is-invalid @enderror" 
                                               id="billing_city" name="billing_city" 
                                               value="{{ old('billing_city') }}" required>
                                        @error('billing_city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-3 mb-3">
                                        <label for="billing_state" class="form-label">State/Province</label>
                                        <input type="text" class="form-control @error('billing_state') is-invalid @enderror" 
                                               id="billing_state" name="billing_state" 
                                               value="{{ old('billing_state') }}">
                                        @error('billing_state')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-3 mb-3">
                                        <label for="billing_postal_code" class="form-label">Postal Code *</label>
                                        <input type="text" class="form-control @error('billing_postal_code') is-invalid @enderror" 
                                               id="billing_postal_code" name="billing_postal_code" 
                                               value="{{ old('billing_postal_code') }}" required>
                                        @error('billing_postal_code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="billing_country" class="form-label">Country *</label>
                                    <select class="form-select @error('billing_country') is-invalid @enderror" 
                                            id="billing_country" name="billing_country" required>
                                        <option value="">Select Country</option>
                                        @foreach(\App\Helpers\Countries::getCountries() as $code => $name)
                                            <option value="{{ $code }}" {{ old('billing_country') == $code ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('billing_country')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="billing_phone" class="form-label">Billing Phone *</label>
                                    <input type="tel" class="form-control @error('billing_phone') is-invalid @enderror" 
                                           id="billing_phone" name="billing_phone" 
                                           value="{{ old('billing_phone', auth()->user()->phone) }}" required>
                                    @error('billing_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">Modules</h5>
                            <p class="text-muted mb-3">Select the modules you want to activate for your account. You can change these later.</p>
                            
                            <div class="row">
                                @foreach($features as $key => $feature)
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" 
                                                           id="feature-{{ $key }}" name="features[]" 
                                                           value="{{ $key }}">
                                                    <label class="form-check-label fw-bold" for="feature-{{ $key }}">
                                                        {{ $feature['name'] }}
                                                    </label>
                                                    <p class="text-muted mb-0">{{ $feature['description'] }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                Create Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Copy business info to billing info when checkbox is checked
    document.getElementById('sameAsBusiness').addEventListener('change', function() {
        const billingInfo = document.getElementById('billingInfo');
        const inputs = billingInfo.getElementsByTagName('input');
        
        if (this.checked) {
            // Disable all inputs in billing info
            for (let input of inputs) {
                input.disabled = true;
            }
            
            // Copy values from business info to billing info
            document.getElementById('billing_name').value = document.getElementById('business_name').value;
            document.getElementById('billing_email').value = document.getElementById('email').value;
            document.getElementById('billing_phone').value = document.getElementById('phone').value;
            document.getElementById('billing_address').value = document.getElementById('address').value;
            document.getElementById('billing_city').value = document.getElementById('city').value;
            document.getElementById('billing_state').value = document.getElementById('state').value;
            document.getElementById('billing_postal_code').value = document.getElementById('postal_code').value;
            document.getElementById('billing_country').value = document.getElementById('country').value;
        } else {
            // Enable all inputs in billing info
            for (let input of inputs) {
                input.disabled = false;
            }
        }
    });
    
    // Trigger change event on page load
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('sameAsBusiness').dispatchEvent(new Event('change'));
    });
</script>
@endpush
@endsection

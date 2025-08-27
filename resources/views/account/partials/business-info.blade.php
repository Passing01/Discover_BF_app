<div class="tab-pane fade show active" id="business" role="tabpanel" aria-labelledby="business-tab">
    <div class="mb-4">
        <h5 class="border-bottom pb-2 mb-3">Business Information</h5>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="business_name" class="form-label">Business Name *</label>
                <input type="text" class="form-control @error('business_name') is-invalid @enderror" 
                       id="business_name" name="business_name" 
                       value="{{ old('business_name', $account->business_name) }}" required>
                @error('business_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="legal_name" class="form-label">Legal Name *</label>
                <input type="text" class="form-control @error('legal_name') is-invalid @enderror" 
                       id="legal_name" name="legal_name" 
                       value="{{ old('legal_name', $account->legal_name) }}" required>
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
                       value="{{ old('tax_number', $account->tax_number) }}">
                @error('tax_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="registration_number" class="form-label">Registration Number</label>
                <input type="text" class="form-control @error('registration_number') is-invalid @enderror" 
                       id="registration_number" name="registration_number" 
                       value="{{ old('registration_number', $account->registration_number) }}">
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
                       value="{{ old('website', str_replace('https://', '', $account->website)) }}">
                @error('website')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="mb-3">
            <label for="description" class="form-label">Business Description</label>
            <textarea class="form-control @error('description') is-invalid @enderror" 
                      id="description" name="description" 
                      rows="3">{{ old('description', $account->description) }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    
    <div class="mb-4">
        <h5 class="border-bottom pb-2 mb-3">Contact Information</h5>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email *</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                       id="email" name="email" 
                       value="{{ old('email', $account->email) }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="phone" class="form-label">Phone *</label>
                <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                       id="phone" name="phone" 
                       value="{{ old('phone', $account->phone) }}" required>
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="mb-3">
            <label for="address" class="form-label">Address *</label>
            <input type="text" class="form-control @error('address') is-invalid @enderror" 
                   id="address" name="address" 
                   value="{{ old('address', $account->address) }}" required>
            @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="city" class="form-label">City *</label>
                <input type="text" class="form-control @error('city') is-invalid @enderror" 
                       id="city" name="city" 
                       value="{{ old('city', $account->city) }}" required>
                @error('city')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="col-md-3 mb-3">
                <label for="state" class="form-label">State/Province</label>
                <input type="text" class="form-control @error('state') is-invalid @enderror" 
                       id="state" name="state" 
                       value="{{ old('state', $account->state) }}">
                @error('state')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="col-md-3 mb-3">
                <label for="postal_code" class="form-label">Postal Code *</label>
                <input type="text" class="form-control @error('postal_code') is-invalid @enderror" 
                       id="postal_code" name="postal_code" 
                       value="{{ old('postal_code', $account->postal_code) }}" required>
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
                    <option value="{{ $code }}" {{ old('country', $account->country) == $code ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
            @error('country')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                   value="1" {{ old('is_active', $account->is_active) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Account is active</label>
        </div>
    </div>
</div>

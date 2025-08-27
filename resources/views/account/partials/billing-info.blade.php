<div class="tab-pane fade" id="billing" role="tabpanel" aria-labelledby="billing-tab">
    <div class="mb-4">
        <h5 class="border-bottom pb-2 mb-3">Billing Information</h5>
        
        <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" id="sameAsBusiness" 
                   {{ old('billing_same_as_business', $account->billing_same_as_business) ? 'checked' : '' }}>
            <label class="form-check-label" for="sameAsBusiness">Same as business information</label>
            <input type="hidden" name="billing_same_as_business" value="{{ $account->billing_same_as_business ? '1' : '0' }}" id="billingSameAsBusiness">
        </div>
        
        <div id="billingInfo" class="{{ $account->billing_same_as_business ? 'd-none' : '' }}">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="billing_name" class="form-label">Billing Name *</label>
                    <input type="text" class="form-control @error('billing_name') is-invalid @enderror" 
                           id="billing_name" name="billing_name" 
                           value="{{ old('billing_name', $account->billing?->billing_name) }}" 
                           {{ $account->billing_same_as_business ? 'disabled' : 'required' }}>
                    @error('billing_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="billing_email" class="form-label">Billing Email *</label>
                    <input type="email" class="form-control @error('billing_email') is-invalid @enderror" 
                           id="billing_email" name="billing_email" 
                           value="{{ old('billing_email', $account->billing?->billing_email) }}" 
                           {{ $account->billing_same_as_business ? 'disabled' : 'required' }}>
                    @error('billing_email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label for="billing_address" class="form-label">Billing Address *</label>
                <input type="text" class="form-control @error('billing_address') is-invalid @enderror" 
                       id="billing_address" name="billing_address" 
                       value="{{ old('billing_address', $account->billing?->billing_address) }}" 
                       {{ $account->billing_same_as_business ? 'disabled' : 'required' }}>
                @error('billing_address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="billing_city" class="form-label">City *</label>
                    <input type="text" class="form-control @error('billing_city') is-invalid @enderror" 
                           id="billing_city" name="billing_city" 
                           value="{{ old('billing_city', $account->billing?->billing_city) }}" 
                           {{ $account->billing_same_as_business ? 'disabled' : 'required' }}>
                    @error('billing_city')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="billing_state" class="form-label">State/Province</label>
                    <input type="text" class="form-control @error('billing_state') is-invalid @enderror" 
                           id="billing_state" name="billing_state" 
                           value="{{ old('billing_state', $account->billing?->billing_state) }}"
                           {{ $account->billing_same_as_business ? 'disabled' : '' }}>
                    @error('billing_state')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="billing_postal_code" class="form-label">Postal Code *</label>
                    <input type="text" class="form-control @error('billing_postal_code') is-invalid @enderror" 
                           id="billing_postal_code" name="billing_postal_code" 
                           value="{{ old('billing_postal_code', $account->billing?->billing_postal_code) }}" 
                           {{ $account->billing_same_as_business ? 'disabled' : 'required' }}>
                    @error('billing_postal_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label for="billing_country" class="form-label">Country *</label>
                <select class="form-select @error('billing_country') is-invalid @enderror" 
                        id="billing_country" name="billing_country" 
                        {{ $account->billing_same_as_business ? 'disabled' : 'required' }}>
                    <option value="">Select Country</option>
                    @foreach(\App\Helpers\Countries::getCountries() as $code => $name)
                        <option value="{{ $code }}" {{ old('billing_country', $account->billing?->billing_country) == $code ? 'selected' : '' }}>
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
                       value="{{ old('billing_phone', $account->billing?->billing_phone) }}" 
                       {{ $account->billing_same_as_business ? 'disabled' : 'required' }}>
                @error('billing_phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="tax_id" class="form-label">Tax ID</label>
                <input type="text" class="form-control @error('tax_id') is-invalid @enderror" 
                       id="tax_id" name="tax_id" 
                       value="{{ old('tax_id', $account->billing?->tax_id) }}"
                       {{ $account->billing_same_as_business ? 'disabled' : '' }}>
                @error('tax_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="billing_cycle" class="form-label">Billing Cycle *</label>
                    <select class="form-select @error('billing_cycle') is-invalid @enderror" 
                            id="billing_cycle" name="billing_cycle" 
                            {{ $account->billing_same_as_business ? 'disabled' : 'required' }}>
                        <option value="monthly" {{ old('billing_cycle', $account->billing?->billing_cycle) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="quarterly" {{ old('billing_cycle', $account->billing?->billing_cycle) == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                        <option value="annually" {{ old('billing_cycle', $account->billing?->billing_cycle) == 'annually' ? 'selected' : '' }}>Annually</option>
                    </select>
                    @error('billing_cycle')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="payment_method" class="form-label">Payment Method *</label>
                    <select class="form-select @error('payment_method') is-invalid @enderror" 
                            id="payment_method" name="payment_method" 
                            {{ $account->billing_same_as_business ? 'disabled' : 'required' }}>
                        <option value="credit_card" {{ old('payment_method', $account->billing?->payment_method) == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                        <option value="bank_transfer" {{ old('payment_method', $account->billing?->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="paypal" {{ old('payment_method', $account->billing?->payment_method) == 'paypal' ? 'selected' : '' }}>PayPal</option>
                        <option value="other" {{ old('payment_method', $account->billing?->payment_method) == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('payment_method')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label for="billing_notes" class="form-label">Billing Notes</label>
                <textarea class="form-control @error('billing_notes') is-invalid @enderror" 
                          id="billing_notes" name="billing_notes" 
                          rows="3" {{ $account->billing_same_as_business ? 'disabled' : '' }}>{{ old('billing_notes', $account->billing?->billing_notes) }}</textarea>
                @error('billing_notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>

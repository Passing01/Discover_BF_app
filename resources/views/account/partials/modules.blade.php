<div class="tab-pane fade" id="modules" role="tabpanel" aria-labelledby="modules-tab">
    <div class="mb-4">
        <h5 class="border-bottom pb-2 mb-3">Account Modules</h5>
        <p class="text-muted mb-3">Enable or disable modules for your account. Some modules may require additional configuration or verification.</p>
        
        <div class="row">
            @foreach($features as $key => $feature)
                @php
                    $accountFeature = $account->features->where('feature', $key)->first();
                    $isActive = $accountFeature ? $accountFeature->is_active : false;
                    $expiresAt = $accountFeature ? $accountFeature->expires_at : null;
                    $canToggle = $canManageModules ?? true;
                @endphp
                
                <div class="col-md-6 mb-4">
                    <div class="card h-100 {{ $isActive ? 'border-primary' : '' }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="card-title">{{ $feature['name'] }}</h5>
                                    <p class="text-muted">{{ $feature['description'] }}</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" 
                                           id="feature-{{ $key }}" 
                                           name="features[{{ $key }}]" 
                                           value="1" 
                                           {{ $isActive ? 'checked' : '' }}
                                           {{ !$canToggle ? 'disabled' : '' }}>
                                    <label class="form-check-label" for="feature-{{ $key }}">
                                        {{ $isActive ? 'Enabled' : 'Disabled' }}
                                    </label>
                                </div>
                            </div>
                            
                            @if($isActive && $expiresAt)
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="far fa-calendar-alt me-1"></i>
                                        Expires: {{ $expiresAt->format('M d, Y') }}
                                    </small>
                                </div>
                            @endif
                            
                            @if(isset($feature['settings']) && $isActive)
                                <div class="mt-3 pt-2 border-top">
                                    <h6 class="mb-2">Settings</h6>
                                    @foreach($feature['settings'] as $settingKey => $setting)
                                        <div class="mb-2">
                                            <label for="setting-{{ $key }}-{{ $settingKey }}" class="form-label small mb-1">
                                                {{ $setting['label'] }}
                                            </label>
                                            @if($setting['type'] === 'select')
                                                <select class="form-select form-select-sm" 
                                                        id="setting-{{ $key }}-{{ $settingKey }}" 
                                                        name="feature_settings[{{ $key }}][{{ $settingKey }}]"
                                                        {{ !$canToggle ? 'disabled' : '' }}>
                                                    @foreach($setting['options'] as $optionValue => $optionLabel)
                                                        <option value="{{ $optionValue }}" 
                                                                {{ isset($accountFeature->settings[$settingKey]) && $accountFeature->settings[$settingKey] == $optionValue ? 'selected' : '' }}>
                                                            {{ $optionLabel }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @elseif($setting['type'] === 'checkbox')
                                                <div class="form-check">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           id="setting-{{ $key }}-{{ $settingKey }}" 
                                                           name="feature_settings[{{ $key }}][{{ $settingKey }}]" 
                                                           value="1"
                                                           {{ !empty($accountFeature->settings[$settingKey] ?? false) ? 'checked' : '' }}
                                                           {{ !$canToggle ? 'disabled' : '' }}>
                                                    <label class="form-check-label" for="setting-{{ $key }}-{{ $settingKey }}">
                                                        {{ $setting['description'] ?? '' }}
                                                    </label>
                                                </div>
                                            @else
                                                <input type="{{ $setting['type'] }}" 
                                                       class="form-control form-control-sm" 
                                                       id="setting-{{ $key }}-{{ $settingKey }}" 
                                                       name="feature_settings[{{ $key }}][{{ $settingKey }}]" 
                                                       value="{{ $accountFeature->settings[$settingKey] ?? $setting['default'] ?? '' }}"
                                                       {{ !$canToggle ? 'disabled' : '' }}>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        @if(empty($features))
            <div class="text-center py-4">
                <div class="mb-3">
                    <i class="fas fa-puzzle-piece fa-3x text-muted"></i>
                </div>
                <h5>No modules available</h5>
                <p class="text-muted">There are no modules configured for your account.</p>
            </div>
        @endif
    </div>
</div>

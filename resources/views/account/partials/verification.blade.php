<div class="tab-pane fade" id="verification" role="tabpanel" aria-labelledby="verification-tab">
    <div class="mb-4">
        <h5 class="border-bottom pb-2 mb-3">Account Verification</h5>
        
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
            
            <div class="alert alert-{{ $statusClass }}">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Verification Status: </strong>
                        <span class="text-capitalize">{{ $verification->status }}</span>
                        
                        @if($verification->status === 'rejected' && $verification->rejection_reason)
                            <div class="mt-2">
                                <strong>Reason: </strong> {{ $verification->rejection_reason }}
                            </div>
                        @endif
                        
                        @if($verification->reviewed_at)
                            <div class="mt-1">
                                <small>
                                    {{ $verification->status === 'verified' ? 'Verified' : 'Rejected' }} on 
                                    {{ $verification->reviewed_at->format('M d, Y') }} by 
                                    {{ $verification->reviewer ? $verification->reviewer->name : 'Admin' }}
                                </small>
                            </div>
                        @endif
                    </div>
                    
                    @if($verification->status === 'rejected' || $verification->status === 'expired')
                        <form id="resubmit-verification" action="{{ route('account.verification.resubmit', $account) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-redo me-1"></i> Resubmit Verification
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            
            @if($verification->documents->isNotEmpty())
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Submitted Documents</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                            <i class="fas fa-plus me-1"></i> Add Document
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Document Type</th>
                                        <th>Status</th>
                                        <th>Uploaded At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($verification->documents as $document)
                                        @php
                                            $documentStatusClass = [
                                                'pending' => 'warning',
                                                'approved' => 'success',
                                                'rejected' => 'danger'
                                            ][$document->status] ?? 'secondary';
                                        @endphp
                                        <tr>
                                            <td>{{ ucfirst(str_replace('_', ' ', $document->type)) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $documentStatusClass }}">
                                                    {{ ucfirst($document->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $document->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{ route('account.verification.document.show', [$account, $document]) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   target="_blank"
                                                   data-bs-toggle="tooltip"
                                                   title="View Document">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                @if($document->status === 'rejected' && $document->rejection_reason)
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-secondary ms-1" 
                                                            data-bs-toggle="tooltip" 
                                                            title="Rejection Reason: {{ $document->rejection_reason }}">
                                                        <i class="fas fa-info-circle"></i>
                                                    </button>
                                                @endif
                                                
                                                <form action="{{ route('account.verification.document.destroy', [$account, $document]) }}" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this document?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-danger ms-1"
                                                            data-bs-toggle="tooltip"
                                                            title="Delete Document">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
            
            @if($verification->status === 'pending')
                <div class="alert alert-warning">
                    <p class="mb-2">Your verification request is under review. This process may take up to 48 hours.</p>
                    <p class="mb-0">You can still use the platform with limited functionality until your account is verified.</p>
                </div>
            @endif
            
        @else
            <div class="alert alert-info">
                <p>Your account has not been verified yet. Please submit the required documents to verify your business.</p>
                <a href="{{ route('account.verification.create', $account) }}" class="btn btn-primary">
                    <i class="fas fa-check-circle me-1"></i> Start Verification
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Upload Document Modal -->
@if($account->verification)
<div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-labelledby="uploadDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('account.verification.document.store', $account) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadDocumentModalLabel">Upload Verification Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="document_type" class="form-label">Document Type *</label>
                        <select class="form-select" id="document_type" name="document_type" required>
                            <option value="">Select Document Type</option>
                            <option value="business_license">Business License</option>
                            <option value="tax_certificate">Tax Certificate</option>
                            <option value="id_proof">ID Proof</option>
                            <option value="address_proof">Address Proof</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="document_file" class="form-label">Document File *</label>
                        <input class="form-control" type="file" id="document_file" name="document_file" accept=".pdf,.jpg,.jpeg,.png" required>
                        <div class="form-text">Accepted file types: PDF, JPG, PNG (Max: 5MB)</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-1"></i> Upload Document
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

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

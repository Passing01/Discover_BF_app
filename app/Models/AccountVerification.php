<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountVerification extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'account_id',
        'business_license_path',
        'tax_certificate_path',
        'registration_certificate_path',
        'id_document_path',
        'id_document_type',
        'id_document_number',
        'status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
        'additional_data',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'additional_data' => 'array',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_IN_REVIEW = 'in_review';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'En attente',
            self::STATUS_IN_REVIEW => 'En cours de vérification',
            self::STATUS_APPROVED => 'Approuvé',
            self::STATUS_REJECTED => 'Rejeté',
        ];
    }

    public static function documentTypes(): array
    {
        return [
            'passport' => 'Passeport',
            'national_id' => 'Carte d\'identité nationale',
            'driver_license' => 'Permis de conduire',
            'voter_id' => 'Carte d\'électeur',
            'other' => 'Autre',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function getDocumentPaths(): array
    {
        return [
            'business_license' => $this->business_license_path,
            'tax_certificate' => $this->tax_certificate_path,
            'registration_certificate' => $this->registration_certificate_path,
            'id_document' => $this->id_document_path,
        ];
    }
}

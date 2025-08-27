<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountBilling extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'account_id',
        'billing_name',
        'billing_email',
        'billing_phone',
        'billing_address',
        'billing_city',
        'billing_state',
        'billing_postal_code',
        'billing_country',
        'payment_method_type',
        'payment_method_last_four',
        'payment_method_expiry',
        'billing_cycle',
        'next_billing_date',
        'tax_identification_number',
    ];

    protected $casts = [
        'next_billing_date' => 'date',
    ];

    public const BILLING_CYCLE_MONTHLY = 'monthly';
    public const BILLING_CYCLE_QUARTERLY = 'quarterly';
    public const BILLING_CYCLE_ANNUALLY = 'annually';

    public static function billingCycles(): array
    {
        return [
            self::BILLING_CYCLE_MONTHLY => 'Mensuel',
            self::BILLING_CYCLE_QUARTERLY => 'Trimestriel',
            self::BILLING_CYCLE_ANNUALLY => 'Annuel',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function getBillingAddressFormatted(): string
    {
        $parts = [
            $this->billing_name,
            $this->billing_address,
            $this->billing_postal_code . ' ' . $this->billing_city,
            $this->billing_state ? $this->billing_state . ', ' . $this->billing_country : $this->billing_country,
        ];

        return implode("\n", array_filter($parts));
    }

    public function getPaymentMethodSummary(): ?string
    {
        if (!$this->payment_method_type || !$this->payment_method_last_four) {
            return null;
        }

        $type = match($this->payment_method_type) {
            'credit_card' => 'Carte de crédit',
            'mobile_money' => 'Mobile Money',
            'bank_transfer' => 'Virement bancaire',
            default => ucfirst(str_replace('_', ' ', $this->payment_method_type)),
        };

        return $type . ' •••• ' . $this->payment_method_last_four;
    }

    public function getNextBillingAmount(): float
    {
        // This would be calculated based on active features and billing cycle
        // In a real app, this would query the billing service
        return 0.0;
    }

    public function hasValidPaymentMethod(): bool
    {
        return !empty($this->payment_method_type) && !empty($this->payment_method_last_four);
    }
}

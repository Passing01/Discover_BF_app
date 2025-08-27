<?php

namespace App\Exports;

use App\Models\Event;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EventRegistrationsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $event;
    
    public function __construct(Event $event)
    {
        $this->event = $event;
    }
    
    public function collection()
    {
        return $this->event->registrations()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }
    
    public function headings(): array
    {
        return [
            'ID',
            'Nom',
            'Email',
            'Téléphone',
            'Statut',
            'Date d\'inscription',
            'Commentaire',
            'Nombre de places',
            'Montant total',
            'Moyen de paiement',
            'Référence de paiement'
        ];
    }
    
    public function map($registration): array
    {
        return [
            $registration->id,
            $registration->name,
            $registration->email,
            $registration->phone ?? 'Non renseigné',
            $this->getStatusLabel($registration->status),
            $registration->created_at->format('d/m/Y H:i'),
            $registration->notes ?? '',
            $registration->ticket_quantity,
            number_format($registration->total_amount, 2, ',', ' ') . ' FCFA',
            $registration->payment_method ? $this->getPaymentMethodLabel($registration->payment_method) : 'Non payé',
            $registration->payment_reference ?? 'N/A'
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            // Style de l'en-tête
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => 'E0E0E0']
                ]
            ],
            // Style des lignes paires
            'A2:Z' . ($this->collection()->count() + 1) => [
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => 'F9F9F9']
                ]
            ]
        ];
    }
    
    protected function getStatusLabel($status)
    {
        $statuses = [
            'pending' => 'En attente',
            'confirmed' => 'Confirmée',
            'cancelled' => 'Annulée',
            'refunded' => 'Remboursée'
        ];
        
        return $statuses[$status] ?? $status;
    }
    
    protected function getPaymentMethodLabel($method)
    {
        $methods = [
            'cash' => 'Espèces',
            'card' => 'Carte bancaire',
            'mobile_money' => 'Mobile Money',
            'bank_transfer' => 'Virement bancaire',
            'check' => 'Chèque',
            'other' => 'Autre'
        ];
        
        return $methods[$method] ?? $method;
    }
}

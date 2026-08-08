<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Reçu de paiement — {{ $payment->reference ?? 'TXN-DEMO0001' }}</title>
    <style>
        @page { size: A4 portrait; margin: 16mm 14mm; }
        body { font-family: DejaVu Sans, sans-serif; color: #1a1a1a; font-size: 11px; line-height: 1.5; }

        .header { border-bottom: 3px solid #af101a; padding-bottom: 12px; margin-bottom: 20px; }
        .header h1 { color: #af101a; font-size: 20px; margin: 0 0 2px 0; }
        .header p { margin: 0; color: #666; font-size: 10px; }

        .receipt-badge {
            display: inline-block; background: #006444; color: #fff; font-weight: bold;
            font-size: 11px; text-transform: uppercase; letter-spacing: 1px;
            padding: 6px 14px; border-radius: 4px; margin-bottom: 18px;
        }

        table.grid { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        table.grid td { padding: 6px 8px; border-bottom: 1px solid #eee; vertical-align: top; }
        table.grid td.label { width: 40%; color: #666; font-weight: bold; }

        .amount-box {
            background: #fbe9e9; border: 2px dashed #af101a; border-radius: 8px;
            padding: 16px; text-align: center; margin: 20px 0;
        }
        .amount-box .label { font-size: 10px; color: #af101a; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .amount-box .value { font-size: 26px; font-weight: bold; color: #af101a; margin-top: 4px; }

        .footer { margin-top: 30px; font-size: 9px; color: #999; text-align: center; border-top: 1px solid #eee; padding-top: 12px; }
    </style>
</head>
<body>

    @php
        // Repli de démonstration en attendant le contrôleur réel.
        $payment = $payment ?? (object) [
            'reference' => 'TXN-DEMO0001',
            'amount' => 30000,
            'method' => 'geniuspay',
            'status' => 'valide',
            'paid_at' => now(),
            'enrollment' => (object) [
                'matricule' => 'ESC-2026-0001',
                'user' => (object) ['name' => 'Jean-Marc Koffi', 'email' => 'jean.koffi@Campus360.cm'],
                'cursus' => (object) ['label' => 'Licence'],
                'field' => (object) ['label' => 'Génie Informatique'],
            ],
        ];
    @endphp

    <div class="header">
        <h1>Reçu de Paiement</h1>
        <p>Campus360 — École Supérieure de Commerce et d'Administration</p>
    </div>

    <span class="receipt-badge">Paiement validé</span>

    <table class="grid">
        <tr>
            <td class="label">Référence de transaction</td>
            <td>{{ $payment->reference }}</td>
        </tr>
        <tr>
            <td class="label">Étudiant</td>
            <td>{{ $payment->enrollment->user->name }} ({{ $payment->enrollment->user->email }})</td>
        </tr>
        <tr>
            <td class="label">Matricule</td>
            <td>{{ $payment->enrollment->matricule ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Cursus / Filière</td>
            <td>{{ $payment->enrollment->cursus->label ?? '—' }} — {{ $payment->enrollment->field->label ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Méthode de paiement</td>
            <td>{{ $payment->method === 'geniuspay' ? 'GeniusPay (en ligne)' : 'Espèces (campus)' }}</td>
        </tr>
        <tr>
            <td class="label">Date de paiement</td>
            <td>{{ optional($payment->paid_at)->format('d/m/Y à H:i') }}</td>
        </tr>
    </table>

    <div class="amount-box">
        <div class="label">Montant versé</div>
        <div class="value">{{ number_format($payment->amount, 0, ',', ' ') }} XAF</div>
    </div>

    <div class="footer">
        Document généré automatiquement le {{ now()->format('d/m/Y à H:i') }} — à conserver comme preuve de paiement.
    </div>

</body>
</html>

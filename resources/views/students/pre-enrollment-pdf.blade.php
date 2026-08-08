<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Pré-inscription — {{ $preEnrollment->nom }} {{ $preEnrollment->prenom }}</title>
    <style>
        @page { size: A4 portrait; margin: 10mm 12mm; }

        body { font-family: DejaVu Sans, sans-serif; color: #1a1a1a; font-size: 9.5px; line-height: 1.35; }
        .top-row { width: 100%; margin-bottom: 8px; }
        .top-row table { width: 100%; border-collapse: collapse; }
        .photo-box { width: 80px; height: 98px; border: 1px solid #999; text-align: center; vertical-align: middle; }
        .photo-box img { width: 78px; height: 96px; object-fit: cover; }
        .photo-box .placeholder { font-size: 8px; color: #999; padding-top: 42px; }

        h2.section { background: #af101a; color: #fff; font-size: 9.5px; font-weight: bold; text-transform: uppercase;
            letter-spacing: 0.5px; padding: 3px 8px; margin: 8px 0 4px 0; border-radius: 2px; }

        table.grid { width: 100%; border-collapse: collapse; margin-bottom: 3px; }
        table.grid td { padding: 1.5px 6px 1.5px 0; vertical-align: top; }

        .field-value { font-weight: bold; border-bottom: 1px dotted #bbb; display: inline-block; min-width: 90px; padding-bottom: 0px; }

        .checkbox { display: inline-block; width: 8px; height: 8px; border: 1px solid #333; text-align: center;
            font-size: 7px; line-height: 8px; margin-right: 3px; }
        .checkbox.checked { background: #af101a; color: #fff; border-color: #af101a; }

        .signature-block { margin-top: 10px; border-top: 1px solid #ccc; padding-top: 8px; }
        .signature-line { display: inline-block; width: 160px; border-bottom: 1px solid #333; margin-left: 6px; }

        .footer-note { margin-top: 10px; font-size: 7.5px; color: #999; text-align: center; }
        p { margin: 3px 0; }
    </style>
</head>
<body>

    <table class="top-row">
        <tr>
            <td style="width: 72%; vertical-align: top;">
                <h1 style="color:#af101a; font-size: 16px; margin: 0 0 2px 0;">Fiche de Pré-inscription</h1>
                <p style="margin: 0; color:#666; font-size: 9px;">Campus360 — École Supérieure de Commerce et d'Administration</p>
                <p style="margin: 2px 0 0 0; color:#999; font-size: 8px;">Dossier n° {{ str_pad($preEnrollment->id, 6, '0', STR_PAD_LEFT) }} — généré le {{ now()->format('d/m/Y') }}</p>
            </td>
            <td style="width: 28%; text-align: right; vertical-align: top;">
                <table style="margin-left: auto;"><tr><td class="photo-box">
                    @if ($photoData)
                        <img src="{{ $photoData }}" alt="Photo">
                    @else
                        <div class="placeholder">PHOTO</div>
                    @endif
                </td></tr></table>
            </td>
        </tr>
    </table>

    <h2 class="section">État civil</h2>
    <table class="grid">
        <tr>
            <td style="width:50%;">Nom : <span class="field-value">{{ $preEnrollment->nom }}</span></td>
            <td style="width:50%;">Prénom : <span class="field-value">{{ $preEnrollment->prenom }}</span></td>
        </tr>
        <tr>
            <td>Date de naissance : <span class="field-value">{{ $preEnrollment->date_naissance->format('d/m/Y') }}</span></td>
            <td>Lieu de naissance / Ville : <span class="field-value">{{ $preEnrollment->lieu_naissance ?? '—' }}</span></td>
        </tr>
        <tr>
            <td>Département : <span class="field-value">{{ $preEnrollment->departement ?? '—' }}</span></td>
            <td>Pays : <span class="field-value">{{ $preEnrollment->pays ?? '—' }}</span></td>
        </tr>
        <tr>
            <td>
                Sexe :
                <span class="checkbox {{ $preEnrollment->sexe === 'M' ? 'checked' : '' }}">{{ $preEnrollment->sexe === 'M' ? 'X' : '' }}</span> Masculin
                &nbsp;
                <span class="checkbox {{ $preEnrollment->sexe === 'F' ? 'checked' : '' }}">{{ $preEnrollment->sexe === 'F' ? 'X' : '' }}</span> Féminin
            </td>
            <td>Nationalité : <span class="field-value">{{ $preEnrollment->nationalite ?? '—' }}</span></td>
        </tr>
        <tr>
            <td>Téléphone : <span class="field-value">{{ $preEnrollment->telephone ?? '—' }}</span></td>
            <td>Email : <span class="field-value">{{ $preEnrollment->email ?? '—' }}</span></td>
        </tr>
        <tr>
            <td>
                Situation familiale :
                <span class="checkbox {{ $preEnrollment->situation_familiale === 'marie' ? 'checked' : '' }}">{{ $preEnrollment->situation_familiale === 'marie' ? 'X' : '' }}</span> Marié(e)
                &nbsp;
                <span class="checkbox {{ $preEnrollment->situation_familiale === 'celibataire' ? 'checked' : '' }}">{{ $preEnrollment->situation_familiale === 'celibataire' ? 'X' : '' }}</span> Célibataire
            </td>
            <td>
                Handicap :
                <span class="checkbox {{ $preEnrollment->handicap === 'oui' ? 'checked' : '' }}">{{ $preEnrollment->handicap === 'oui' ? 'X' : '' }}</span> Oui
                &nbsp;
                <span class="checkbox {{ $preEnrollment->handicap === 'non' ? 'checked' : '' }}">{{ $preEnrollment->handicap === 'non' ? 'X' : '' }}</span> Non
            </td>
        </tr>
    </table>

    <h2 class="section">Baccalauréat ou diplôme équivalent</h2>
    <table class="grid">
        <tr>
            <td style="width:34%;">Année : <span class="field-value">{{ $preEnrollment->bac_annee ?? '—' }}</span></td>
            <td style="width:33%;">Série : <span class="field-value">{{ $preEnrollment->bac_serie ?? '—' }}</span></td>
            <td style="width:33%;">Mention : <span class="field-value">{{ $preEnrollment->bac_mention ?? '—' }}</span></td>
        </tr>
        <tr>
            <td colspan="3">Établissement d'obtention : <span class="field-value">{{ $preEnrollment->bac_etablissement ?? '—' }}</span></td>
        </tr>
    </table>

    <h2 class="section">Projet d'études</h2>
    <table class="grid">
        <tr>
            <td style="width:50%;">Cursus visé : <span class="field-value">{{ $preEnrollment->cursus->label }}</span></td>
            <td style="width:50%;">Filière : <span class="field-value">{{ $preEnrollment->field->label }}</span></td>
        </tr>
        <tr>
            <td>Spécialité : <span class="field-value">{{ $preEnrollment->speciality->label ?? 'Tronc commun' }}</span></td>
            <td>Niveau visé : <span class="field-value">{{ $preEnrollment->level->label }}</span></td>
        </tr>
        <tr>
            <td>Statut / fonction : <span class="field-value">{{ $preEnrollment->statut_etudiant ?? '—' }}</span></td>
            <td>Profession du chef de famille : <span class="field-value">{{ $preEnrollment->profession_chef_famille ?? '—' }}</span></td>
        </tr>
    </table>

    <p>Cursus scolaire des quatre dernières années :</p>
    <table class="grid">
        @php $cs = $preEnrollment->cursus_scolaire ?? []; @endphp
        <tr>
            <td style="width:50%;">1) 2024-2025 : <span class="field-value">{{ $cs['2024-2025'] ?? '—' }}</span></td>
            <td style="width:50%;">2) 2023-2024 : <span class="field-value">{{ $cs['2023-2024'] ?? '—' }}</span></td>
        </tr>
        <tr>
            <td>3) 2022-2023 : <span class="field-value">{{ $cs['2022-2023'] ?? '—' }}</span></td>
            <td>4) 2021-2022 : <span class="field-value">{{ $cs['2021-2022'] ?? '—' }}</span></td>
        </tr>
    </table>

    <h2 class="section">Logistique &amp; financement</h2>
    <table class="grid">
        <tr>
            <td style="width:50%;">Hébergement : <span class="field-value">{{ $preEnrollment->type_hebergement ?? '—' }}</span></td>
            <td style="width:50%;">Quartier de résidence : <span class="field-value">{{ $preEnrollment->quartier_residence ?? '—' }}</span></td>
        </tr>
        @if ($preEnrollment->hebergement_precisions)
            <tr><td colspan="2">Précisions : <span class="field-value">{{ $preEnrollment->hebergement_precisions }}</span></td></tr>
        @endif
    </table>

    <p>
        Financement :
        @php
            $financements = ['personnel' => 'Vous-même', 'employeur' => 'Employeur', 'bourse' => 'Bourse', 'autre' => 'Autres'];
        @endphp
        @foreach ($financements as $key => $label)
            <span class="checkbox {{ $preEnrollment->financement === $key ? 'checked' : '' }}">{{ $preEnrollment->financement === $key ? 'X' : '' }}</span> {{ $label }} &nbsp;
        @endforeach
        &nbsp;&nbsp;|&nbsp;&nbsp;
        Mobilité intern. :
        <span class="checkbox {{ $preEnrollment->mobilite_internationale === 'oui' ? 'checked' : '' }}">{{ $preEnrollment->mobilite_internationale === 'oui' ? 'X' : '' }}</span> Oui
        <span class="checkbox {{ $preEnrollment->mobilite_internationale === 'non' ? 'checked' : '' }}">{{ $preEnrollment->mobilite_internationale === 'non' ? 'X' : '' }}</span> Non
    </p>

    <p>
        Mode de paiement :
        @php
            $modes = ['especes' => 'Espèces', 'mandat' => 'Mandat', 'cheque' => 'Chèque', 'virement' => 'Virement / Caisse'];
            $selectedModes = $preEnrollment->mode_paiement ?? [];
        @endphp
        @foreach ($modes as $key => $label)
            <span class="checkbox {{ in_array($key, $selectedModes) ? 'checked' : '' }}">{{ in_array($key, $selectedModes) ? 'X' : '' }}</span> {{ $label }} &nbsp;
        @endforeach
    </p>

    <h2 class="section">Contact d'urgence</h2>
    <table class="grid">
        <tr>
            <td style="width:40%;">Nom : <span class="field-value">{{ $preEnrollment->contact_urgence_nom ?? '—' }}</span></td>
            <td style="width:30%;">Tél. : <span class="field-value">{{ $preEnrollment->contact_urgence_telephone ?? '—' }}</span></td>
            <td style="width:30%;">Email : <span class="field-value">{{ $preEnrollment->contact_urgence_email ?? '—' }}</span></td>
        </tr>
    </table>

    <div class="signature-block">
        <p style="font-weight: bold; margin-bottom: 3px;">NB : À REMPLIR OBLIGATOIREMENT</p>
        <p style="margin-bottom: 8px;">
            Je déclare sur l'honneur que les renseignements fournis à l'appui de cette demande sont complets et exacts.
            Toute fausse déclaration entraînera l'annulation immédiate de ma candidature.
        </p>
        <table style="width:100%;">
            <tr>
                <td style="width:50%;">Fait à : <span class="field-value">{{ $preEnrollment->fait_a ?? '..............................' }}</span></td>
                <td style="width:50%;">Date : <span class="field-value">{{ $preEnrollment->date_signature?->format('d/m/Y') ?? '../../....' }}</span></td>
            </tr>
        </table>
        <p style="margin-top: 10px;">Signature de l'étudiant(e) : <span class="signature-line">&nbsp;</span></p>
    </div>

    <div class="footer-note">
        Document généré automatiquement le {{ now()->format('d/m/Y à H:i') }} — à imprimer et présenter, signé, lors de votre passage à l'école.
    </div>

</body>
</html>

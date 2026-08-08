<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche de Décharge PC - {{ $student->matricule }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #1f2937;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #006444;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .header h1 {
            font-size: 18px;
            color: #006444;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .header p {
            margin: 0;
            font-size: 10px;
            color: #6b7280;
        }
        .doc-title {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 25px;
            background-color: #f3f4f6;
            padding: 8px;
            border-radius: 4px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #111827;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 4px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        table td {
            padding: 6px 8px;
            vertical-align: top;
        }
        .label {
            font-weight: bold;
            width: 35%;
            color: #374151;
        }
        .value {
            width: 65%;
            color: #111827;
        }
        .terms {
            font-size: 10.5px;
            color: #4b5563;
            line-height: 1.5;
            background-color: #f9fafb;
            padding: 12px;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            margin-top: 15px;
        }
        .signatures {
            margin-top: 50px;
            width: 100%;
        }
        .signatures td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        .sig-box {
            height: 70px;
        }
    </style>
</head>
<body>

    <!-- En-tête -->
    <div class="header">
        <h1>ÉCOLE SUPÉRIEURE DE COMMERCE ET D'ADMINISTRATION</h1>
        <p>Service de la Scolarité & de l'Équipement Informatique</p>
    </div>

    <div class="doc-title">
        FICHE DE DÉCHARGE ET DE REMISE DE MATÉRIEL
    </div>

    <!-- Informations Étudiant -->
    <div class="section">
        <div class="section-title">1. Informations sur le Bénéficiaire</div>
        <table>
            <tr>
                <td class="label">Nom & Prénom :</td>
                <td class="value"><strong>{{ $user->name }}</strong></td>
            </tr>
            <tr>
                <td class="label">Matricule Étudiant :</td>
                <td class="value"><strong>{{ $student->matricule ?? 'N/A' }}</strong></td>
            </tr>
            <tr>
                <td class="label">Adresse E-mail :</td>
                <td class="value">{{ $user->email }}</td>
            </tr>
            <tr>
                <td class="label">Date d'attribution :</td>
                <td class="value">{{ $date }}</td>
            </tr>
        </table>
    </div>

    <!-- Caractéristiques du Matériel -->
    <div class="section">
        <div class="section-title">2. Caractéristiques du Matériel Remis</div>
        <table>
            <tr>
                <td class="label">Type d'équipement :</td>
                <td class="value">Ordinateur Portable</td>
            </tr>
            <tr>
                <td class="label">Marque / Modèle :</td>
                <td class="value"><strong>{{ $laptop->brand }} {{ $laptop->model }}</strong></td>
            </tr>
            <tr>
                <td class="label">Numéro de Série (S/N) :</td>
                <td class="value"><strong style="font-family: monospace;">{{ $laptop->serial_number }}</strong></td>
            </tr>
            <tr>
                <td class="label">Adresse MAC Réseau :</td>
                <td class="value" style="font-family: monospace;">{{ $laptop->mac_address ?? 'Non renseignée' }}</td>
            </tr>
            <tr>
                <td class="label">État du matériel :</td>
                <td class="value">Neuf / Bon état de fonctionnement</td>
            </tr>
        </table>
    </div>

    <!-- Engagements -->
    <div class="section">
        <div class="section-title">3. Engagement et Responsabilité</div>
        <div class="terms">
            Je soussigné(e), <strong>{{ $user->name }}</strong>, reconnais avoir reçu ce jour l'ordinateur portable mentionné ci-dessus en parfait état de marche. Je m'engage à :
            <ul>
                <li>Utiliser cet équipement à des fins pédagogiques dans le cadre de mes études.</li>
                <li>Assurer la garde et la conservation du matériel en bon père de famille.</li>
                <li>Ne pas céder, vendre ou louer cet équipement à un tiers.</li>
                <li>Informer immédiatement l'administration en cas de dysfonctionnement technique majeur ou de perte.</li>
            </ul>
        </div>
    </div>

    <!-- Signatures -->
    <table class="signatures">
        <tr>
            <td>
                <strong>L'Étudiant(e)</strong><br>
                <small>(Précédé de la mention "Lu et approuvé")</small>
                <div class="sig-box"></div>
            </td>
            <td>
                <strong>Pour l'Administration</strong><br>
                <small>Signature & Cachet</small>
                <div class="sig-box"></div>
            </td>
        </tr>
    </table>

</body>
</html>

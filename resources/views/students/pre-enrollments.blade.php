@extends('layouts.student')

@section('title', 'Campus360 | Pré-inscription Étudiant')
@section('page-title', 'Pré-inscription')

@section('content')

@php
    // Si la soumission précédente a échoué, on rouvre le formulaire directement
    // à la première étape qui contient une erreur (au lieu de tout faire recommencer).
    $step1Keys = ['nom', 'prenom', 'date_naissance', 'sexe', 'telephone', 'email'];
    $step2Keys = ['bac_annee', 'bac_serie', 'bac_mention', 'bac_etablissement', 'cursus_id', 'field_id', 'speciality_id', 'level_id', 'statut_etudiant', 'profession_chef_famille'];
    $step3Keys = ['type_hebergement', 'quartier_residence', 'hebergement_precisions', 'financement', 'paiement', 'mobilite_internationale', 'contact_urgence_nom', 'contact_urgence_telephone', 'contact_urgence_email', 'declaration_honneur'];

    $initialStep = 1;
    if ($errors->hasAny($step3Keys)) $initialStep = 3;
    if ($errors->hasAny($step2Keys)) $initialStep = 2;
    if ($errors->hasAny($step1Keys)) $initialStep = 1;
@endphp

<!-- Données des cursus (uniquement ce que l'admin a enregistré), consommées par Alpine ci-dessous -->
<script type="application/json" id="cursuses-data">@json($cursuses)</script>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('preEnrollmentForm', () => ({
        formStep: {{ $initialStep }},
        sexe: @js(old('sexe', 'M')),
        financement: @js(old('financement', '')),
        mobilite: @js(old('mobilite_internationale', '')),
        photoName: '',
        cursuses: JSON.parse(document.getElementById('cursuses-data').textContent),
        cursusId: @js(old('cursus_id', '')),
        fieldId: @js(old('field_id', '')),
        specialityId: @js(old('speciality_id', '')),
        levelId: @js(old('level_id', '')),
        stepErrors: [],
        fieldErrors: {},

        // Règles de format vérifiées en direct, dès que l'utilisateur quitte le champ
        // (indépendamment de la validation serveur, qui reste la source de vérité finale).
        fieldRules: {
            nom: { required: true, label: 'nom' },
            prenom: { required: true, label: 'prénom' },
            date_naissance: { required: true, label: 'date de naissance', minAge: 15 },
            email: { email: true },
            telephone: { phone: true },
            contact_urgence_email: { email: true },
            contact_urgence_telephone: { phone: true },
            cursus_id: { required: true, label: 'cursus' },
            field_id: { required: true, label: 'filière' },
            level_id: { required: true, label: 'niveau' },
        },

        validateField(name, value) {
            const rule = this.fieldRules[name];
            if (!rule) return;

            let message = '';
            const isEmpty = value === null || value === undefined || value.toString().trim() === '';

            if (rule.required && isEmpty) {
                message = `Ce champ${rule.label ? ' (' + rule.label + ')' : ''} est requis. Merci de le remplir ou de le sélectionner.`;
            } else if (rule.email && !isEmpty && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                message = 'Il manque un « @ » : merci de saisir une adresse e-mail valide.';
            } else if (rule.phone && !isEmpty && !/^[0-9+()\s.-]{6,20}$/.test(value)) {
                message = 'Merci de saisir un numéro de téléphone valide (chiffres, espaces, + acceptés).';
            } else if (rule.minAge && !isEmpty) {
                const age = this.calculateAge(value);
                if (age !== null && age < rule.minAge) {
                    message = `Vous devez avoir au moins ${rule.minAge} ans pour vous pré-inscrire.`;
                }
            }

            if (message) {
                this.fieldErrors[name] = message;
            } else {
                delete this.fieldErrors[name];
            }
        },

        calculateAge(dateStr) {
            const birth = new Date(dateStr);
            if (isNaN(birth.getTime())) return null;
            const today = new Date();
            let age = today.getFullYear() - birth.getFullYear();
            const m = today.getMonth() - birth.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
            return age;
        },

        get fields() {
            const c = this.cursuses.find(c => c.id == this.cursusId);
            return c ? c.fields : [];
        },
        get specialities() {
            const f = this.fields.find(f => f.id == this.fieldId);
            return f ? f.specialities : [];
        },
        get levels() {
            const f = this.fields.find(f => f.id == this.fieldId);
            return f ? f.levels : [];
        },

        // Vérifie que tous les champs [required] visibles dans l'étape donnée sont remplis.
        validateStep(step) {
            this.stepErrors = [];
            const container = this.$refs['step' + step];
            if (!container) return true;

            let valid = true;
            const checkedGroups = {};

            container.querySelectorAll('[required]').forEach((el) => {
                let filled = true;

                if (el.type === 'radio') {
                    if (checkedGroups[el.name]) return;
                    checkedGroups[el.name] = true;
                    const group = container.querySelectorAll(`input[type="radio"][name="${el.name}"]`);
                    filled = Array.from(group).some((r) => r.checked);
                } else if (el.type === 'checkbox') {
                    filled = el.checked;
                } else {
                    filled = el.value !== null && el.value.toString().trim() !== '';
                    el.classList.toggle('border-red-500', !filled);
                }

                if (!filled) valid = false;
            });

            // Applique aussi les règles de format (email, téléphone, âge...) à tous
            // les champs concernés présents dans cette étape.
            Object.keys(this.fieldRules).forEach((name) => {
                const el = container.querySelector(`[name="${name}"]`);
                if (!el) return;
                this.validateField(name, el.value);
                if (this.fieldErrors[name]) valid = false;
            });

            // Vérification générique de longueur max : tout champ texte/email/textarea
            // possédant un attribut maxlength est vérifié, même s'il n'a pas de règle
            // dédiée ci-dessus. Ça évite qu'une limite serveur (ex: max:10) ne soit
            // découverte qu'à la toute fin, lors de la soumission finale.
            container.querySelectorAll('input[maxlength], textarea[maxlength]').forEach((el) => {
                const limit = parseInt(el.getAttribute('maxlength'), 10);
                if (!limit || !el.value) return;
                if (el.value.length > limit) {
                    valid = false;
                    el.classList.add('border-red-500');
                    this.fieldErrors[el.name] = `Ce champ ne doit pas dépasser ${limit} caractères (actuellement ${el.value.length}).`;
                } else {
                    el.classList.remove('border-red-500');
                    delete this.fieldErrors[el.name];
                }
            });

            if (!valid) {
                this.stepErrors = ['Merci de corriger les champs en erreur avant de continuer.'];
            }

            return valid;
        },

        next() {
            if (this.validateStep(this.formStep) && this.formStep < 3) {
                this.formStep++;
            }
        },

        prev(dashboardUrl) {
            if (this.formStep > 1) {
                this.formStep--;
            } else {
                window.location.href = dashboardUrl;
            }
        },
    }));
});
</script>

<div class="flex-1 p-4 md:p-12 max-w-[1440px] mx-auto w-full" x-data="preEnrollmentForm()">
    <div class="p-8 md:p-12 bg-white rounded-3xl border border-zinc-100 premium-shadow">

        @if ($errors->any())
            <div class="mb-8 px-5 py-4 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm font-semibold flex items-center gap-2">
                <span class="material-symbols-outlined">error</span>
                Votre dossier contient des erreurs — merci de les corriger ci-dessous avant de continuer.
            </div>
        @endif

        <!-- ================= INDICATEUR D'ÉTAPES ================= -->
        <div class="mb-10">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-[#af101a] uppercase tracking-widest">
                    Étape <span x-text="formStep"></span> sur 3
                </span>
                <span class="text-xs font-bold text-zinc-500 uppercase tracking-widest" x-text="formStep === 1 ? 'Informations Personnelles' : (formStep === 2 ? 'Cursus & Diplômes' : 'Logistique & Financement')"></span>
            </div>
            <div class="flex gap-2">
                <div class="h-1.5 flex-1 rounded-full transition-colors" :class="formStep === 1 ? 'bg-[#af101a]' : (formStep > 1 ? 'bg-[#006444]' : 'bg-[#af101a]/10')"></div>
                <div class="h-1.5 flex-1 rounded-full transition-colors" :class="formStep === 2 ? 'bg-[#af101a]' : (formStep > 2 ? 'bg-[#006444]' : 'bg-[#af101a]/10')"></div>
                <div class="h-1.5 flex-1 rounded-full transition-colors" :class="formStep === 3 ? 'bg-[#af101a]' : 'bg-[#af101a]/10'"></div>
            </div>
        </div>

        <!-- Un seul <form>, une seule soumission HTTP : celle du bouton final (étape 3). -->
        <form class="space-y-10" method="POST" action="{{ route('pre-enrollments.store') }}" enctype="multipart/form-data" novalidate @submit="if (formStep < 3) { $event.preventDefault(); }">
            @csrf

            <!-- ================= ÉTAPE 1 : INFORMATIONS PERSONNELLES ================= -->
            <div x-show="formStep === 1" x-ref="step1" class="space-y-10">

                <div class="flex items-start justify-between gap-8 flex-wrap">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 font-montserrat">Informations Personnelles</h1>
                        <p class="text-zinc-500 font-inter mt-1">Veuillez remplir vos coordonnées officielles pour votre dossier d'admission.</p>
                    </div>

                    <div class="flex flex-col items-center gap-2">
                        <label class="flex flex-col items-center justify-center gap-2 w-28 h-28 shrink-0 rounded-2xl bg-zinc-50 border-2 border-dashed {{ $errors->has('photo') ? 'border-red-400' : 'border-zinc-200' }} cursor-pointer hover:border-[#af101a] transition-colors">
                            <input type="file" name="photo" accept="image/*" class="hidden" @change="photoName = $event.target.files[0]?.name ?? ''">
                            <span class="material-symbols-outlined text-zinc-400 text-2xl">add_a_photo</span>
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider text-center px-1" x-text="photoName ? photoName : 'Photo'"></span>
                        </label>
                        @error('photo')
                            <span class="text-xs text-red-500 text-center">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="text-xs text-zinc-600 font-semibold">Nom <span class="text-[#af101a]">*</span></label>
                        <input class="w-full p-4 rounded-xl bg-zinc-50 border {{ $errors->has('nom') ? 'border-red-500' : 'border-zinc-200' }} focus:ring-2 focus:ring-[#af101a]/20" placeholder="Entrez votre nom" type="text" name="nom" value="{{ old('nom') }}" required @blur="validateField('nom', $event.target.value)" maxlength="100">
                        @error('nom') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        <p x-show="fieldErrors.nom" x-text="fieldErrors.nom" class="text-xs text-red-500 mt-1"></p>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs text-zinc-600 font-semibold">Prénom <span class="text-[#af101a]">*</span></label>
                        <input class="w-full p-4 rounded-xl bg-zinc-50 border {{ $errors->has('prenom') ? 'border-red-500' : 'border-zinc-200' }} focus:ring-2 focus:ring-[#af101a]/20" placeholder="Entrez votre prénom" type="text" name="prenom" value="{{ old('prenom') }}" required @blur="validateField('prenom', $event.target.value)" maxlength="100">
                        @error('prenom') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        <p x-show="fieldErrors.prenom" x-text="fieldErrors.prenom" class="text-xs text-red-500 mt-1"></p>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs text-zinc-600 font-semibold">Date de naissance <span class="text-[#af101a]">*</span></label>
                        <input class="w-full p-4 rounded-xl bg-zinc-50 border {{ $errors->has('date_naissance') ? 'border-red-500' : 'border-zinc-200' }} focus:ring-2 focus:ring-[#af101a]/20" type="date" name="date_naissance" value="{{ old('date_naissance') }}" required @blur="validateField('date_naissance', $event.target.value)">
                        @error('date_naissance') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        <p x-show="fieldErrors.date_naissance" x-text="fieldErrors.date_naissance" class="text-xs text-red-500 mt-1"></p>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs text-zinc-600 font-semibold">Lieu de naissance / Ville</label>
                        <input class="w-full p-4 rounded-xl bg-zinc-50 border border-zinc-200 focus:ring-2 focus:ring-[#af101a]/20" placeholder="Ville de naissance" type="text" name="lieu_naissance" value="{{ old('lieu_naissance') }}" maxlength="100">
                        @error('lieu_naissance') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs text-zinc-600 font-semibold">Département</label>
                        <input class="w-full p-4 rounded-xl bg-zinc-50 border border-zinc-200 focus:ring-2 focus:ring-[#af101a]/20" placeholder="Votre département" type="text" name="departement" value="{{ old('departement') }}" maxlength="100">
                        @error('departement') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs text-zinc-600 font-semibold">Pays</label>
                        <input class="w-full p-4 rounded-xl bg-zinc-50 border border-zinc-200 focus:ring-2 focus:ring-[#af101a]/20" placeholder="Votre pays de résidence" type="text" name="pays" value="{{ old('pays') }}" maxlength="100">
                        @error('pays') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-4">
                        <label class="text-xs text-zinc-600 font-semibold">Sexe</label>
                        <div class="flex gap-4">
                            <button type="button" @click="sexe = 'M'" :class="sexe === 'M' ? 'border-[#af101a] bg-[#af101a] text-white' : 'border-zinc-200 hover:bg-zinc-50'" class="flex-1 p-4 border-2 rounded-xl font-bold transition-all cursor-pointer">Masculin</button>
                            <button type="button" @click="sexe = 'F'" :class="sexe === 'F' ? 'border-[#af101a] bg-[#af101a] text-white' : 'border-zinc-200 hover:bg-zinc-50'" class="flex-1 p-4 border-2 rounded-xl font-bold transition-all cursor-pointer">Féminin</button>
                        </div>
                        <input type="hidden" name="sexe" :value="sexe">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs text-zinc-600 font-semibold">Nationalité</label>
                        <input class="w-full p-4 rounded-xl bg-zinc-50 border border-zinc-200 focus:ring-2 focus:ring-[#af101a]/20" placeholder="Votre nationalité" type="text" name="nationalite" value="{{ old('nationalite') }}" maxlength="100">
                        @error('nationalite') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs text-zinc-600 font-semibold">Téléphone</label>
                        <input class="w-full p-4 rounded-xl bg-zinc-50 border {{ $errors->has('telephone') ? 'border-red-500' : 'border-zinc-200' }} focus:ring-2 focus:ring-[#af101a]/20" placeholder="Ex: +237 ..." type="text" name="telephone" value="{{ old('telephone') }}" @blur="validateField('telephone', $event.target.value)" maxlength="30">
                        @error('telephone') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        <p x-show="fieldErrors.telephone" x-text="fieldErrors.telephone" class="text-xs text-red-500 mt-1"></p>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs text-zinc-600 font-semibold">Adresse e-mail</label>
                        <input class="w-full p-4 rounded-xl bg-zinc-50 border {{ $errors->has('email') ? 'border-red-500' : 'border-zinc-200' }} focus:ring-2 focus:ring-[#af101a]/20" placeholder="nom@gmail.com" type="email" name="email" value="{{ old('email') }}" @blur="validateField('email', $event.target.value)" maxlength="150">
                        @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        <p x-show="fieldErrors.email" x-text="fieldErrors.email" class="text-xs text-red-500 mt-1"></p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-8 border-t border-zinc-100">
                    <div class="space-y-3">
                        <label class="text-xs text-zinc-600 font-semibold">Situation familiale</label>
                        <div class="flex items-center gap-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="situation_familiale" value="marie" @checked(old('situation_familiale') === 'marie') class="w-4 h-4 border-zinc-300 text-[#af101a] focus:ring-[#af101a]/30">
                                <span class="text-sm text-zinc-700">Marié(e)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="situation_familiale" value="celibataire" @checked(old('situation_familiale') === 'celibataire') class="w-4 h-4 border-zinc-300 text-[#af101a] focus:ring-[#af101a]/30">
                                <span class="text-sm text-zinc-700">Célibataire</span>
                            </label>
                        </div>
                        @error('situation_familiale') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-3">
                        <label class="text-xs text-zinc-600 font-semibold">Êtes-vous en situation de handicap ?</label>
                        <div class="flex items-center gap-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="handicap" value="oui" @checked(old('handicap') === 'oui') class="w-4 h-4 border-zinc-300 text-[#af101a] focus:ring-[#af101a]/30">
                                <span class="text-sm text-zinc-700">Oui</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="handicap" value="non" @checked(old('handicap') === 'non') class="w-4 h-4 border-zinc-300 text-[#af101a] focus:ring-[#af101a]/30">
                                <span class="text-sm text-zinc-700">Non</span>
                            </label>
                        </div>
                        @error('handicap') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- ================= ÉTAPE 2 : PARCOURS ACADÉMIQUE ================= -->
            <div x-show="formStep === 2" x-ref="step2" style="display: none;" class="space-y-10">
                <div class="mb-2">
                    <h1 class="text-2xl font-bold text-zinc-900 font-montserrat">Parcours Académique</h1>
                    <p class="text-zinc-500 font-inter mt-1">Veuillez renseigner les détails de votre cursus scolaire et les diplômes obtenus.</p>
                </div>

                <!-- Baccalauréat -->
                <div class="rounded-2xl border border-zinc-100 p-6 space-y-4">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[#af101a]">school</span>
                        <h3 class="text-lg font-bold text-[#af101a] font-montserrat">Baccalauréat ou Équivalent</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="flex flex-col gap-2">
                            <label class="text-xs text-zinc-600 font-semibold">Année d'obtention</label>
                            <input class="bg-zinc-50 border border-zinc-200 rounded-xl p-4" placeholder="Ex: 2026" type="text" name="bac_annee" value="{{ old('bac_annee') }}" maxlength="4">
                            @error('bac_annee') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-xs text-zinc-600 font-semibold">Série</label>
                            <input class="bg-zinc-50 border border-zinc-200 rounded-xl p-4" placeholder="Ex: C, D, A, F4..." type="text" name="bac_serie" value="{{ old('bac_serie') }}" maxlength="20">
                            @error('bac_serie') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-xs text-zinc-600 font-semibold">Mention</label>
                            <select class="bg-zinc-50 border border-zinc-200 rounded-xl p-4" name="bac_mention">
                                <option value="">Sélectionner...</option>
                                @foreach (['Passable', 'Assez Bien', 'Bien', 'Très Bien'] as $mention)
                                    <option @selected(old('bac_mention') === $mention)>{{ $mention }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-xs text-zinc-600 font-semibold">Établissement d'obtention</label>
                        <input class="w-full bg-zinc-50 border border-zinc-200 rounded-xl p-4" placeholder="Nom complet de l'établissement" type="text" name="bac_etablissement" value="{{ old('bac_etablissement') }}" maxlength="150">
                        @error('bac_etablissement') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Projet d'Études : uniquement ce que l'admin a enregistré -->
                <div class="rounded-2xl border border-zinc-100 p-6 space-y-4">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[#af101a]">badge</span>
                        <h3 class="text-lg font-bold text-[#af101a] font-montserrat">Projet d'Études</h3>
                    </div>

                    @if ($cursuses->isEmpty())
                        <p class="text-sm text-amber-600 bg-amber-50 border border-amber-200 rounded-xl p-4">
                            Aucun cursus n'est encore disponible. Merci de réessayer plus tard.
                        </p>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex flex-col gap-2">
                            <label class="text-xs text-zinc-600 font-semibold">Cursus visé <span class="text-[#af101a]">*</span></label>
                            <select x-model="cursusId" @change="fieldId = ''; specialityId = ''; levelId = ''; validateField('cursus_id', cursusId)" name="cursus_id" class="bg-zinc-50 border {{ $errors->has('cursus_id') ? 'border-red-500' : 'border-zinc-200' }} rounded-xl p-4" required>
                                <option value="">Sélectionner...</option>
                                <template x-for="c in cursuses" :key="c.id">
                                    <option :value="c.id" x-text="c.label"></option>
                                </template>
                            </select>
                            @error('cursus_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            <p x-show="fieldErrors.cursus_id" x-text="fieldErrors.cursus_id" class="text-xs text-red-500 mt-1"></p>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-xs text-zinc-600 font-semibold">Filière <span class="text-[#af101a]">*</span></label>
                            <select x-model="fieldId" @change="specialityId = ''; levelId = ''; validateField('field_id', fieldId)" name="field_id" :disabled="!cursusId" class="bg-zinc-50 border {{ $errors->has('field_id') ? 'border-red-500' : 'border-zinc-200' }} rounded-xl p-4 disabled:opacity-50" required>
                                <option value="">Sélectionner...</option>
                                <template x-for="f in fields" :key="f.id">
                                    <option :value="f.id" x-text="f.label"></option>
                                </template>
                            </select>
                            @error('field_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            <p x-show="fieldErrors.field_id" x-text="fieldErrors.field_id" class="text-xs text-red-500 mt-1"></p>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-xs text-zinc-600 font-semibold">Spécialité (si applicable)</label>
                            <select x-model="specialityId" name="speciality_id" :disabled="!fieldId" class="bg-zinc-50 border border-zinc-200 rounded-xl p-4 disabled:opacity-50">
                                <option value="">Aucune / Tronc commun</option>
                                <template x-for="s in specialities" :key="s.id">
                                    <option :value="s.id" x-text="s.label"></option>
                                </template>
                            </select>
                            @error('speciality_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-xs text-zinc-600 font-semibold">Niveau visé <span class="text-[#af101a]">*</span></label>
                            <select x-model="levelId" @change="validateField('level_id', levelId)" name="level_id" :disabled="!fieldId" class="bg-zinc-50 border {{ $errors->has('level_id') ? 'border-red-500' : 'border-zinc-200' }} rounded-xl p-4 disabled:opacity-50" required>
                                <option value="">Sélectionner...</option>
                                <template x-for="l in levels" :key="l.id">
                                    <option :value="l.id" x-text="l.label"></option>
                                </template>
                            </select>
                            @error('level_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            <p x-show="fieldErrors.level_id" x-text="fieldErrors.level_id" class="text-xs text-red-500 mt-1"></p>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-xs text-zinc-600 font-semibold">Statut ou fonction de l'étudiant</label>
                            <input class="bg-zinc-50 border border-zinc-200 rounded-xl p-4" placeholder="Nouveau bachelier, salarié..." type="text" name="statut_etudiant" value="{{ old('statut_etudiant') }}" maxlength="100">
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-xs text-zinc-600 font-semibold">Profession du chef de famille</label>
                            <input class="bg-zinc-50 border border-zinc-200 rounded-xl p-4" type="text" name="profession_chef_famille" value="{{ old('profession_chef_famille') }}" maxlength="100">
                        </div>
                    </div>
                </div>

                <!-- Cursus Scolaire -->
                <div class="rounded-2xl border border-zinc-100 p-6 space-y-4">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[#af101a]">auto_stories</span>
                        <h3 class="text-lg font-bold text-[#af101a] font-montserrat">Cursus Scolaire (4 dernières années)</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-2 bg-zinc-50 border border-zinc-200 rounded-xl p-4">
                            <label class="text-xs text-[#af101a] font-bold">2024-2025</label>
                            <input class="bg-white border border-zinc-200 rounded-lg p-3" placeholder="Établissement / Niveau" type="text" name="cursus_2024_2025" value="{{ old('cursus_2024_2025') }}" maxlength="150">
                        </div>
                        <div class="flex flex-col gap-2 bg-zinc-50 border border-zinc-200 rounded-xl p-4">
                            <label class="text-xs text-zinc-600 font-bold">2023-2024</label>
                            <input class="bg-white border border-zinc-200 rounded-lg p-3" placeholder="Établissement / Niveau" type="text" name="cursus_2023_2024" value="{{ old('cursus_2023_2024') }}" maxlength="150">
                        </div>
                        <div class="flex flex-col gap-2 bg-zinc-50 border border-zinc-200 rounded-xl p-4">
                            <label class="text-xs text-zinc-600 font-bold">2022-2023</label>
                            <input class="bg-white border border-zinc-200 rounded-lg p-3" placeholder="Établissement / Niveau" type="text" name="cursus_2022_2023" value="{{ old('cursus_2022_2023') }}" maxlength="150">
                        </div>
                        <div class="flex flex-col gap-2 bg-zinc-50 border border-zinc-200 rounded-xl p-4">
                            <label class="text-xs text-zinc-600 font-bold">2021-2022</label>
                            <input class="bg-white border border-zinc-200 rounded-lg p-3" placeholder="Établissement / Niveau" type="text" name="cursus_2021_2022" value="{{ old('cursus_2021_2022') }}" maxlength="150">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= ÉTAPE 3 : LOGISTIQUE & FINANCEMENT ================= -->
            <div x-show="formStep === 3" x-ref="step3" style="display: none;" class="space-y-10">
                <div class="mb-2">
                    <h1 class="text-2xl font-bold text-[#af101a] font-montserrat">Logistique & Financement</h1>
                    <p class="text-zinc-500 font-inter mt-1">Veuillez compléter les informations relatives à votre séjour et au règlement.</p>
                </div>

                <!-- Hébergement -->
                <div class="rounded-2xl border border-zinc-100 p-6 space-y-4">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[#af101a]">home</span>
                        <h3 class="text-lg font-bold text-[#af101a] font-montserrat">Hébergement</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex flex-col gap-2">
                            <label class="text-xs text-zinc-600 font-semibold">Type d'hébergement</label>
                            <select class="bg-zinc-50 border border-zinc-200 rounded-xl p-4" name="type_hebergement">
                                @foreach (['Domicile parental', 'Internat', 'Location / Colocation', 'Chez un proche', 'Autre'] as $type)
                                    <option @selected(old('type_hebergement') === $type)>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-xs text-zinc-600 font-semibold">Quartier de résidence</label>
                            <input class="bg-zinc-50 border border-zinc-200 rounded-xl p-4" placeholder="Ex: Quartier haoussa, Evechet..." type="text" name="quartier_residence" value="{{ old('quartier_residence') }}" maxlength="150">
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-xs text-zinc-600 font-semibold">Précisions (si Autre)</label>
                        <textarea class="bg-zinc-50 border border-zinc-200 rounded-xl p-4 min-h-[90px]" placeholder="Détails complémentaires sur votre logement..." name="hebergement_precisions" maxlength="1000">{{ old('hebergement_precisions') }}</textarea>
                    </div>
                </div>

                <!-- Financement des études -->
                <div class="rounded-2xl border border-zinc-100 p-6 space-y-6">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[#af101a]">account_balance</span>
                        <h3 class="text-lg font-bold text-[#af101a] font-montserrat">Financement des études</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all" :class="financement === 'personnel' ? 'border-[#af101a] bg-[#af101a]/5' : 'border-zinc-200 hover:bg-zinc-50'">
                            <input type="radio" name="financement" value="personnel" x-model="financement" class="mt-1 w-4 h-4 text-[#af101a] focus:ring-[#af101a]/30">
                            <span>
                                <span class="block font-bold text-zinc-900 text-sm">Prise en charge par vous-même</span>
                                <span class="block text-xs text-zinc-500 mt-0.5">Financement personnel ou familial direct</span>
                            </span>
                        </label>
                        <label class="flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all" :class="financement === 'employeur' ? 'border-[#af101a] bg-[#af101a]/5' : 'border-zinc-200 hover:bg-zinc-50'">
                            <input type="radio" name="financement" value="employeur" x-model="financement" class="mt-1 w-4 h-4 text-[#af101a] focus:ring-[#af101a]/30">
                            <span>
                                <span class="block font-bold text-zinc-900 text-sm">Prise en charge par employeur</span>
                                <span class="block text-xs text-zinc-500 mt-0.5">Formation financée par votre entreprise</span>
                            </span>
                        </label>
                        <label class="flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all" :class="financement === 'bourse' ? 'border-[#af101a] bg-[#af101a]/5' : 'border-zinc-200 hover:bg-zinc-50'">
                            <input type="radio" name="financement" value="bourse" x-model="financement" class="mt-1 w-4 h-4 text-[#af101a] focus:ring-[#af101a]/30">
                            <span>
                                <span class="block font-bold text-zinc-900 text-sm">Bailleur de fonds / Bourse</span>
                                <span class="block text-xs text-zinc-500 mt-0.5">Organisme tiers ou programme de bourse</span>
                            </span>
                        </label>
                        <label class="flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all" :class="financement === 'autre' ? 'border-[#af101a] bg-[#af101a]/5' : 'border-zinc-200 hover:bg-zinc-50'">
                            <input type="radio" name="financement" value="autre" x-model="financement" class="mt-1 w-4 h-4 text-[#af101a] focus:ring-[#af101a]/30">
                            <span>
                                <span class="block font-bold text-zinc-900 text-sm">Autres</span>
                                <span class="block text-xs text-zinc-500 mt-0.5">Précisez dans le champ ci-dessous</span>
                            </span>
                        </label>
                    </div>
                    @error('financement') <p class="text-xs text-red-500">{{ $message }}</p> @enderror

                    <div class="flex flex-col gap-2">
                        <label class="text-xs text-zinc-600 font-semibold">Mode de paiement envisagé</label>
                        <div class="flex flex-wrap gap-6">
                            @foreach (['especes' => 'Espèces / Cash', 'mandat' => 'Mandat', 'cheque' => 'Chèque', 'virement' => 'Virement / Caisse'] as $value => $label)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="paiement[]" value="{{ $value }}" @checked(in_array($value, old('paiement', []))) class="w-4 h-4 rounded border-zinc-300 text-[#af101a] focus:ring-[#af101a]/30">
                                    <span class="text-sm text-zinc-700">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Mobilité Internationale -->
                <div class="rounded-2xl border border-zinc-100 p-6 flex items-center justify-between gap-6 flex-wrap">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[#af101a]">public</span>
                        <div>
                            <h3 class="text-sm font-bold text-zinc-900 font-montserrat">Mobilité Internationale</h3>
                            <p class="text-xs text-zinc-500 mt-0.5">Avez-vous déjà pensé à poursuivre vos études à l'étranger ?</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <button type="button" @click="mobilite = 'oui'" :class="mobilite === 'oui' ? 'border-[#af101a] bg-[#af101a] text-white' : 'border-zinc-200 text-zinc-600 hover:bg-zinc-50'" class="px-6 py-2 rounded-full border-2 font-bold text-sm transition-all cursor-pointer">Oui</button>
                        <button type="button" @click="mobilite = 'non'" :class="mobilite === 'non' ? 'border-[#af101a] bg-[#af101a] text-white' : 'border-zinc-200 text-zinc-600 hover:bg-zinc-50'" class="px-6 py-2 rounded-full border-2 font-bold text-sm transition-all cursor-pointer">Non</button>
                    </div>
                    <input type="hidden" name="mobilite_internationale" :value="mobilite">
                </div>

                <!-- Contact d'urgence -->
                <div class="rounded-2xl border border-zinc-100 p-6 space-y-4">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[#af101a]">emergency</span>
                        <h3 class="text-lg font-bold text-[#af101a] font-montserrat">Contact d'urgence</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex flex-col gap-2">
                            <label class="text-xs text-zinc-600 font-semibold">Nom complet du contact</label>
                            <input class="bg-zinc-50 border border-zinc-200 rounded-xl p-4" type="text" name="contact_urgence_nom" value="{{ old('contact_urgence_nom') }}" maxlength="150">
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-xs text-zinc-600 font-semibold">Téléphone d'urgence</label>
                            <input class="bg-zinc-50 border border-zinc-200 rounded-xl p-4" placeholder="+237 ..." type="text" name="contact_urgence_telephone" value="{{ old('contact_urgence_telephone') }}" @blur="validateField('contact_urgence_telephone', $event.target.value)" maxlength="30">
                            <p x-show="fieldErrors.contact_urgence_telephone" x-text="fieldErrors.contact_urgence_telephone" class="text-xs text-red-500 mt-1"></p>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-xs text-zinc-600 font-semibold">Adresse Email</label>
                        <input class="bg-zinc-50 border {{ $errors->has('contact_urgence_email') ? 'border-red-500' : 'border-zinc-200' }} rounded-xl p-4" placeholder="contact@exemple.com" type="email" name="contact_urgence_email" value="{{ old('contact_urgence_email') }}" @blur="validateField('contact_urgence_email', $event.target.value)" maxlength="150">
                        @error('contact_urgence_email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        <p x-show="fieldErrors.contact_urgence_email" x-text="fieldErrors.contact_urgence_email" class="text-xs text-red-500 mt-1"></p>
                    </div>
                </div>

                <!-- Déclaration -->
                <div class="rounded-2xl border-2 border-dashed border-[#af101a]/30 bg-[#af101a]/[0.03] p-6 space-y-6">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="declaration_honneur" value="1" @checked(old('declaration_honneur')) required class="mt-1 w-4 h-4 rounded border-zinc-300 text-[#af101a] focus:ring-[#af101a]/30">
                        <span class="text-sm text-zinc-700 leading-relaxed">
                            <span class="font-bold">NB : À REMPLIR OBLIGATOIREMENT.</span>
                            Je déclare sur l'honneur que les renseignements fournis à l'appui de cette demande sont complets et exacts. Toute fausse déclaration entraînera l'annulation immédiate de ma candidature.
                            <span class="block text-xs text-zinc-500 mt-2">Votre signature manuscrite sera à apposer sur le PDF récapitulatif imprimé, le jour de votre passage à l'école.</span>
                        </span>
                    </label>
                    @error('declaration_honneur') <p class="text-xs text-red-500">{{ $message }}</p> @enderror

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex flex-col gap-2">
                            <label class="text-xs text-zinc-600 font-semibold">Fait à (Ville)</label>
                            <input class="bg-white border border-zinc-200 rounded-xl p-4" type="text" name="fait_a" value="{{ old('fait_a') }}" maxlength="100">
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-xs text-zinc-600 font-semibold">Date de signature</label>
                            <input class="bg-white border border-zinc-200 rounded-xl p-4" type="date" name="date_signature" value="{{ old('date_signature') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Message d'erreur d'étape (client, avant même d'envoyer au serveur) -->
            <template x-if="stepErrors.length">
                <div class="px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm font-semibold" x-text="stepErrors[0]"></div>
            </template>

            <!-- ================= Boutons de navigation ================= -->
            <div class="flex justify-between items-center pt-10 border-t border-zinc-100">
                <button
                    type="button"
                    @click="prev('{{ route('student-dashboard.dashboard') }}')"
                    class="text-zinc-600 font-bold flex items-center gap-2 hover:text-[#af101a] transition-colors cursor-pointer"
                >
                    <span class="material-symbols-outlined">arrow_back</span>
                    <span x-text="formStep === 1 ? 'Annuler' : (formStep === 2 ? 'Retour au tableau de bord' : 'Retour')"></span>
                </button>

                <button
                    type="submit"
                    @click="
                        if (formStep < 3) { $event.preventDefault(); next(); }
                        else if (!validateStep(3)) { $event.preventDefault(); }
                    "
                    class="bg-[#af101a] text-white px-8 py-4 rounded-xl font-bold flex items-center gap-3 shadow-xl shadow-[#af101a]/20 hover:scale-105 transition-all cursor-pointer"
                >
                    <span x-text="formStep === 3 ? 'Valider ma pré-inscription' : 'Continuer vers l\'étape ' + (formStep + 1)"></span>
                    <span class="material-symbols-outlined" x-text="formStep === 3 ? 'check_circle' : 'arrow_forward'"></span>
                </button>
            </div>

        </form>
    </div>
</div>
@endsection

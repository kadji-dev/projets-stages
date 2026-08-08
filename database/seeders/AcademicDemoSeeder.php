<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Cursus;
use App\Models\Field;
use App\Models\Level;
use App\Models\Speciality;
use Illuminate\Database\Seeder;

class AcademicDemoSeeder extends Seeder
{
    public function run(): void
    {
        AcademicYear::create([
            'label' => '2025-2026',
            'start_date' => '2025-09-01',
            'end_date' => '2026-06-30',
            'is_current' => true,
        ]);

        AcademicYear::create([
            'label' => '2024-2025',
            'start_date' => '2024-09-02',
            'end_date' => '2025-06-28',
            'is_current' => false,
        ]);

        $licence = Cursus::create(['code' => 'LICENCE', 'label' => 'Licence', 'duration_years' => 3]);
        $bts = Cursus::create(['code' => 'BTS', 'label' => 'Brevet de Technicien Supérieur', 'duration_years' => 2]);

        $giLicence = Field::create(['cursus_id' => $licence->id, 'code' => 'GI', 'label' => 'Génie Informatique']);
        $giBts = Field::create(['cursus_id' => $bts->id, 'code' => 'GI', 'label' => 'Génie Informatique']);

        $gl = Speciality::create(['field_id' => $giLicence->id, 'code' => 'GL', 'label' => 'Génie Logiciel']);
        Speciality::create(['field_id' => $giLicence->id, 'code' => 'RSI', 'label' => 'Réseaux & Systèmes Informatiques']);

        Level::create(['field_id' => $giLicence->id, 'speciality_id' => null, 'code' => 'L1', 'label' => 'Licence 1', 'order' => 1]);
        Level::create(['field_id' => $giLicence->id, 'speciality_id' => null, 'code' => 'L2', 'label' => 'Licence 2', 'order' => 2]);
        Level::create(['field_id' => $giLicence->id, 'speciality_id' => $gl->id, 'code' => 'L3', 'label' => 'Licence 3', 'order' => 3]);

        Level::create(['field_id' => $giBts->id, 'speciality_id' => null, 'code' => 'BTS1', 'label' => 'BTS 1ère Année', 'order' => 1]);
        Level::create(['field_id' => $giBts->id, 'speciality_id' => null, 'code' => 'BTS2', 'label' => 'BTS 2ème Année', 'order' => 2]);
    }
}

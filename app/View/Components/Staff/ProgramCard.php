<?php

namespace App\View\Components\Staff;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProgramCard extends Component
{
    public function __construct(
        public string $code,
        public string $libelle,
        public string $diplome,
        public int $semestres,
        public int $specialites,
        public string $editRoute = '#',
        public string $deleteRoute = '#',
    ) {}

    public function render(): View
    {
        return view('components.staff.program-card');
    }
}

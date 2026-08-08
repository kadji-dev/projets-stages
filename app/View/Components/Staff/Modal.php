<?php

namespace App\View\Components\Staff;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Modal extends Component
{
    public function __construct(
        public string $title = 'Formulaire',
    ) {}

    public function render(): View
    {
        return view('components.staff.modal');
    }
}

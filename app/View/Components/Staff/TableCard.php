<?php

namespace App\View\Components\Staff;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TableCard extends Component
{
    public function __construct(
        public ?string $title = null,
        public ?string $icon = null,
    ) {}

    public function render(): View
    {
        return view('components.staff.table-card');
    }
}

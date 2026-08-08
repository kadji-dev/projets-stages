<?php

namespace App\View\Components\Staff;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class StatCard extends Component
{
    public function __construct(
        public string $label,
        public string|int $value,
        public string $valueClass = 'text-zinc-900',
    ) {}

    public function render(): View
    {
        return view('components.staff.stat-card');
    }
}

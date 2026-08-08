<?php

namespace App\View\Components\Staff;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class StatusBadge extends Component
{
    public function __construct(
        public string $status = 'default',
    ) {}

    public function render(): View
    {
        return view('components.staff.status-badge');
    }
}

<?php

namespace App\View\Components\Staff;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PageHeader extends Component
{
    public function __construct(
        public string $title,
        public ?string $subtitle = null,
        public ?string $actionLabel = null,
        public string $actionIcon = 'add',
        public ?string $actionUrl = null,
    ) {}

    public function render(): View
    {
        return view('components.staff.page-header');
    }
}

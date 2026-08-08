<?php

namespace App\View\Components\Staff;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Footer extends Component
{
    public function render(): View
    {
        return view('components.staff.footer');
    }
}

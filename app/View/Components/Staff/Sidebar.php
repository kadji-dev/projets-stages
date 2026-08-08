<?php

namespace App\View\Components\Staff;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Sidebar extends Component
{
    public function render(): View
    {
        return view('components.staff.sidebar');
    }
}

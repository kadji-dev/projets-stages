<?php

namespace App\View\Components\Staff;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AddButton extends Component
{
    public function render(): View
    {
        return view('components.staff.add-button');
    }
}

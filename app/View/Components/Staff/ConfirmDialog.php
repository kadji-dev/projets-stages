<?php

namespace App\View\Components\Staff;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ConfirmDialog extends Component
{
    public function render(): View
    {
        return view('components.staff.confirm-dialog');
    }
}

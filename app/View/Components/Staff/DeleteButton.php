<?php

namespace App\View\Components\Staff;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Str;

class DeleteButton extends Component
{
    public string $formId;

    public function __construct(
        public string $action,
        public string $title = 'Confirmer la suppression',
        public string $message = 'Cette action est irréversible.',
    ) {
        $this->formId = 'delete-form-'.Str::random(8);
    }

    public function render(): View
    {
        return view('components.staff.delete-button');
    }
}

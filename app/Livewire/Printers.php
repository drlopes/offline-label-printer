<?php

namespace App\Livewire;

use Mary\Traits\Toast;
use Native\Laravel\Facades\Settings;
use Native\Laravel\Facades\System;
use Livewire\Component;

class Printers extends Component
{
    use Toast;

    public array $printers = [];
    public ?string $selectedPrinter = null;

    public function mount()
    {
        $selectedPrinter = Settings::get('selected_printer', null);
        if ($selectedPrinter) {
            $this->selectedPrinter = $selectedPrinter;
        }
    }

    public function render()
    {
        $this->printers = [];
        $systemPrinters = System::printers();

        foreach ($systemPrinters as $printer) {
            $this->printers[] = [
                'name' => $printer->name,
                'displayName' => $printer->displayName,
                'isDefault' => $printer->isDefault,
                'status' => $printer->status,
                'description' => $printer->description != "" ? $printer->description : null,
                'isSelected' => $printer->displayName === $this->selectedPrinter
            ];
        }

        return view('livewire.printers');
    }

    public function selectPrinter($printer)
    {
        if ($this->selectedPrinter !== $printer) {
            Settings::set('selected_printer', $printer);
        }

        $this->selectedPrinter = $printer;

        $this->toast(
            type: 'success',
            title: 'Sucesso!',
            description: "Impressora alterada com sucesso",
            position: 'toast-bottom toast-end',
            icon: 'o-printer',
            css: 'alert-success',
            timeout: 3000,
            redirectTo: null,
        );

        $this->dispatch('updated');
    }
}

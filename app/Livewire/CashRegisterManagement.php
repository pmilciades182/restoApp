<?php

namespace App\Livewire;

use App\Models\CashRegister;
use Livewire\Component;
use App\Traits\HasBreadcrumbs;

class CashRegisterManagement extends Component
{
    use HasBreadcrumbs;

    public $initial_cash = '';
    public $final_cash = '';
    public $closing_notes = '';
    public $current_register = null;

    protected $rules = [
        'initial_cash' => 'required|numeric|min:0',
        'final_cash' => 'required|numeric|min:0',
        'closing_notes' => 'nullable|string',
    ];

    public function mount()
    {
        $this->loadCurrentRegister();
    }

    public function loadCurrentRegister()
    {
        $this->current_register = CashRegister::getOpenRegister();
    }

    public function openRegister()
    {
        $this->validate([
            'initial_cash' => 'required|numeric|min:0'
        ]);

        if (CashRegister::hasOpenRegister()) {
            session()->flash('error', 'Ya existe una caja abierta.');
            return;
        }

        CashRegister::create([
            'user_id' => auth()->id(),
            'initial_cash' => $this->initial_cash,
            'opened_at' => now(),
            'status' => 'open'
        ]);

        session()->flash('message', 'Caja abierta exitosamente.');
        $this->loadCurrentRegister();
        $this->reset('initial_cash');
    }

    public function closeRegister()
    {
        $this->validate([
            'final_cash' => 'required|numeric|min:0',
            'closing_notes' => 'nullable|string'
        ]);

        if (!$this->current_register) {
            session()->flash('error', 'No hay una caja abierta para cerrar.');
            return;
        }

        $this->current_register->close(
            $this->final_cash,
            $this->closing_notes
        );

        session()->flash('message', 'Caja cerrada exitosamente.');
        $this->loadCurrentRegister();
        $this->reset(['final_cash', 'closing_notes']);
    }

    protected function getBaseBreadcrumbs()
    {
        return [
            ['name' => 'Administración de Caja']
        ];
    }

    public function render()
    {
        return view('livewire.cash-register-management', [
            'breadcrumbs' => $this->getBaseBreadcrumbs()
        ])->layout('layouts.app');
    }
}

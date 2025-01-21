<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CashRegister;
use App\Traits\HasBreadcrumbs;

class CashRegisterTable extends Component
{
    use WithPagination, HasBreadcrumbs;

    public $search = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $status = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'status' => ['except' => '']
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    protected function getBaseBreadcrumbs()
    {
        return [
            ['name' => 'Caja'],
            ['name' => 'Registros']
        ];
    }

    public function render()
    {
        $registers = CashRegister::with('user')
            ->when($this->search, function($query) {
                $query->whereHas('user', function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->dateFrom, function($query) {
                $query->whereDate('opened_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function($query) {
                $query->whereDate('opened_at', '<=', $this->dateTo);
            })
            ->when($this->status, function($query) {
                $query->where('status', $this->status);
            })
            ->orderBy('opened_at', 'desc')
            ->paginate(10);

        return view('livewire.cash-register-table', [
            'registers' => $registers,
            'breadcrumbs' => $this->getBaseBreadcrumbs()
        ])->layout('layouts.app');
    }
}

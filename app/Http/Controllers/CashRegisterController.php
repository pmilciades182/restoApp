<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\Invoice;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashRegisterController extends Controller
{
    public function statement($id)
    {
        // Cargar la caja con sus relaciones
        $cashRegister = CashRegister::with(['user', 'invoices.payments.paymentMethod'])
            ->findOrFail($id);

        // Verificar que la caja esté cerrada
        if ($cashRegister->status !== 'closed') {
            return redirect()->route('cash-registers.index')
                ->with('error', 'Solo se puede ver la rendición de cajas cerradas.');
        }

        // Obtener todas las facturas de la caja
        $invoices = $cashRegister->invoices()
            ->with(['client', 'payments.paymentMethod'])
            ->where('status', 'paid')
            ->get();

        // Calcular totales por método de pago
        $paymentTotals = $invoices->flatMap->payments
            ->groupBy('payment_method_id')
            ->map(function ($payments) {
                return [
                    'method' => $payments->first()->paymentMethod->name,
                    'total' => $payments->sum('amount')
                ];
            });

        // Encontrar el total de pagos en efectivo de manera segura
        $cashPayments = $paymentTotals->first(function ($payment) {
            return strtolower($payment['method']) === 'efectivo';
        });
        $totalCashPayments = $cashPayments ? $cashPayments['total'] : 0;

        // Calcular balance
        $balance = [
            'initial_cash' => $cashRegister->initial_cash,
            'final_cash' => $cashRegister->final_cash,
            'total_sales' => $invoices->sum('total'),
            'total_collected' => $invoices->sum('amount_paid'),
            'difference' => $cashRegister->final_cash - $cashRegister->initial_cash,
            'expected_cash' => $cashRegister->initial_cash + $totalCashPayments,
            'cash_difference' => $cashRegister->final_cash - ($cashRegister->initial_cash + $totalCashPayments)
        ];

        // Agrupar facturas por estado de pago
        $invoicesByStatus = $invoices->groupBy('payment_status');

        return view('cash-register.statement', compact(
            'cashRegister',
            'balance',
            'paymentTotals',
            'invoices',
            'invoicesByStatus'
        ));
    }
}

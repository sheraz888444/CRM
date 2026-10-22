<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Admission;
use App\Models\Installment;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'admission_id' => ['required', 'exists:admissions,id'],
            'installment_id' => ['nullable', 'exists:installments,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_date' => ['required', 'date'],
            'mode' => ['required', 'in:Cash,Bank,Online,Cheque'],
            'type' => ['required', 'in:payment,refund'],
            'remarks' => ['nullable', 'string', 'max:500'],
        ]);

        return DB::transaction(function () use ($request) {
            $admission = Admission::findOrFail($request->admission_id);
            $amount = (float) $request->amount;

            // Enforce installment logic: If student has installments, they must pay installment-by-installment
            if ($admission->payment_type === 'installment') {
                $installmentId = $request->installment_id;

                if ($installmentId) {
                    $installment = Installment::where('id', $installmentId)
                        ->where('admission_id', $admission->id)
                        ->firstOrFail();
                } else {
                    $installment = $admission->installments()
                        ->where('status', '!=', 'paid')
                        ->orderBy('seq')
                        ->first();
                    $installmentId = $installment?->id;
                }

                if (!$installment) {
                    return redirect()->back()->with('error', 'All installments for this student have already been cleared in full.');
                }

                // Disallow paying more than this installment's exact remaining amount (preventing paying full course at once)
                $installmentRemaining = (float) $installment->remaining;
                if ($amount > $installmentRemaining) {
                    return redirect()->back()->with('error', "Installment #{$installment->seq} requires exact amount of PKR " . number_format($installmentRemaining) . ". You cannot pay the full course fee at once for an installment plan.");
                }
            } else {
                // Lump sum / upfront plan validation
                $remainingFee = (float) $admission->remaining;
                if ($amount > $remainingFee) {
                    return redirect()->back()->with('error', 'Payment cannot exceed the total remaining balance of PKR ' . number_format($remainingFee) . '.');
                }
                $installmentId = null;
            }

            $payment = Payment::create([
                'admission_id' => $admission->id,
                'installment_id' => $installmentId,
                'amount' => $amount,
                'payment_date' => $request->payment_date,
                'mode' => $request->mode,
                'received_by' => Auth::id(),
                'type' => $request->type ?? 'payment',
                'remarks' => $request->remarks,
            ]);

            // If an installment was selected or auto-linked, update its status
            if ($installmentId) {
                $installment = Installment::find($installmentId);
                $installment?->updateStatus();
            } else {
                // Refresh all installments if they exist
                foreach ($admission->installments as $inst) {
                    $inst->updateStatus();
                }
            }

            ActivityLog::record(
                'received_payment',
                "Recorded fee payment of PKR " . number_format($payment->amount) . " ({$payment->mode}) for Roll No: {$admission->roll_no}",
                ['admission_id' => $admission->id, 'payment_id' => $payment->id, 'amount' => $payment->amount]
            );

            return redirect()->route('admissions.show', $admission)
                ->with('success', "Payment of PKR " . number_format($payment->amount) . " recorded successfully! Receipt #{$payment->id} generated.");
        });
    }

    public function slip(Payment $payment): View
    {
        $payment->load(['admission.branch', 'admission.courses', 'admission.payments', 'receiver', 'installment']);

        return view('payments.slip', compact('payment'));
    }
}

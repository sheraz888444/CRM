@extends('layouts.app')

@section('title', 'Admission #' . $admission->roll_no . ' - EduTrack CRM')
@section('page_title', 'Admission File: ' . $admission->name)
@section('page_subtitle', 'Roll No: ' . $admission->roll_no . ' • Campus: ' . ($admission->branch->name ?? 'Main'))

@section('content')
@php
    $isStaff = Auth::user()->role === 'staff' || Auth::user()->role === 'branch_admin';
    $nextPendingInstallment = $admission->installments->first(fn($i) => $i->remaining > 0);
    $initialAmount = ($admission->payment_type === 'installment' && $nextPendingInstallment)
        ? (float) $nextPendingInstallment->remaining
        : (float) $admission->remaining;
    $initialInstId = ($admission->payment_type === 'installment' && $nextPendingInstallment)
        ? $nextPendingInstallment->id
        : 'null';
    $initialInstName = ($admission->payment_type === 'installment' && $nextPendingInstallment)
        ? "'Part #{$nextPendingInstallment->seq}'"
        : "''";
@endphp

<div class="space-y-6" x-data="{ paymentModal: false, selectedInstallment: {{ $initialInstId }}, selectedInstallmentName: {{ $initialInstName }}, paymentAmount: {{ $initialAmount }} }">

    <!-- Top Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-extrabold flex items-center justify-center text-xl shadow-md shadow-blue-500/20 shrink-0">
                {{ strtoupper(substr($admission->name, 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">{{ $admission->name }}</h3>
                    <span class="px-2.5 py-0.5 rounded-lg text-xs font-mono font-extrabold bg-blue-100 text-blue-800 border border-blue-200">
                        {{ $admission->roll_no }}
                    </span>
                    @if($admission->fee_status === 'paid')
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            Fully Paid
                        </span>
                    @elseif($admission->fee_status === 'overdue')
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
                            Dues Overdue
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                            Installments Pending
                        </span>
                    @endif
                </div>
                <div class="flex items-center gap-3 text-xs text-slate-500 mt-1">
                    <span>Campus: <strong class="text-slate-800">{{ $admission->branch->name ?? 'Main' }}</strong></span>
                    <span>•</span>
                    <span>Admitted on: <strong class="text-slate-800">{{ $admission->admission_date ? \Carbon\Carbon::parse($admission->admission_date)->format('M d, Y') : $admission->created_at->format('M d, Y') }}</strong></span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if($admission->remaining > 0)
                @if($isStaff)
                    <button @click="paymentModal = true; selectedInstallment = {{ $initialInstId }}; selectedInstallmentName = {{ $initialInstName }}; paymentAmount = {{ $initialAmount }};"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/25 transition-all">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Receive Fee Payment</span>
                    </button>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                        <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Supervisor View (Fee Managed by Staff)</span>
                    </span>
                @endif
            @endif

            <a href="{{ route('admissions.edit', $admission) }}" class="p-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition-colors border border-slate-200" title="Edit Admission">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </a>

            <form action="{{ route('admissions.destroy', $admission) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this admission record for {{ addslashes($admission->name) }} (Roll No: {{ $admission->roll_no }})?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="p-2.5 rounded-xl text-rose-600 hover:bg-rose-50 border border-slate-200 hover:border-rose-200 transition-colors" title="Delete Admission Record">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Financial KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Package -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Package</span>
            <div class="text-xl font-extrabold text-slate-900 mt-1">PKR {{ number_format($admission->total_package) }}</div>
            @if($admission->discount > 0)
                <div class="text-xs font-semibold text-rose-500 mt-0.5">- Discount: PKR {{ number_format($admission->discount) }}</div>
            @else
                <div class="text-xs text-slate-400 mt-0.5">No discount applied</div>
            @endif
        </div>

        <!-- Net Payable -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Net Payable Fee</span>
            <div class="text-xl font-extrabold text-slate-900 mt-1">PKR {{ number_format($admission->net_payable) }}</div>
            <div class="text-xs text-slate-500 mt-0.5 capitalize">{{ str_replace('_', ' ', $admission->payment_type) }}</div>
        </div>

        <!-- Total Paid -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold uppercase tracking-wider text-emerald-600">Total Fee Paid</span>
            <div class="text-xl font-extrabold text-emerald-600 mt-1">PKR {{ number_format($admission->total_received) }}</div>
            <div class="text-xs text-emerald-700/80 mt-0.5 font-medium">{{ $admission->payments->count() }} payments recorded</div>
        </div>

        <!-- Remaining Balance -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Remaining Balance</span>
            <div class="text-xl font-extrabold mt-1 {{ $admission->remaining > 0 ? 'text-amber-600' : 'text-slate-400' }}">
                PKR {{ number_format($admission->remaining) }}
            </div>
            <div class="text-xs font-semibold mt-0.5 {{ $admission->remaining > 0 ? 'text-amber-600' : 'text-emerald-600' }}">
                {{ $admission->remaining > 0 ? 'Pending collection' : 'Cleared in full ✓' }}
            </div>
        </div>
    </div>

    <!-- Student Details & Enrolled Courses Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left 2 Cols: Installment Schedule & Payments -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Installment Schedule -->
            @if($admission->payment_type === 'installment')
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h4 class="text-base font-bold text-slate-900">Fee Installment Plan</h4>
                            <p class="text-xs text-slate-500">Scheduled payment breakdown, due dates, and status</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                                <tr>
                                    <th class="px-5 py-3">Installment</th>
                                    <th class="px-4 py-3">Due Date</th>
                                    <th class="px-4 py-3">Amount</th>
                                    <th class="px-4 py-3">Paid</th>
                                    <th class="px-4 py-3">Remaining</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-5 py-3 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($admission->installments as $inst)
                                    @php
                                        $instPaid = ($inst->status === 'paid' || $inst->remaining <= 0);
                                    @endphp
                                    <tr class="{{ $instPaid ? 'bg-emerald-50/50 hover:bg-emerald-100/60 border-l-4 border-l-emerald-500' : 'bg-rose-50/60 hover:bg-rose-100/70 border-l-4 border-l-rose-500' }} transition-colors">
                                        <td class="px-5 py-3.5 font-bold text-slate-900">
                                            Part #{{ $inst->seq }}
                                        </td>
                                        <td class="px-4 py-3.5 text-xs font-medium text-slate-700">
                                            {{ \Carbon\Carbon::parse($inst->due_date)->format('M d, Y') }}
                                            @if($inst->is_overdue)
                                                <span class="block text-[10px] font-bold text-rose-500">Overdue!</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3.5 font-bold text-slate-900">
                                            PKR {{ number_format($inst->amount) }}
                                        </td>
                                        <td class="px-4 py-3.5 text-xs font-semibold text-emerald-600">
                                            PKR {{ number_format($inst->total_paid) }}
                                        </td>
                                        <td class="px-4 py-3.5 text-xs font-bold text-slate-700">
                                            PKR {{ number_format($inst->remaining) }}
                                        </td>
                                        <td class="px-4 py-3.5 whitespace-nowrap">
                                            @if($inst->status === 'paid')
                                                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Paid</span>
                                            @elseif($inst->status === 'overdue' || $inst->is_overdue)
                                                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">Overdue</span>
                                            @elseif($inst->status === 'partial')
                                                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">Partial</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">Pending</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                            @if($inst->remaining > 0)
                                                @if($isStaff)
                                                    <button @click="paymentModal = true; selectedInstallment = {{ $inst->id }}; selectedInstallmentName = 'Part #{{ $inst->seq }}'; paymentAmount = {{ $inst->remaining }};"
                                                            class="px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-xs transition-colors">
                                                        Pay Part #{{ $inst->seq }}
                                                    </button>
                                                @else
                                                    <span class="text-xs text-amber-700 font-semibold bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200">
                                                        PKR {{ number_format($inst->remaining) }} Due
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-xs text-emerald-700 font-bold bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">✓ Paid</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-5 py-8 text-center text-slate-400">
                                            No installments generated for this admission.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Payment History & Receipts -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h4 class="text-base font-bold text-slate-900">Fee Payment History & Receipts</h4>
                        <p class="text-xs text-slate-500">Official voucher slips generated for each transaction</p>
                    </div>

                    @if($isStaff && $admission->remaining > 0)
                        <button @click="paymentModal = true; selectedInstallment = {{ $initialInstId }}; selectedInstallmentName = {{ $initialInstName }}; paymentAmount = {{ $initialAmount }};"
                                class="px-3 py-1.5 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700">
                            + Add Payment
                        </button>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                            <tr>
                                <th class="px-5 py-3">Receipt #</th>
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Amount Paid</th>
                                <th class="px-4 py-3">Method</th>
                                <th class="px-4 py-3">Received By</th>
                                <th class="px-5 py-3 text-right">Receipt Slip</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($admission->payments as $payment)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-5 py-3.5 font-mono text-xs font-bold text-blue-600">
                                        REC-{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="px-4 py-3.5 text-xs text-slate-700">
                                        {{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}
                                    </td>
                                    <td class="px-4 py-3.5 font-extrabold text-emerald-600">
                                        PKR {{ number_format($payment->amount) }}
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-700">
                                            {{ $payment->mode }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5 text-xs text-slate-600">
                                        {{ $payment->receiver->name ?? 'Front Desk' }}
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <a href="{{ route('payments.slip', $payment) }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold text-blue-600 hover:text-blue-800 hover:bg-blue-50 border border-blue-200 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                            <span>Print Slip</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-8 text-center text-slate-400">
                                        No payments recorded yet. Click "Receive Fee Payment" to record a transaction.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Right 1 Col: Student Profile & Enrolled Courses -->
        <div class="space-y-6">

            <!-- Enrolled Courses Card -->
            <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Enrolled Programs</h4>

                <div class="space-y-2">
                    @forelse($admission->courses as $c)
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-indigo-50/50 border border-indigo-100">
                            <div>
                                <div class="text-sm font-bold text-slate-900">{{ $c->name }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $c->code }}</div>
                            </div>
                            <div class="text-xs font-extrabold text-indigo-700">
                                PKR {{ number_format($c->default_fee) }}
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400">No courses linked.</p>
                    @endforelse
                </div>
            </div>

            <!-- Student Profile Information -->
            <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs space-y-4 text-xs">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Student Particulars</h4>

                <div class="space-y-3">
                    <div>
                        <span class="text-slate-400 block">Father / Guardian</span>
                        <span class="font-bold text-slate-800 text-sm">{{ $admission->father_name ?: 'Not recorded' }}</span>
                    </div>

                    <div>
                        <span class="text-slate-400 block">Phone / WhatsApp</span>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $admission->phone) }}" target="_blank" class="font-mono font-bold text-blue-600 hover:underline flex items-center gap-1.5 mt-0.5">
                            <span>{{ $admission->phone }}</span>
                            <span class="text-[10px] px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-800">WhatsApp</span>
                        </a>
                    </div>

                    @if($admission->alt_phone)
                        <div>
                            <span class="text-slate-400 block">Alternate Contact</span>
                            <span class="font-mono font-semibold text-slate-700">{{ $admission->alt_phone }}</span>
                        </div>
                    @endif

                    @if($admission->email)
                        <div>
                            <span class="text-slate-400 block">Email</span>
                            <span class="text-slate-700">{{ $admission->email }}</span>
                        </div>
                    @endif

                    @if($admission->qualification)
                        <div>
                            <span class="text-slate-400 block">Qualification</span>
                            <span class="font-semibold text-slate-800">{{ $admission->qualification }}</span>
                        </div>
                    @endif

                    @if($admission->address)
                        <div>
                            <span class="text-slate-400 block">Address</span>
                            <span class="text-slate-700">{{ $admission->address }}</span>
                        </div>
                    @endif

                    @if($admission->inquiry)
                        <div class="pt-3 border-t border-slate-100">
                            <span class="text-slate-400 block">Linked Inquiry</span>
                            <a href="{{ route('students.show', $admission->inquiry) }}" class="font-bold text-blue-600 hover:underline">
                                Inquiry #{{ $admission->inquiry->id }} ({{ $admission->inquiry->student_name }})
                            </a>
                        </div>
                    @endif
                </div>
            </div>

        </div>

    </div>

    <!-- Record Payment Modal -->
    <div x-show="paymentModal" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.away="paymentModal = false"
             class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-100 transition-all">

            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                <div>
                    <h4 class="text-base font-bold text-slate-900">Record Fee Collection</h4>
                    <p class="text-xs text-slate-500">Roll No: {{ $admission->roll_no }} ({{ $admission->name }})</p>
                </div>
                <button @click="paymentModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('payments.store') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="admission_id" value="{{ $admission->id }}">
                <input type="hidden" name="installment_id" :value="selectedInstallment">
                <input type="hidden" name="type" value="payment">

                @if($admission->payment_type === 'installment')
                    <div class="p-3.5 bg-purple-50 border border-purple-200 rounded-2xl text-xs space-y-1">
                        <div class="flex items-center justify-between font-bold text-purple-900">
                            <span class="flex items-center gap-1.5">
                                <span>🟣 Installment Mode:</span>
                                <span class="px-2 py-0.5 rounded-lg bg-purple-200 text-purple-900 font-mono text-xs" x-text="selectedInstallmentName || 'Scheduled Part'"></span>
                            </span>
                        </div>
                        <p class="text-purple-700 text-[11px]">Installment fee must be paid part-by-part for the exact installment amount. Full course fee cannot be paid all at once.</p>
                    </div>
                @endif

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Amount (PKR) <span class="text-rose-500">*</span>
                        @if($admission->payment_type === 'installment')
                            <span class="text-purple-700 font-semibold lowercase">(Installment due amount)</span>
                        @endif
                    </label>
                    <input type="number" name="amount" x-model.number="paymentAmount" required min="1" step="any"
                           @if($admission->payment_type === 'installment') :max="paymentAmount" @endif
                           class="w-full text-lg font-bold px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-600 outline-none">
                    @if($admission->payment_type === 'installment')
                        <p class="text-[11px] text-slate-500 mt-1">Exact payable for this installment: <strong class="text-slate-800" x-text="'PKR ' + Number(paymentAmount).toLocaleString()"></strong></p>
                    @endif
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Payment Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-600 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Payment Mode <span class="text-rose-500">*</span></label>
                    <select name="mode" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold focus:ring-2 focus:ring-emerald-600 outline-none">
                        <option value="Cash">Cash (Front Desk)</option>
                        <option value="Bank">Bank Deposit / Transfer</option>
                        <option value="Online">Online Gateway / Mobile Wallet</option>
                        <option value="Cheque">Cheque</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Remarks / Notes</label>
                    <textarea name="remarks" rows="2" placeholder="e.g. Received via JazzCash / Bank slip #12345"
                              class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-600 outline-none"></textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="paymentModal = false" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-100">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/25">
                        Save Payment & Issue Receipt
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

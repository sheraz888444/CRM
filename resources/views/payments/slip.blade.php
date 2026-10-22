<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Receipt #{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }} - {{ $payment->admission->name }}</title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #0f172a; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        @media print {
            body { background: white !important; padding: 0 !important; }
            .no-print { display: none !important; }
            .print-shadow-none { box-shadow: none !important; border-color: #cbd5e1 !important; }
        }
    </style>
</head>
<body class="p-6 md:p-12">

    <!-- Action Bar (Hidden on print) -->
    <div class="max-w-2xl mx-auto mb-6 flex items-center justify-between no-print">
        <a href="{{ route('admissions.show', $payment->admission) }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-600 hover:text-slate-900">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Back to Admission File</span>
        </a>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-600/25 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Print Receipt Slip</span>
            </button>
        </div>
    </div>

    <!-- Official Voucher Container -->
    <div class="max-w-2xl mx-auto bg-white rounded-3xl border border-slate-200 p-8 sm:p-10 shadow-xl print-shadow-none relative">
        
        <!-- Header -->
        <div class="flex items-start justify-between border-b border-slate-200 pb-6">
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xl font-black text-slate-900 tracking-tight">EduTrack</span>
                    <span class="text-xs font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-blue-100 text-blue-700">ACADEMY</span>
                </div>
                <h2 class="text-sm font-bold text-slate-700 mt-1">{{ \App\Models\Setting::get('school_name', 'Institute of Language & Test Preparation') }}</h2>
                <p class="text-xs text-slate-500 mt-0.5">{{ $payment->admission->branch->address ?? 'Main Campus, Lahore' }}</p>
                <p class="text-xs text-slate-500">Helpline: {{ $payment->admission->branch->phone ?? '042-35780000' }}</p>
            </div>

            <div class="text-right">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-slate-900 text-white mb-2">
                    Official Fee Receipt
                </span>
                <div class="font-mono text-xs font-bold text-slate-900">
                    Receipt #: <span class="text-blue-600">REC-{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="text-xs text-slate-500 mt-0.5">
                    Date: {{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}
                </div>
            </div>
        </div>

        <!-- Student Particulars -->
        <div class="grid grid-cols-2 gap-4 py-6 border-b border-slate-100 text-xs">
            <div>
                <span class="text-slate-400 block text-[11px] uppercase tracking-wider font-semibold">Student Name</span>
                <span class="text-sm font-extrabold text-slate-900 block mt-0.5">{{ $payment->admission->name }}</span>
                <span class="text-slate-500">S/O, D/O: {{ $payment->admission->father_name ?: '—' }}</span>
            </div>

            <div class="text-right">
                <span class="text-slate-400 block text-[11px] uppercase tracking-wider font-semibold">Roll Number</span>
                <span class="font-mono text-base font-black text-blue-600 block mt-0.5">{{ $payment->admission->roll_no }}</span>
                <span class="text-slate-500">Campus: {{ $payment->admission->branch->name ?? 'Main' }}</span>
            </div>

            <div>
                <span class="text-slate-400 block text-[11px] uppercase tracking-wider font-semibold">Phone Contact</span>
                <span class="font-mono text-slate-800 font-bold">{{ $payment->admission->phone }}</span>
            </div>

            <div class="text-right">
                <span class="text-slate-400 block text-[11px] uppercase tracking-wider font-semibold">Enrolled Program(s)</span>
                <span class="text-slate-800 font-bold">
                    {{ $payment->admission->courses->pluck('name')->implode(', ') }}
                </span>
            </div>
        </div>

        <!-- Payment Particulars Table -->
        <div class="py-6 border-b border-slate-200">
            <table class="w-full text-xs">
                <thead>
                    <tr class="text-slate-400 uppercase text-[10px] font-bold tracking-wider border-b border-slate-100 pb-2">
                        <th class="text-left pb-2">Description</th>
                        <th class="text-center pb-2">Payment Mode</th>
                        <th class="text-right pb-2">Amount Paid</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    <tr>
                        <td class="py-3">
                            <span class="font-bold text-slate-900 block">
                                Fee Collection ({{ $payment->type === 'refund' ? 'Refund' : 'Tuition / Course Fee' }})
                            </span>
                            @if($payment->installment)
                                <span class="text-[11px] text-slate-500">Against Installment Part #{{ $payment->installment->seq }}</span>
                            @endif
                            @if($payment->remarks)
                                <span class="text-[11px] text-slate-400 block italic">{{ $payment->remarks }}</span>
                            @endif
                        </td>
                        <td class="text-center py-3 font-semibold text-slate-700">
                            {{ $payment->mode }}
                        </td>
                        <td class="text-right py-3 text-sm font-extrabold text-emerald-600">
                            PKR {{ number_format($payment->amount) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Financial Summary Breakdown -->
        <div class="py-5 border-b border-slate-200 flex justify-end">
            <div class="w-64 space-y-2 text-xs">
                <div class="flex justify-between text-slate-500">
                    <span>Total Package:</span>
                    <span class="font-semibold text-slate-800">PKR {{ number_format($payment->admission->total_package) }}</span>
                </div>

                @if($payment->admission->discount > 0)
                    <div class="flex justify-between text-rose-500">
                        <span>Discount Allowed:</span>
                        <span class="font-semibold">- PKR {{ number_format($payment->admission->discount) }}</span>
                    </div>
                @endif

                <div class="flex justify-between font-bold text-slate-900 pt-1 border-t border-slate-100">
                    <span>Net Payable:</span>
                    <span>PKR {{ number_format($payment->admission->net_payable) }}</span>
                </div>

                <div class="flex justify-between font-bold text-emerald-700 bg-emerald-50 px-2 py-1 rounded-lg">
                    <span>This Payment:</span>
                    <span>PKR {{ number_format($payment->amount) }}</span>
                </div>

                <div class="flex justify-between text-slate-600">
                    <span>Total Paid to Date:</span>
                    <span class="font-semibold">PKR {{ number_format($payment->admission->total_received) }}</span>
                </div>

                <div class="flex justify-between font-extrabold text-slate-900 pt-1 border-t border-slate-200">
                    <span>Remaining Balance:</span>
                    <span class="{{ $payment->admission->remaining > 0 ? 'text-amber-600' : 'text-emerald-600' }}">
                        PKR {{ number_format($payment->admission->remaining) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Footer & Signatures -->
        <div class="pt-10 flex items-end justify-between text-xs text-slate-400">
            <div class="text-center">
                <div class="w-40 border-b border-slate-300 mb-1"></div>
                <span>Student / Depositor</span>
            </div>

            <div class="text-center">
                <div class="w-40 border-b border-slate-300 mb-1"></div>
                <span class="font-semibold text-slate-700">{{ $payment->receiver->name ?? 'Accounts Officer' }}</span>
                <span class="block text-[10px]">Authorized Signature</span>
            </div>
        </div>

        <div class="mt-8 text-center text-[10px] text-slate-400 border-t border-slate-100 pt-3">
            This is a computer-generated official receipt. Fees once paid are non-refundable & non-transferable.
        </div>

    </div>

</body>
</html>

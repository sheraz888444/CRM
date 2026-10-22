<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $user->name }} - Inquiries & Performance Audit Report - {{ date('Y-m-d') }}</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])

    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; font-size: 9.5pt; padding: 0 !important; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
            @page {
                size: A4 landscape;
                margin: 12mm 10mm;
            }
        }
    </style>
</head>
<body class="bg-slate-100 p-6 sm:p-8 text-slate-800 font-sans">

    <!-- Top floating print button for screen view -->
    <div class="no-print max-w-7xl mx-auto mb-6 flex items-center justify-between bg-white p-4 rounded-2xl shadow-md border border-slate-200">
        <div>
            <h2 class="text-sm font-bold text-slate-900">Counselor Performance & Inquiry Audit Sheet Preview</h2>
            <p class="text-xs text-slate-500">Live inquiry log and fee audit for <strong>{{ $user->name }}</strong> ({{ $user->branch?->name ?? 'All Campuses' }})</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-5 py-2.5 rounded-xl font-bold text-xs text-white bg-blue-600 hover:bg-blue-700 shadow-md transition-all">
                🖨 Print / Save as PDF
            </button>
            <button onclick="window.close()" class="px-4 py-2.5 rounded-xl font-semibold text-xs text-slate-600 bg-slate-100 hover:bg-slate-200 transition-all">
                Close Window
            </button>
        </div>
    </div>

    <!-- Printable Paper Sheet -->
    <div class="max-w-7xl mx-auto bg-white p-6 sm:p-10 rounded-2xl shadow-lg border border-slate-200 print:shadow-none print:border-none print:p-0">
        
        <!-- School & Staff Report Header -->
        <div class="border-b-2 border-slate-900 pb-4 mb-4 flex items-start justify-between gap-6">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ $schoolName ?? \App\Models\Setting::get('school_name', config('app.name', 'EduTrack CRM Institute')) }}</h1>
                <p class="text-sm font-bold text-blue-700 mt-0.5">Staff Counselor Performance & Student Inquiry Financial Audit Sheet</p>
                <div class="text-xs text-slate-600 mt-2 space-y-0.5">
                    <p>Counselor: <strong class="text-slate-900">{{ $user->name }}</strong> ({{ ucfirst(str_replace('_', ' ', $user->role)) }})</p>
                    <p>Assigned Campus: <strong>{{ $user->branch?->name ?? 'All Campuses (Universal)' }}</strong> • Email/User: {{ $user->username ? '@' . $user->username : $user->email }}</p>
                    <p class="text-[11px] text-slate-400">Generated on: {{ date('F j, Y • g:i A') }}</p>
                </div>
            </div>

            <!-- Summary KPI Box -->
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs text-slate-700 min-w-[260px] space-y-1">
                <div class="flex justify-between border-b border-slate-200 pb-1 font-bold text-slate-900">
                    <span>Total Inquiries:</span>
                    <span>{{ $totalInquiries }}</span>
                </div>
                <div class="flex justify-between text-emerald-700 font-semibold">
                    <span>Admissions Done:</span>
                    <span>{{ $totalAdmitted }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Full Paid (Lump-Sum):</span>
                    <span class="font-bold text-emerald-600">{{ $lumpSumCount }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>On Installments:</span>
                    <span class="font-bold text-purple-700">{{ $installmentCount }}</span>
                </div>
                <div class="flex justify-between border-t border-slate-200 pt-1 font-bold text-slate-900">
                    <span>Fee Recovered:</span>
                    <span class="text-emerald-700">PKR {{ number_format($totalFeeCollected) }}</span>
                </div>
                <div class="flex justify-between text-amber-700 font-bold">
                    <span>Remaining Due:</span>
                    <span>PKR {{ number_format($totalOutstandingDue) }}</span>
                </div>
            </div>
        </div>

        <!-- Filter Subtitle if applied -->
        @if(count(array_filter($filters ?? [])) > 0)
            <div class="mb-3 px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-[11px] text-slate-600 flex items-center gap-3">
                <span class="font-bold text-slate-700">Applied Filter:</span>
                @if(!empty($filters['date_from']) || !empty($filters['date_to']))
                    <span>Dates: {{ $filters['date_from'] ?: 'Start' }} &rarr; {{ $filters['date_to'] ?: 'Present' }}</span>
                @endif
                @if(!empty($filters['payment_type']))
                    <span>• Mode: {{ ucfirst(str_replace('_', ' ', $filters['payment_type'])) }}</span>
                @endif
            </div>
        @endif

        <!-- Data Table -->
        <table class="w-full text-left text-xs border border-slate-300 divide-y divide-slate-200">
            <thead class="bg-slate-100 font-bold text-slate-800 uppercase tracking-wider text-[10px]">
                <tr>
                    <th class="p-2 border-r border-slate-300 w-10 text-center">#</th>
                    <th class="p-2 border-r border-slate-300">Student & Contact</th>
                    <th class="p-2 border-r border-slate-300">Area / Locality</th>
                    <th class="p-2 border-r border-slate-300">Campus</th>
                    <th class="p-2 border-r border-slate-300">Course / Program</th>
                    <th class="p-2 border-r border-slate-300 text-center">Status</th>
                    <th class="p-2 border-r border-slate-300 text-center">Payment Mode</th>
                    <th class="p-2 border-r border-slate-300 text-right">Fee Package</th>
                    <th class="p-2 border-r border-slate-300 text-right">Received</th>
                    <th class="p-2 border-r border-slate-300 text-right">Due Balance</th>
                    <th class="p-2 text-right">Date & Time</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($students as $index => $s)
                    @php
                        $adm = $s->admission;
                    @endphp
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-slate-50/50' }}">
                        <td class="p-2 font-mono text-[10px] text-slate-500 border-r border-slate-200 text-center">
                            {{ $s->id }}
                        </td>
                        <td class="p-2 border-r border-slate-200">
                            <span class="font-bold text-slate-900 block">{{ $s->student_name }}</span>
                            <span class="text-[10px] text-slate-500 font-mono">{{ $s->phone_number }}</span>
                            @if($s->parent_name)
                                <span class="text-[10px] text-slate-400 block">S/O: {{ $s->parent_name }}</span>
                            @endif
                        </td>
                        <td class="p-2 border-r border-slate-200 font-medium">
                            {{ $s->city ?: 'N/A' }}
                        </td>
                        <td class="p-2 border-r border-slate-200 font-medium text-[11px]">
                            {{ $s->branch->code ?? ($s->branch->name ?? 'Main') }}
                        </td>
                        <td class="p-2 font-bold border-r border-slate-200 text-[11px]">
                            {{ $s->course?->name ?? $s->class_applying_for }}
                        </td>
                        <td class="p-2 border-r border-slate-200 text-center">
                            @if($adm)
                                <span class="font-bold text-emerald-800 text-[10px]">Admitted</span>
                            @else
                                <span class="text-slate-600 text-[10px]">{{ ucfirst($s->status) }}</span>
                            @endif
                        </td>
                        <td class="p-2 border-r border-slate-200 text-center">
                            @if($adm)
                                @if($adm->payment_type === 'lump_sum')
                                    <span class="text-emerald-700 font-bold text-[10px]">Lump Sum (Full)</span>
                                @else
                                    <span class="text-purple-700 font-bold text-[10px]">Installments</span>
                                @endif
                            @else
                                <span class="text-slate-400 text-[10px]">Inquiry Only</span>
                            @endif
                        </td>
                        <td class="p-2 border-r border-slate-200 text-right font-medium">
                            {{ $adm ? number_format($adm->net_payable) : '—' }}
                        </td>
                        <td class="p-2 border-r border-slate-200 text-right font-bold text-emerald-700">
                            {{ $adm ? number_format($adm->total_received) : '—' }}
                        </td>
                        <td class="p-2 border-r border-slate-200 text-right font-bold text-amber-700">
                            {{ $adm ? ($adm->remaining > 0 ? number_format($adm->remaining) : '0') : '—' }}
                        </td>
                        <td class="p-2 text-right whitespace-nowrap text-[10px]">
                            <span class="font-semibold text-slate-800 block">{{ $s->created_at->format('d/m/Y') }}</span>
                            <span class="text-slate-500 font-mono">{{ $s->created_at->format('h:i A') }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="p-8 text-center text-slate-400">
                            No inquiry records found for this counselor under chosen parameters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-slate-100 font-bold text-slate-900 border-t-2 border-slate-300">
                <tr>
                    <td colspan="7" class="p-2 text-right uppercase tracking-wider text-[10px]">Cumulative Total:</td>
                    <td class="p-2 text-right">{{ number_format($totalNetPayable) }}</td>
                    <td class="p-2 text-right text-emerald-800">{{ number_format($totalFeeCollected) }}</td>
                    <td class="p-2 text-right text-amber-800">{{ number_format($totalOutstandingDue) }}</td>
                    <td class="p-2"></td>
                </tr>
            </tfoot>
        </table>

        <!-- Signature Endorsement Block -->
        <div class="mt-14 pt-8 border-t border-slate-200 grid grid-cols-3 gap-8 text-center text-xs">
            <div>
                <div class="border-b border-slate-400 w-48 mx-auto mb-2"></div>
                <p class="font-bold text-slate-900">{{ $user->name }}</p>
                <p class="text-slate-500 text-[11px]">Counselor Signature</p>
            </div>
            <div>
                <div class="border-b border-slate-400 w-48 mx-auto mb-2"></div>
                <p class="font-bold text-slate-900">Branch Accountant / Cashier</p>
                <p class="text-slate-500 text-[11px]">Audit Verification</p>
            </div>
            <div>
                <div class="border-b border-slate-400 w-48 mx-auto mb-2"></div>
                <p class="font-bold text-slate-900">Campus Director / Principal</p>
                <p class="text-slate-500 text-[11px]">Official Stamp & Approval</p>
            </div>
        </div>

    </div>

</body>
</html>

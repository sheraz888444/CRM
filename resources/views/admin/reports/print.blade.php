<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduTrack Admissions Report - {{ date('Y-m-d') }}</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])

    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; font-size: 10pt; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
        }
    </style>
</head>
<body class="bg-slate-100 p-6 sm:p-8 text-slate-800 font-sans">

    <!-- Top floating print button for screen view -->
    <div class="no-print max-w-7xl mx-auto mb-6 flex items-center justify-between bg-white p-4 rounded-2xl shadow-md border border-slate-200">
        <div>
            <h2 class="text-sm font-bold text-slate-900">Institutional Admissions & Inquiries Report Preview</h2>
            <p class="text-xs text-slate-500">Live dataset with staff attribution, lump-sum vs installment plan breakdown, area, and exact timestamps</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-5 py-2.5 rounded-xl font-bold text-xs text-white bg-blue-600 hover:bg-blue-700 shadow-md">
                🖨 Print / Save as PDF
            </button>
            <button onclick="window.close()" class="px-4 py-2.5 rounded-xl font-semibold text-xs text-slate-600 bg-slate-100 hover:bg-slate-200">
                Close Window
            </button>
        </div>
    </div>

    <!-- Printable Paper Sheet -->
    <div class="max-w-7xl mx-auto bg-white p-6 sm:p-10 rounded-2xl shadow-lg border border-slate-200 print:shadow-none print:border-none print:p-0">
        
        <!-- School Header -->
        <div class="border-b-2 border-slate-900 pb-5 mb-5 flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $schoolName ?? \App\Models\Setting::get('school_name', config('app.name', 'EduTrack CRM Institute')) }}</h1>
                <p class="text-sm font-semibold text-blue-600 mt-0.5">EduTrack CRM • Student Inquiry & Admission Financial Audit Report</p>
                <p class="text-xs text-slate-500 mt-1">Generated: {{ date('F j, Y • g:i A') }}</p>
            </div>
            @php
                $lumpCount = $students->filter(fn($s) => $s->admission && $s->admission->payment_type === 'lump_sum')->count();
                $instCount = $students->filter(fn($s) => $s->admission && $s->admission->payment_type === 'installment')->count();
                $totalRec = $students->sum(fn($s) => $s->admission ? (float)$s->admission->total_received : 0);
            @endphp
            <div class="text-right text-xs text-slate-600 space-y-0.5">
                <p class="font-bold text-slate-900 text-sm">Total Records: {{ $students->count() }}</p>
                <p>Full Paid (Lump-Sum): <strong class="text-emerald-700 font-bold">{{ $lumpCount }}</strong></p>
                <p>On Installments: <strong class="text-purple-700 font-bold">{{ $instCount }}</strong></p>
                <p>Fee Collected: <strong class="text-emerald-700 font-bold">PKR {{ number_format($totalRec) }}</strong></p>
            </div>
        </div>

        <!-- Data Table -->
        <table class="w-full text-left text-xs border border-slate-200 divide-y divide-slate-200">
            <thead class="bg-slate-100 font-bold text-slate-800 uppercase tracking-wider text-[10px]">
                <tr>
                    <th class="p-2 border-r border-slate-200">#</th>
                    <th class="p-2 border-r border-slate-200">Student & Contact</th>
                    <th class="p-2 border-r border-slate-200">Area / Locality</th>
                    <th class="p-2 border-r border-slate-200">Campus</th>
                    <th class="p-2 border-r border-slate-200">Course / Class</th>
                    <th class="p-2 border-r border-slate-200">Counselor</th>
                    <th class="p-2 border-r border-slate-200">Payment Mode</th>
                    <th class="p-2 border-r border-slate-200">Paid / Due</th>
                    <th class="p-2">Date & Exact Time</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($students as $index => $s)
                    @php
                        $adm = $s->admission;
                    @endphp
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-slate-50/50' }}">
                        <td class="p-2 font-mono text-[10px] text-slate-500 border-r border-slate-200">{{ $s->id }}</td>
                        <td class="p-2 border-r border-slate-200">
                            <span class="font-bold text-slate-900 block">{{ $s->student_name }}</span>
                            <span class="text-[11px] text-slate-500 font-mono">{{ $s->phone_number }}</span>
                        </td>
                        <td class="p-2 font-medium border-r border-slate-200">
                            {{ $s->city ?: 'N/A' }}
                        </td>
                        <td class="p-2 border-r border-slate-200 font-medium">
                            {{ $s->branch->code ?? ($s->branch->name ?? 'Main') }}
                        </td>
                        <td class="p-2 font-semibold border-r border-slate-200">
                            {{ $s->course?->name ?? $s->class_applying_for }}
                        </td>
                        <td class="p-2 border-r border-slate-200 font-medium">
                            {{ $s->creator ? $s->creator->name : 'Staff' }}
                        </td>
                        <td class="p-2 border-r border-slate-200">
                            @if($adm)
                                @if($adm->payment_type === 'lump_sum')
                                    <span class="font-bold text-emerald-700">✓ Full Paid (Lump-Sum)</span>
                                @else
                                    <span class="font-bold text-purple-700">🗓 Installments ({{ $adm->installments->count() }})</span>
                                @endif
                            @else
                                <span class="text-slate-400 italic">Inquiry ({{ $s->status }})</span>
                            @endif
                        </td>
                        <td class="p-2 border-r border-slate-200 font-mono text-[11px]">
                            @if($adm)
                                <span class="font-bold text-emerald-700">PKR {{ number_format($adm->total_received) }}</span>
                                @if($adm->remaining > 0)
                                    <span class="block text-amber-700 text-[10px]">Due: {{ number_format($adm->remaining) }}</span>
                                @endif
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="p-2 font-mono text-[11px] whitespace-nowrap">
                            <span class="font-bold text-slate-900 block">{{ $s->created_at->format('Y-m-d') }}</span>
                            <span class="text-slate-500 text-[10px]">{{ $s->created_at->format('h:i:s A') }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Sign-off Footer -->
        <div class="mt-10 pt-6 border-t border-slate-200 flex justify-between text-xs text-slate-500">
            <div>
                <p class="font-bold text-slate-700">Confidential Institutional Document</p>
                <p>EduTrack CRM Management Suite</p>
            </div>
            <div class="text-right">
                <div class="w-48 border-b border-slate-400 mb-1"></div>
                <p>Authorized Administrator Signature & Stamp</p>
            </div>
        </div>
    </div>
</body>
</html>

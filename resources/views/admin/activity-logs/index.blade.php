@extends('layouts.app')

@section('title', 'System Audit Trail - EduTrack CRM')
@section('page_title', 'Activity & Audit Trail Logs')
@section('page_subtitle', 'Institutional record of system actions, logins, inquiry additions, and status changes')

@section('content')
<div class="space-y-6">

    <!-- Filter Header -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6">
        <form method="GET" action="{{ route('admin.activityLogs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
            <!-- User -->
            <div>
                <label class="block font-bold text-slate-600 uppercase mb-1">User Officer</label>
                <select name="user_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                    <option value="">All Users</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Action -->
            <div>
                <label class="block font-bold text-slate-600 uppercase mb-1">Action Type</label>
                <select name="action" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                    <option value="">All Actions</option>
                    @foreach($actions as $act)
                        <option value="{{ $act }}" {{ request('action') == $act ? 'selected' : '' }}>{{ $act }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Date -->
            <div>
                <label class="block font-bold text-slate-600 uppercase mb-1">Date</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
            </div>

            <!-- Actions -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-xl font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm">
                    Filter Logs
                </button>
                @if(request()->hasAny(['user_id', 'action', 'date_from']))
                    <a href="{{ route('admin.activityLogs.index') }}" class="py-2 px-3 text-slate-500 hover:bg-slate-100 rounded-xl font-semibold">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5">Timestamp</th>
                        <th class="px-4 py-3.5">User</th>
                        <th class="px-4 py-3.5">Action</th>
                        <th class="px-4 py-3.5">Description</th>
                        <th class="px-6 py-3.5 text-right">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($activityLogs as $log)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-xs font-mono text-slate-500">
                                {{ $log->created_at->format('M d, Y h:i:s A') }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="font-bold text-slate-900 text-xs">
                                    {{ $log->user ? $log->user->name : 'System Automated' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-slate-100 text-slate-700">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-xs text-slate-700 font-medium">
                                {{ $log->description }}
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap font-mono text-xs text-slate-400">
                                {{ $log->ip_address ?: '127.0.0.1' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                No activity audit records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($activityLogs->hasPages())
            <div class="p-6 border-t border-slate-100">
                {{ $activityLogs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8" id="statsContainer">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200/60 p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-medium text-slate-500">Total Laporan</p>
            <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center">
                <svg class="w-4.5 h-4.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-slate-900" id="totalReports">-</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200/60 p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-medium text-slate-500">Kasus Aktif</p>
            <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center">
                <svg class="w-4.5 h-4.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-slate-900" id="activeCases">-</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200/60 p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-medium text-slate-500">Darurat</p>
            <div class="w-9 h-9 rounded-lg bg-red-50 flex items-center justify-center">
                <svg class="w-4.5 h-4.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-slate-900" id="emergencyCases">-</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200/60 p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-medium text-slate-500">Pelapor Terverifikasi</p>
            <div class="w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center">
                <svg class="w-4.5 h-4.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-slate-900" id="verifiedReporters">-</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200/60">
    <div class="px-5 py-4 border-b border-slate-100">
        <h3 class="text-base font-semibold text-slate-900">Laporan Terbaru</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <th class="px-5 py-3.5">Tracking ID</th>
                    <th class="px-5 py-3.5">Kategori</th>
                    <th class="px-5 py-3.5">Severity</th>
                    <th class="px-5 py-3.5">Status</th>
                    <th class="px-5 py-3.5">Tanggal</th>
                </tr>
            </thead>
            <tbody id="recentReports" class="divide-y divide-slate-100"></tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    async function loadStats() {
        const token = localStorage.getItem('admin_token');
        if (!token) { window.location.href = '/admin/login'; return; }

        try {
            const res = await fetch('/api/admin/dashboard/stats', {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (!data.success) throw new Error(data.message);

            document.getElementById('totalReports').textContent = data.data.total_reports;
            document.getElementById('activeCases').textContent = data.data.active_cases;
            document.getElementById('emergencyCases').textContent = data.data.emergency_cases;
            document.getElementById('verifiedReporters').textContent = data.data.verified_reporters;

            const tbody = document.getElementById('recentReports');
            tbody.innerHTML = data.data.recent_reports.map(r => `
                <tr class="hover:bg-slate-50 transition-colors duration-150">
                    <td class="px-5 py-3.5"><a href="/admin/reports/${r.tracking_id}" class="font-mono font-medium text-indigo-600 hover:text-indigo-700">${r.tracking_id}</a></td>
                    <td class="px-5 py-3.5 text-slate-600">${r.category || '-'}</td>
                    <td class="px-5 py-3.5"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ${severityClass(r.severity)}">${r.severity}</span></td>
                    <td class="px-5 py-3.5"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ${statusClass(r.status)}">${r.status}</span></td>
                    <td class="px-5 py-3.5 text-slate-500">${new Date(r.submitted_at).toLocaleDateString('id-ID')}</td>
                </tr>
            `).join('');
        } catch (err) {
            console.error(err);
        }
    }

    function severityClass(s) {
        return { 'Emergency': 'bg-red-50 text-red-700', 'High': 'bg-orange-50 text-orange-700', 'Medium': 'bg-amber-50 text-amber-700', 'Low': 'bg-emerald-50 text-emerald-700' }[s] || 'bg-slate-100 text-slate-700';
    }

    function statusClass(s) {
        return { 'Submitted': 'bg-blue-50 text-blue-700', 'Under Review': 'bg-violet-50 text-violet-700', 'Investigation': 'bg-amber-50 text-amber-700', 'Action Taken': 'bg-teal-50 text-teal-700', 'Resolved': 'bg-emerald-50 text-emerald-700', 'Closed': 'bg-slate-100 text-slate-600', 'Rejected': 'bg-red-50 text-red-700' }[s] || 'bg-slate-100 text-slate-600';
    }

    loadStats();
</script>
@endpush

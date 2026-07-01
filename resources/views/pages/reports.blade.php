@extends('layouts.admin')

@section('title', 'Laporan')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-slate-200/60 p-5 mb-6">
    <form id="filterForm" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
        <div class="relative">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" placeholder="Cari Tracking ID..." class="w-full h-10 pl-9 pr-3.5 text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500 placeholder:text-slate-400 transition-all duration-150">
        </div>
        <select name="status" class="w-full h-10 px-3.5 text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500 appearance-none transition-all duration-150">
            <option value="">Semua Status</option>
            <option value="Submitted">Submitted</option>
            <option value="Under Review">Under Review</option>
            <option value="Investigation">Investigation</option>
            <option value="Action Taken">Action Taken</option>
            <option value="Resolved">Resolved</option>
            <option value="Closed">Closed</option>
            <option value="Rejected">Rejected</option>
        </select>
        <select name="severity" class="w-full h-10 px-3.5 text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500 appearance-none transition-all duration-150">
            <option value="">Semua Severity</option>
            <option value="Low">Low</option>
            <option value="Medium">Medium</option>
            <option value="High">High</option>
            <option value="Emergency">Emergency</option>
        </select>
        <button type="submit" class="h-10 bg-indigo-600 text-white px-4 rounded-xl hover:bg-indigo-700 active:bg-indigo-800 transition-all duration-150 text-sm font-semibold shadow-sm shadow-indigo-500/10">Filter</button>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200/60">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <th class="px-5 py-3.5">Tracking ID</th>
                    <th class="px-5 py-3.5">Kategori</th>
                    <th class="px-5 py-3.5">Severity</th>
                    <th class="px-5 py-3.5">Status</th>
                    <th class="px-5 py-3.5">Tanggal</th>
                    <th class="px-5 py-3.5">Aksi</th>
                </tr>
            </thead>
            <tbody id="reportsTable" class="divide-y divide-slate-100"></tbody>
        </table>
    </div>
    <div id="pagination" class="px-5 py-4 border-t border-slate-100 flex justify-between items-center"></div>
</div>
@endsection

@push('scripts')
<script>
    let currentPage = 1;

    async function loadReports(page = 1) {
        const token = localStorage.getItem('admin_token');
        if (!token) { window.location.href = '/admin/login'; return; }

        const params = new URLSearchParams({ page });
        const form = document.getElementById('filterForm');
        ['search', 'status', 'severity'].forEach(f => {
            if (form[f]?.value) params.set(f, form[f].value);
        });

        try {
            const res = await fetch(`/api/admin/reports?${params}`, {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (!data.success) throw new Error(data.message);

            const tbody = document.getElementById('reportsTable');
            if (data.data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="px-5 py-12 text-center text-slate-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <p class="text-sm font-medium">Tidak ada laporan</p>
                    <p class="text-xs mt-1">Belum ada data laporan yang tersedia.</p>
                </td></tr>`;
            } else {
                tbody.innerHTML = data.data.map(r => `
                    <tr class="hover:bg-slate-50 transition-colors duration-150">
                        <td class="px-5 py-3.5 font-mono font-medium text-slate-800">${r.tracking_id}</td>
                        <td class="px-5 py-3.5 text-slate-600">${r.category || '-'}</td>
                        <td class="px-5 py-3.5"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ${severityClass(r.severity)}">${r.severity}</span></td>
                        <td class="px-5 py-3.5"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ${statusClass(r.status)}">${r.status}</span></td>
                        <td class="px-5 py-3.5 text-slate-500">${new Date(r.submitted_at).toLocaleDateString('id-ID')}</td>
                        <td class="px-5 py-3.5"><a href="/admin/reports/${r.tracking_id}" class="text-indigo-600 hover:text-indigo-700 font-medium text-sm transition-colors">Detail</a></td>
                    </tr>
                `).join('');
            }

            const pagination = document.getElementById('pagination');
            pagination.innerHTML = `
                <span class="text-sm text-slate-500">Halaman ${data.meta.current_page} dari ${data.meta.last_page}</span>
                <div class="flex gap-2">
                    ${data.meta.current_page > 1 ? `<button onclick="loadReports(${data.meta.current_page - 1})" class="px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:border-slate-300 transition-all duration-150">Sebelumnya</button>` : ''}
                    ${data.meta.current_page < data.meta.last_page ? `<button onclick="loadReports(${data.meta.current_page + 1})" class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 active:bg-indigo-800 transition-all duration-150 shadow-sm shadow-indigo-500/10">Selanjutnya</button>` : ''}
                </div>
            `;
        } catch (err) { console.error(err); }
    }

    document.getElementById('filterForm').addEventListener('submit', (e) => { e.preventDefault(); loadReports(1); });

    function severityClass(s) {
        return { 'Emergency': 'bg-red-50 text-red-700', 'High': 'bg-orange-50 text-orange-700', 'Medium': 'bg-amber-50 text-amber-700', 'Low': 'bg-emerald-50 text-emerald-700' }[s] || 'bg-slate-100 text-slate-700';
    }

    function statusClass(s) {
        return { 'Submitted': 'bg-blue-50 text-blue-700', 'Under Review': 'bg-violet-50 text-violet-700', 'Investigation': 'bg-amber-50 text-amber-700', 'Action Taken': 'bg-teal-50 text-teal-700', 'Resolved': 'bg-emerald-50 text-emerald-700', 'Closed': 'bg-slate-100 text-slate-600', 'Rejected': 'bg-red-50 text-red-700' }[s] || 'bg-slate-100 text-slate-600';
    }

    loadReports();
</script>
@endpush

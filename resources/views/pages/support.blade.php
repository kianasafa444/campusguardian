@extends('layouts.admin')

@section('title', 'Dukungan')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200/60">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-semibold text-slate-900">Permohonan Dukungan</h3>
                <select id="statusFilter" class="h-9 px-3 text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500 appearance-none transition-all duration-150" onchange="loadSupport()">
                    <option value="">Semua Status</option>
                    <option value="Pending">Pending</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <th class="px-5 py-3.5">ID</th>
                            <th class="px-5 py-3.5">Tracking ID</th>
                            <th class="px-5 py-3.5">Jenis</th>
                            <th class="px-5 py-3.5">Status</th>
                            <th class="px-5 py-3.5">Tanggal</th>
                            <th class="px-5 py-3.5">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="supportTable" class="divide-y divide-slate-100"></tbody>
                </table>
            </div>
            <div id="pagination" class="px-5 py-4 border-t border-slate-100 flex justify-between items-center"></div>
        </div>
    </div>

    <div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200/60" id="detailPanel">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="text-base font-semibold text-slate-900">Detail Permohonan</h3>
            </div>
            <div class="p-5">
                <p class="text-sm text-slate-400 text-center py-6">Klik baris permohonan untuk melihat detail.</p>
                <div id="supportDetail" class="hidden">
                    <div class="space-y-4">
                        <div class="bg-slate-50 rounded-xl p-4 space-y-3 text-sm">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500">Tracking ID</span>
                                <span id="detTrackingId" class="font-medium font-mono text-slate-800"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500">Jenis</span>
                                <span id="detType" class="font-medium text-slate-800"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500">Status</span>
                                <span id="detStatus" class="font-medium"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500">Diajukan</span>
                                <span id="detDate" class="font-medium text-slate-800"></span>
                            </div>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Deskripsi</span>
                            <p id="detDescription" class="mt-1.5 text-sm text-slate-700 leading-relaxed"></p>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Catatan Admin</span>
                            <p id="detNotes" class="mt-1.5 text-sm text-slate-500 italic bg-slate-50 rounded-lg px-3.5 py-2.5 border border-slate-100">-</p>
                        </div>
                    </div>

                    <hr class="my-5 border-slate-200">

                    <h4 class="font-semibold text-sm text-slate-800 mb-3">Update Status</h4>
                    <form id="supportStatusForm" class="space-y-3">
                        <input type="hidden" id="editSupportId">
                        <select name="status" class="w-full h-10 px-3.5 text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500 appearance-none transition-all duration-150">
                            <option value="Pending">Pending</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                        <textarea name="admin_notes" placeholder="Catatan admin (opsional)" class="w-full px-3.5 py-3 text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500 placeholder:text-slate-400 resize-y min-h-[80px] transition-all duration-150" rows="3"></textarea>
                        <button type="submit" class="w-full h-10 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 active:bg-indigo-800 transition-all duration-150 text-sm shadow-sm shadow-indigo-500/10">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    async function loadSupport() {
        const token = localStorage.getItem('admin_token');
        if (!token) { window.location.href = '/admin/login'; return; }

        const params = new URLSearchParams();
        const status = document.getElementById('statusFilter').value;
        if (status) params.set('status', status);

        try {
            const res = await fetch(`/api/admin/support-requests?${params}`, {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (!data.success) return;

            const tbody = document.getElementById('supportTable');
            if (data.data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="px-5 py-12 text-center text-slate-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <p class="text-sm font-medium">Tidak ada permohonan</p>
                    <p class="text-xs mt-1">Belum ada permohonan dukungan yang masuk.</p>
                </td></tr>`;
            } else {
                tbody.innerHTML = data.data.map(r => `
                    <tr class="hover:bg-slate-50 transition-colors duration-150 cursor-pointer" onclick="loadSupportDetail(${r.id})">
                        <td class="px-5 py-3.5 font-medium text-slate-800">${r.id}</td>
                        <td class="px-5 py-3.5 font-mono font-medium text-slate-800">${r.tracking_id || '-'}</td>
                        <td class="px-5 py-3.5 text-slate-600">${r.support_type || '-'}</td>
                        <td class="px-5 py-3.5"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ${supportStatusClass(r.status)}">${r.status}</span></td>
                        <td class="px-5 py-3.5 text-slate-500">${new Date(r.created_at).toLocaleDateString('id-ID')}</td>
                        <td class="px-5 py-3.5"><button onclick="event.stopPropagation();loadSupportDetail(${r.id})" class="text-indigo-600 hover:text-indigo-700 font-medium text-sm transition-colors">Detail</button></td>
                    </tr>
                `).join('');
            }
        } catch (err) { console.error(err); }
    }

    function supportStatusClass(s) {
        const colors = {
            'Pending': 'bg-amber-50 text-amber-700',
            'In Progress': 'bg-blue-50 text-blue-700',
            'Completed': 'bg-emerald-50 text-emerald-700',
            'Cancelled': 'bg-red-50 text-red-700'
        };
        return colors[s] || 'bg-slate-100 text-slate-600';
    }

    async function loadSupportDetail(id) {
        const token = localStorage.getItem('admin_token');
        if (!token) return;

        try {
            const res = await fetch(`/api/admin/support-requests/${id}`, {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (!data.success) return;

            const d = data.data;
            document.getElementById('supportDetail').classList.remove('hidden');
            document.getElementById('detTrackingId').textContent = d.tracking_id || '-';
            document.getElementById('detType').textContent = d.support_type || '-';
            document.getElementById('detStatus').textContent = d.status;
            document.getElementById('detDescription').textContent = d.description || '-';
            document.getElementById('detDate').textContent = new Date(d.created_at).toLocaleString('id-ID');
            document.getElementById('detNotes').textContent = d.admin_notes || '-';
            document.getElementById('editSupportId').value = d.id;
            document.getElementById('supportStatusForm').querySelector('[name="status"]').value = d.status;
            document.getElementById('supportStatusForm').querySelector('[name="admin_notes"]').value = d.admin_notes || '';
        } catch (err) { console.error(err); }
    }

    document.getElementById('supportStatusForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const token = localStorage.getItem('admin_token');
        if (!token) return;

        const id = document.getElementById('editSupportId').value;
        if (!id) return;

        const form = e.target;
        const btn = form.querySelector('button');
        btn.disabled = true;
        btn.textContent = 'Menyimpan...';

        try {
            const res = await fetch(`/api/admin/support-requests/${id}/status`, {
                method: 'PUT',
                headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ status: form.status.value, admin_notes: form.admin_notes.value })
            });
            const data = await res.json();
            if (data.success) {
                loadSupportDetail(id);
                loadSupport();
            }
            btn.textContent = 'Simpan';
        } catch (err) { console.error(err); }
        btn.disabled = false;
    });

    loadSupport();
</script>
@endpush

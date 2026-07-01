@extends('layouts.admin')

@section('title', 'Timeline Laporan')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-slate-200/60">
    <div class="px-5 py-4 border-b border-slate-100">
        <a href="/admin/reports/{{ request()->route('trackingId') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium transition-colors">&larr; Kembali ke Detail Laporan</a>
        <h3 class="text-base font-semibold text-slate-900 mt-2">Timeline Lengkap</h3>
        <p class="text-sm text-slate-500 mt-0.5">Tracking ID: <span class="font-mono font-semibold text-indigo-600" id="timelineTrackingId"></span></p>
    </div>
    <div class="p-5" id="timelineFullContainer">
        <div class="flex items-center gap-3 text-slate-400">
            <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            <span class="text-sm">Memuat...</span>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const trackingId = window.location.pathname.split('/').filter(Boolean);
    const currentTrackingId = trackingId[trackingId.length - 2];

    document.getElementById('timelineTrackingId').textContent = currentTrackingId;

    async function loadTimeline() {
        const token = localStorage.getItem('admin_token');
        if (!token) { window.location.href = '/admin/login'; return; }

        try {
            const res = await fetch(`/api/admin/reports/${currentTrackingId}/timeline`, {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (!data.success) throw new Error(data.message);

            const container = document.getElementById('timelineFullContainer');
            if (data.data.length > 0) {
                container.innerHTML = `<div class="space-y-0 max-w-2xl">` + data.data.map((t, i) => `
                    <div class="relative pl-8 pb-6 ${i === data.data.length - 1 ? '' : ''}">
                        <div class="absolute left-[11px] top-1.5 w-[13px] h-[13px] rounded-full bg-white border-[3px] border-indigo-500 shadow-sm z-10"></div>
                        ${i < data.data.length - 1 ? `<div class="absolute left-[16.5px] top-[26px] bottom-0 w-0.5 bg-slate-200"></div>` : ''}
                        <div>
                            <p class="text-xs font-medium text-slate-400">${new Date(t.created_at).toLocaleString('id-ID')}</p>
                            <p class="text-sm font-medium text-slate-800 mt-0.5">${t.previous_status || '—'} <span class="text-slate-300 mx-1">&rarr;</span> <span class="text-indigo-600">${t.new_status}</span></p>
                            ${t.note ? `<p class="text-sm text-slate-500 mt-1.5 bg-slate-50 rounded-lg px-3 py-2 border border-slate-100 italic">"${t.note}"</p>` : ''}
                            ${t.action_by ? `<p class="text-xs text-slate-400 mt-1">oleh ${t.action_by}</p>` : ''}
                        </div>
                    </div>
                `).join('') + `</div>`;
            } else {
                container.innerHTML = `<div class="text-center py-10">
                    <svg class="w-12 h-12 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm font-medium text-slate-400">Belum ada timeline</p>
                    <p class="text-xs text-slate-400 mt-1">Belum ada aktivitas pada laporan ini.</p>
                </div>`;
            }
        } catch (err) {
            document.getElementById('timelineFullContainer').innerHTML = `<div class="flex items-center gap-2.5 text-sm text-red-600 bg-red-50 border border-red-100 rounded-xl px-4 py-3"><svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span>Gagal memuat timeline: ${err.message}</span></div>`;
        }
    }

    loadTimeline();
</script>
@endpush

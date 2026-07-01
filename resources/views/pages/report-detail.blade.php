@extends('layouts.admin')

@section('title', 'Detail Laporan')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" id="reportDetail">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200/60">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h3 class="text-base font-semibold text-slate-900">Informasi Laporan</h3>
                </div>
                <div class="p-5" id="reportInfo">
                    <div class="flex items-center gap-3 text-slate-400">
                        <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span class="text-sm">Memuat...</span>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-200/60">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h3 class="text-base font-semibold text-slate-900">Timeline</h3>
                </div>
                <div class="p-5" id="timelineContainer">
                    <div class="flex items-center gap-3 text-slate-400">
                        <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span class="text-sm">Memuat...</span>
                    </div>
                </div>
                <div class="px-5 py-3 border-t border-slate-100">
                    <a id="fullTimelineLink" href="#" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium transition-colors">Lihat semua timeline &rarr;</a>
                </div>
            </div>
        </div>
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200/60">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h3 class="text-base font-semibold text-slate-900">Update Status</h3>
                </div>
                <div class="p-5">
                    <form id="statusForm" class="space-y-3">
                        <select name="status" class="w-full h-10 px-3.5 text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500 appearance-none transition-all duration-150" id="statusSelect"></select>
                        <textarea name="note" placeholder="Catatan (opsional)" class="w-full px-3.5 py-3 text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500 placeholder:text-slate-400 resize-y min-h-[80px] transition-all duration-150" rows="3"></textarea>
                        <button type="submit" class="w-full h-10 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 active:bg-indigo-800 transition-all duration-150 text-sm shadow-sm shadow-indigo-500/10">Simpan</button>
                    </form>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-200/60">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h3 class="text-base font-semibold text-slate-900">Evidence</h3>
                </div>
                <div class="p-5" id="evidencesList">
                    <div class="flex items-center gap-3 text-slate-400">
                        <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span class="text-sm">Memuat...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const trackingId = window.location.pathname.split('/').pop();

        async function loadDetail() {
            const token = localStorage.getItem('admin_token');
            if (!token) {
                window.location.href = '/admin/login';
                return;
            }

            try {
                const detailRes = await fetch(`/api/admin/reports/${trackingId}`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
                const detail = await detailRes.json();
                if (!detail.success) {
                    throw new Error(detail.message);
                }

                const r = detail.data;
                document.getElementById('reportInfo').innerHTML = `
                <div class="grid grid-cols-2 gap-x-6 gap-y-4">
                    <div><span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Tracking ID</span><p class="mt-1 font-mono font-semibold text-indigo-600 tracking-wider text-sm">${r.tracking_id}</p></div>
                    <div><span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Kategori</span><p class="mt-1 font-medium text-slate-800 text-sm">${r.category || '-'}</p></div>
                    <div><span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Severity</span><p class="mt-1"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ${severityClass(r.severity)}">${r.severity}</span></p></div>
                    <div><span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Status</span><p class="mt-1"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ${statusClass(r.status)}">${r.status}</span></p></div>
                    <div><span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Lokasi</span><p class="mt-1 font-medium text-slate-800 text-sm">${r.location || '-'}</p></div>
                    <div><span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Tanggal Kejadian</span><p class="mt-1 font-medium text-slate-800 text-sm">${r.incident_date ? new Date(r.incident_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-'}</p></div>
                    <div class="col-span-2"><span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Deskripsi</span><p class="mt-1.5 text-sm text-slate-700 leading-relaxed whitespace-pre-wrap bg-slate-50 rounded-lg p-3.5 border border-slate-100">${r.description}</p></div>
                    <div><span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Diajukan</span><p class="mt-1 font-medium text-slate-800 text-sm">${new Date(r.submitted_at).toLocaleString('id-ID')}</p></div>
                    ${r.resolved_at ? `<div><span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Diselesaikan</span><p class="mt-1 font-medium text-slate-800 text-sm">${new Date(r.resolved_at).toLocaleString('id-ID')}</p></div>` : ''}
                </div>
            `;

                const sel = document.getElementById('statusSelect');
                const statuses = ['Submitted', 'Under Review', 'Investigation', 'Action Taken', 'Resolved', 'Closed',
                    'Rejected'
                ];
                sel.innerHTML = statuses.map(s =>
                    `<option value="${s}" ${s === r.status ? 'selected' : ''}>${s}</option>`).join('');

                document.getElementById('evidencesList').innerHTML = r.evidences.length > 0 ?
                    r.evidences.map(e => {
                        const isImage = e.mime_type && e.mime_type.startsWith('image/');
                        const isAudio = e.mime_type && e.mime_type.startsWith('audio/');
                        if (isAudio) {
                            return `<div class="py-3.5 border-b border-slate-100 last:border-0">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-slate-800 truncate" title="${e.file_name}">${e.file_name}</p>
                                        <p class="text-xs text-slate-400">${(e.file_size/1024/1024).toFixed(1)} MB</p>
                                    </div>
                                </div>
                                <button onclick="downloadEvidence('${e.download_url}')" class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors flex-shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Download
                                </button>
                            </div>
                            <audio controls class="w-full h-10 rounded-lg" preload="metadata">
                                <source src="${e.stream_url}" type="${e.mime_type}">
                                Browser tidak mendukung pemutar audio.
                            </audio>
                        </div>`;
                        }
                        const fileExt = (e.file_name || '').split('.').pop().toLowerCase();
                        const isPdf = fileExt === 'pdf';
                        const isDoc = ['doc', 'docx'].includes(fileExt);
                        const isVideo = e.mime_type && e.mime_type.startsWith('video/');
                        let fileIconSvg = '';
                        if (isImage) {
                            fileIconSvg = `<img src="${e.stream_url || e.download_url}" alt="${e.file_name}" class="w-10 h-10 object-cover rounded-lg flex-shrink-0" onerror="this.style.display='none'">`;
                        } else if (isPdf) {
                            fileIconSvg = `<div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0"><svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg></div>`;
                        } else if (isVideo) {
                            fileIconSvg = `<div class="w-10 h-10 rounded-lg bg-accent-50 flex items-center justify-center flex-shrink-0"><svg class="w-5 h-5 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg></div>`;
                        } else {
                            fileIconSvg = `<div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0"><svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>`;
                        }
                        return `<div class="flex items-center justify-between py-3 border-b border-slate-100 last:border-0 gap-3">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            ${fileIconSvg}
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-slate-800 truncate" title="${e.file_name}">${e.file_name}</p>
                                <p class="text-xs text-slate-400">${(e.file_size/1024).toFixed(1)} KB</p>
                            </div>
                        </div>
                        <button onclick="downloadEvidence('${e.download_url}')" class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors flex-shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Download
                        </button>
                    </div>`;
                    }).join('') :
                    '<div class="text-center py-6"><svg class="w-10 h-10 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg><p class="text-sm text-slate-400">Tidak ada file bukti.</p></div>';

                document.getElementById('timelineContainer').innerHTML = r.recent_timeline.length > 0 ?
                    `<div class="space-y-0">` + r.recent_timeline.map((t, i) => `
                    <div class="relative pl-8 pb-5 ${i === r.recent_timeline.length - 1 ? '' : ''}">
                        <div class="absolute left-[11px] top-1.5 w-[13px] h-[13px] rounded-full bg-white border-[3px] border-indigo-500 shadow-sm z-10"></div>
                        ${i < r.recent_timeline.length - 1 ? `<div class="absolute left-[16.5px] top-[26px] bottom-0 w-0.5 bg-slate-200"></div>` : ''}
                        <div>
                            <p class="text-xs font-medium text-slate-400">${new Date(t.created_at).toLocaleString('id-ID')}</p>
                            <p class="text-sm font-medium text-slate-800 mt-0.5">${t.previous_status || '—'} <span class="text-slate-300 mx-1">&rarr;</span> <span class="text-indigo-600">${t.new_status}</span></p>
                            ${t.note ? `<p class="text-sm text-slate-500 mt-1.5 bg-slate-50 rounded-lg px-3 py-2 border border-slate-100 italic">"${t.note}"</p>` : ''}
                            ${t.action_by ? `<p class="text-xs text-slate-400 mt-1">oleh ${t.action_by}</p>` : ''}
                        </div>
                    </div>
                `).join('') + `</div>` :
                    '<div class="text-center py-6"><svg class="w-10 h-10 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><p class="text-sm text-slate-400">Belum ada timeline.</p></div>';

                document.getElementById('fullTimelineLink').href = `/admin/reports/${r.tracking_id}/timeline`;
            } catch (err) {
                document.getElementById('reportInfo').innerHTML =
                    `<div class="flex items-center gap-2.5 text-sm text-red-600 bg-red-50 border border-red-100 rounded-xl px-4 py-3"><svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span>Gagal memuat detail laporan: ${err.message}</span></div>`;
            }
        }

        document.getElementById('statusForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const token = localStorage.getItem('admin_token');
            if (!token) return;

            const form = e.target;
            const btn = form.querySelector('button');
            btn.disabled = true;
            btn.textContent = 'Menyimpan...';

            try {
                const res = await fetch(`/api/admin/reports/${trackingId}/status`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        status: form.status.value,
                        note: form.note.value
                    })
                });
                const data = await res.json();
                if (data.success) {
                    loadDetail();
                }
                btn.textContent = 'Simpan';
            } catch (err) {
                console.error(err);
            }
            btn.disabled = false;
        });

        function severityClass(s) {
            return {
                'Emergency': 'bg-red-50 text-red-700',
                'High': 'bg-orange-50 text-orange-700',
                'Medium': 'bg-amber-50 text-amber-700',
                'Low': 'bg-emerald-50 text-emerald-700'
            } [s] || 'bg-slate-100 text-slate-700';
        }

        function statusClass(s) {
            return {
                'Submitted': 'bg-blue-50 text-blue-700',
                'Under Review': 'bg-violet-50 text-violet-700',
                'Investigation': 'bg-amber-50 text-amber-700',
                'Action Taken': 'bg-teal-50 text-teal-700',
                'Resolved': 'bg-emerald-50 text-emerald-700',
                'Closed': 'bg-slate-100 text-slate-600',
                'Rejected': 'bg-red-50 text-red-700'
            } [s] || 'bg-slate-100 text-slate-600';
        }

        async function downloadEvidence(url) {
            const token = localStorage.getItem('admin_token');

            const res = await fetch(url, {
                headers: {
                    Authorization: `Bearer ${token}`,
                    Accept: '*/*'
                }
            });

            if (!res.ok) {
                alert('Gagal mengunduh file');
                return;
            }

            const blob = await res.blob();

            const disposition = res.headers.get('Content-Disposition');
            let filename = 'evidence';

            if (disposition && disposition.includes('filename=')) {
                filename = disposition
                    .split('filename=')[1]
                    .replace(/"/g, '');
            }

            const objectUrl = URL.createObjectURL(blob);

            const a = document.createElement('a');
            a.href = objectUrl;
            a.download = filename;
            document.body.appendChild(a);
            a.click();

            a.remove();
            URL.revokeObjectURL(objectUrl);
        }

        loadDetail();
    </script>
@endpush

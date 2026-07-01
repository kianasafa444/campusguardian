@extends('layouts.admin')

@section('title', 'Resource')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-slate-200/60">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
        <h3 class="text-base font-semibold text-slate-900">Daftar Resource</h3>
        <button onclick="showCreateForm()" class="inline-flex items-center gap-1.5 h-9 px-4 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 active:bg-indigo-800 transition-all duration-150 text-sm shadow-sm shadow-indigo-500/10">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Resource
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <th class="px-5 py-3.5">Judul</th>
                    <th class="px-5 py-3.5">Tipe</th>
                    <th class="px-5 py-3.5">Kategori</th>
                    <th class="px-5 py-3.5">Status</th>
                    <th class="px-5 py-3.5">Aksi</th>
                </tr>
            </thead>
            <tbody id="resourceTable" class="divide-y divide-slate-100"></tbody>
        </table>
    </div>
</div>

<div id="resourceModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[85vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-semibold text-slate-900" id="modalTitle">Tambah Resource</h3>
            <button type="button" onclick="hideModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="resourceForm" class="p-6 space-y-4">
            <input type="hidden" name="edit_id">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Kategori</label>
                <select name="resource_category_id" class="w-full h-10 px-3.5 text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500 appearance-none transition-all duration-150" required>
                    <option value="1">Anti Bullying</option>
                    <option value="2">Kontak Darurat</option>
                    <option value="3">FAQ</option>
                    <option value="4">Panduan</option>
                    <option value="5">Kesehatan Mental</option>
                    <option value="6">Hak & Perlindungan</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Judul</label>
                <input type="text" name="title" class="w-full h-10 px-3.5 text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500 placeholder:text-slate-400 transition-all duration-150" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Tipe</label>
                <select name="type" class="w-full h-10 px-3.5 text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500 appearance-none transition-all duration-150" required>
                    <option value="article">Article</option>
                    <option value="faq">FAQ</option>
                    <option value="contact">Contact</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Konten</label>
                <textarea name="content" rows="6" class="w-full px-3.5 py-3 text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500 placeholder:text-slate-400 resize-y min-h-[120px] transition-all duration-150" required></textarea>
            </div>
            <div>
                <label class="inline-flex items-center gap-2.5 cursor-pointer">
                    <div class="relative">
                        <input type="checkbox" name="is_published" value="1" class="peer sr-only">
                        <div class="w-9 h-5 bg-slate-200 rounded-full peer-checked:bg-indigo-600 transition-colors duration-200 after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:w-4 after:h-4 after:bg-white after:rounded-full after:shadow-sm after:transition-all after:duration-200 peer-checked:after:translate-x-4"></div>
                    </div>
                    <span class="text-sm text-slate-700 font-medium">Publikasikan</span>
                </label>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 h-10 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 active:bg-indigo-800 transition-all duration-150 text-sm shadow-sm shadow-indigo-500/10">Simpan</button>
                <button type="button" onclick="hideModal()" class="flex-1 h-10 bg-slate-100 text-slate-700 font-medium rounded-xl hover:bg-slate-200 active:bg-slate-300 transition-all duration-150 text-sm">Batal</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    async function loadResources() {
        const token = localStorage.getItem('admin_token');
        if (!token) { window.location.href = '/admin/login'; return; }

        try {
            const res = await fetch('/api/admin/resources', {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (!data.success) return;

            const tbody = document.getElementById('resourceTable');
            if (data.data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="px-5 py-12 text-center text-slate-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <p class="text-sm font-medium">Belum ada resource</p>
                    <p class="text-xs mt-1">Klik "Tambah Resource" untuk menambahkan.</p>
                </td></tr>`;
            } else {
                tbody.innerHTML = data.data.map(r => `
                    <tr class="hover:bg-slate-50 transition-colors duration-150">
                        <td class="px-5 py-3.5 font-medium text-slate-800">${r.title}</td>
                        <td class="px-5 py-3.5"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">${r.type}</span></td>
                        <td class="px-5 py-3.5 text-slate-600">${r.category || '-'}</td>
                        <td class="px-5 py-3.5">${r.is_published ? '<span class="inline-flex items-center gap-1 text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full text-xs font-semibold"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>Published</span>' : '<span class="inline-flex items-center gap-1 text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full text-xs font-semibold"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Draft</span>'}</td>
                        <td class="px-5 py-3.5">
                            <button onclick="editResource(${r.id})" class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-700 font-medium text-sm transition-colors mr-3">Edit</button>
                            <button onclick="deleteResource(${r.id})" class="inline-flex items-center gap-1 text-red-500 hover:text-red-600 font-medium text-sm transition-colors">Hapus</button>
                        </td>
                    </tr>
                `).join('');
            }
        } catch (err) { console.error(err); }
    }

    function showCreateForm() {
        document.getElementById('resourceForm').reset();
        document.getElementById('resourceForm').querySelector('[name="edit_id"]').value = '';
        document.getElementById('modalTitle').textContent = 'Tambah Resource';
        document.getElementById('resourceModal').classList.remove('hidden');
        document.getElementById('resourceModal').classList.add('flex');
    }

    function hideModal() {
        document.getElementById('resourceModal').classList.add('hidden');
        document.getElementById('resourceModal').classList.remove('flex');
    }

    async function editResource(id) {
        const token = localStorage.getItem('admin_token');
        if (!token) return;

        try {
            const res = await fetch(`/api/admin/resources/${id}`, {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (!data.success) return;

            const r = data.data;
            const form = document.getElementById('resourceForm');
            form.querySelector('[name="edit_id"]').value = r.id;
            form.querySelector('[name="resource_category_id"]').value = r.resource_category_id;
            form.querySelector('[name="title"]').value = r.title;
            form.querySelector('[name="type"]').value = r.type;
            form.querySelector('[name="content"]').value = r.content;
            form.querySelector('[name="is_published"]').checked = r.is_published;
            document.getElementById('modalTitle').textContent = 'Edit Resource';
            document.getElementById('resourceModal').classList.remove('hidden');
            document.getElementById('resourceModal').classList.add('flex');
        } catch (err) { console.error(err); }
    }

    async function deleteResource(id) {
        if (!confirm('Hapus resource ini?')) return;

        const token = localStorage.getItem('admin_token');
        if (!token) return;

        try {
            const res = await fetch(`/api/admin/resources/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (data.success) loadResources();
        } catch (err) { console.error(err); }
    }

    document.getElementById('resourceForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const token = localStorage.getItem('admin_token');
        if (!token) return;

        const form = e.target;
        const btn = form.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.textContent = 'Menyimpan...';

        const body = {
            resource_category_id: parseInt(form.resource_category_id.value),
            title: form.title.value,
            content: form.content.value,
            type: form.type.value,
            is_published: form.is_published.checked,
        };

        try {
            let res;
            if (form.edit_id.value) {
                res = await fetch(`/api/admin/resources/${form.edit_id.value}`, {
                    method: 'PUT', headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(body)
                });
            } else {
                res = await fetch('/api/admin/resources', {
                    method: 'POST', headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(body)
                });
            }
            const data = await res.json();
            if (data.success) {
                hideModal();
                form.reset();
                loadResources();
            }
        } catch (err) { console.error(err); }
        btn.disabled = false;
        btn.textContent = 'Simpan';
    });

    loadResources();
</script>
@endpush

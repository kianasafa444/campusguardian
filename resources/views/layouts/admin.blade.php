<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin CampusGuardian')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-slate-50 font-sans antialiased">
    <div class="flex h-screen">
        <aside class="w-64 bg-slate-900 text-white flex flex-col flex-shrink-0">
            <div class="px-6 py-5 border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-indigo-500 flex items-center justify-center text-sm font-bold">CG</div>
                    <div>
                        <h1 class="text-base font-bold tracking-tight">CampusGuardian</h1>
                        <p class="text-indigo-300 text-xs">Panel Admin</p>
                    </div>
                </div>
            </div>
            <nav class="flex-1 px-3 py-4 space-y-1" id="sidebarNav">
                <a href="/admin/dashboard" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-800/80 transition-all duration-150 sidebar-link">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>
                <a href="/admin/reports" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-800/80 transition-all duration-150 sidebar-link">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Laporan
                </a>
                <a href="/admin/support" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-800/80 transition-all duration-150 sidebar-link">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Dukungan
                </a>
                <a href="/admin/resources" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-800/80 transition-all duration-150 sidebar-link">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    Resource
                </a>
            </nav>
            <div class="p-3 border-t border-slate-800">
                <form method="POST" action="/admin/logout">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg text-sm font-medium text-slate-400 hover:text-red-400 hover:bg-red-500/10 transition-all duration-150">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Logout
                    </button>
                </form>
            </div>
        </aside>
        <main class="flex-1 overflow-y-auto bg-slate-50">
            <header class="bg-white border-b border-slate-200/60 px-6 py-3.5 sticky top-0 z-10">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-slate-800">@yield('title', 'Dashboard')</h2>
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <span class="text-sm text-slate-500 font-medium" id="admin-email"></span>
                    </div>
                </div>
            </header>
            <div class="p-6 lg:p-8">
                @yield('content')
            </div>
        </main>
    </div>
    <script>
        const token = localStorage.getItem('admin_token');
        if (token) {
            fetch('/api/admin/auth/me', {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            }).then(r => r.json()).then(d => {
                if (d.success) document.getElementById('admin-email').textContent = d.data.user.email;
            });
        }

        document.querySelectorAll('.sidebar-link').forEach(link => {
            if (link.href === window.location.href || window.location.href.startsWith(link.href + '/')) {
                link.classList.add('bg-slate-800/80', 'text-white');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>

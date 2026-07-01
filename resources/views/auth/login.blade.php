<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - CampusGuardian</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-950 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl shadow-slate-900/20 p-8 sm:p-10">
            <div class="text-center mb-8">
                <div class="w-14 h-14 rounded-2xl bg-indigo-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <h1 class="text-2xl font-bold text-slate-900">CampusGuardian</h1>
                <p class="text-slate-500 text-sm mt-1">Panel Admin</p>
            </div>
            <form id="loginForm" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                    <input type="email" name="email" required placeholder="admin@campusguardian.ac.id"
                        class="w-full h-10 px-3.5 text-sm text-slate-800 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500 placeholder:text-slate-400 transition-all duration-150">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                    <input type="password" name="password" required placeholder="Masukkan password"
                        class="w-full h-10 px-3.5 text-sm text-slate-800 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500 placeholder:text-slate-400 transition-all duration-150">
                </div>
                <div id="errorMessage" class="flex items-center gap-2 text-sm text-red-600 bg-red-50 border border-red-100 rounded-xl px-4 py-3 hidden"></div>
                <button type="submit"
                    class="w-full h-11 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 active:bg-indigo-800 transition-all duration-150 shadow-sm shadow-indigo-500/10 disabled:opacity-50 disabled:cursor-not-allowed">
                    Login
                </button>
            </form>
        </div>
        <p class="text-center text-slate-400 text-xs mt-6">&copy; 2026 CampusGuardian. All rights reserved.</p>
    </div>
    <script>
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const btn = form.querySelector('button');
            btn.disabled = true;
            btn.textContent = 'Loading...';
            const errorMsg = document.getElementById('errorMessage');
            errorMsg.classList.add('hidden');

            try {
                const res = await fetch('/api/admin/auth/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ email: form.email.value, password: form.password.value })
                });
                const data = await res.json();
                if (data.success) {
                    localStorage.setItem('admin_token', data.data.token);
                    window.location.href = '/admin/dashboard';
                } else {
                    errorMsg.textContent = data.message;
                    errorMsg.classList.remove('hidden');
                }
            } catch (err) {
                errorMsg.textContent = 'Terjadi kesalahan.';
                errorMsg.classList.remove('hidden');
            }
            btn.disabled = false;
            btn.textContent = 'Login';
        });
    </script>
</body>
</html>

import { Link } from 'react-router-dom';
import { useEffect, useState } from 'react';
import { getResourceCategories } from '../services/resourceService';

export default function LandingPage() {
    const [categories, setCategories] = useState([]);

    useEffect(() => {
        getResourceCategories().then(r => setCategories(r.data.data.slice(0, 6))).catch(() => {});
    }, []);

    return (
        <div>
            <section className="bg-gradient-to-br from-indigo-900 via-indigo-800 to-purple-900 text-white">
                <div className="max-w-6xl mx-auto px-4 py-24 text-center">
                    <h1 className="text-4xl md:text-5xl font-bold mb-4">Lapor. Aman. Tanpa Takut.</h1>
                    <p className="text-lg text-indigo-200 mb-8 max-w-2xl mx-auto">
                        Platform pelaporan insiden kampus yang aman dan anonim. Suaramu didengar, identitasmu terlindungi.
                    </p>
                    <div className="flex flex-col sm:flex-row gap-4 justify-center">
                        <Link to="/verify"
                            className="px-8 py-3 bg-white text-indigo-900 rounded-lg font-semibold hover:bg-indigo-50 transition">
                            Buat Laporan Baru
                        </Link>
                        <Link to="/tracking"
                            className="px-8 py-3 bg-indigo-700 text-white rounded-lg font-semibold hover:bg-indigo-600 transition border border-indigo-500">
                            Cek Status Laporan
                        </Link>
                    </div>
                </div>
            </section>

            <section className="max-w-6xl mx-auto px-4 py-16">
                <div className="grid md:grid-cols-3 gap-6">
                    <div className="bg-white p-6 rounded-lg shadow-sm border text-center">
                        <div className="text-3xl mb-3">🛡️</div>
                        <h3 className="font-semibold text-lg mb-2">100% Anonim</h3>
                        <p className="text-gray-600 text-sm">Identitasmu dilindungi. Kami tidak menyimpan data pribadi yang bisa melacak kamu.</p>
                    </div>
                    <div className="bg-white p-6 rounded-lg shadow-sm border text-center">
                        <div className="text-3xl mb-3">🎤</div>
                        <h3 className="font-semibold text-lg mb-2">Voice-to-Text</h3>
                        <p className="text-gray-600 text-sm">Cukup rekam suaramu, sistem akan mengubahnya menjadi teks laporan.</p>
                    </div>
                    <div className="bg-white p-6 rounded-lg shadow-sm border text-center">
                        <div className="text-3xl mb-3">📊</div>
                        <h3 className="font-semibold text-lg mb-2">Terpantau</h3>
                        <p className="text-gray-600 text-sm">Pantau status laporanmu secara real-time dengan Tracking ID.</p>
                    </div>
                </div>
            </section>

            <section className="bg-gray-50 py-16">
                <div className="max-w-6xl mx-auto px-4">
                    <h2 className="text-2xl font-bold text-center mb-12">Cara Kerja</h2>
                    <div className="grid md:grid-cols-3 gap-8">
                        {[
                            { step: '1', title: 'Verifikasi Email', desc: 'Masukkan email kampus kamu untuk verifikasi.' },
                            { step: '2', title: 'Buat Laporan', desc: 'Isi detail insiden secara lengkap dan anonim.' },
                            { step: '3', title: 'Pantau & Dapatkan Bantuan', desc: 'Lacak status laporan dan dapatkan dukungan.' },
                        ].map((item) => (
                            <div key={item.step} className="text-center">
                                <div className="w-12 h-12 bg-indigo-600 text-white rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-4">
                                    {item.step}
                                </div>
                                <h3 className="font-semibold text-lg mb-2">{item.title}</h3>
                                <p className="text-gray-600 text-sm">{item.desc}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {categories.length > 0 && (
                <section className="max-w-6xl mx-auto px-4 py-16">
                    <h2 className="text-2xl font-bold text-center mb-8">Resource Center</h2>
                    <div className="grid md:grid-cols-3 gap-4">
                        {categories.map((cat) => (
                            <Link key={cat.id} to="/resources"
                                className="bg-white p-4 rounded-lg shadow-sm border hover:shadow-md transition">
                                <h3 className="font-semibold">{cat.name}</h3>
                                <p className="text-xs text-gray-500 mt-1">{cat.description}</p>
                            </Link>
                        ))}
                    </div>
                </section>
            )}
        </div>
    );
}

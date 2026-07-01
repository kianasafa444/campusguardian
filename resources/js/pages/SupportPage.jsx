import { useState, useEffect } from 'react';
import { useSearchParams, Link } from 'react-router-dom';
import api from '../services/api';
import { LoadingSpinner } from '../components/ui/LoadingSpinner';

export default function SupportPage() {
    const [searchParams] = useSearchParams();
    const [trackingId, setTrackingId] = useState(searchParams.get('tracking_id') || '');
    const [supportTypes, setSupportTypes] = useState([]);
    const [form, setForm] = useState({ support_type_id: '', description: '' });
    const [submitting, setSubmitting] = useState(false);
    const [success, setSuccess] = useState('');
    const [error, setError] = useState('');
    const [history, setHistory] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        api.get('/resource-categories').then(() => {}).catch(() => {});
        setLoading(false);
        if (trackingId) loadHistory(trackingId);
    }, []);

    const loadHistory = async (id) => {
        try {
            const res = await api.get(`/support-requests/${id}`);
            setHistory(res.data.data);
        } catch {}
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setSubmitting(true);
        setError('');
        setSuccess('');

        try {
            const res = await api.post('/support-requests', {
                tracking_id: trackingId,
                support_type_id: form.support_type_id,
                description: form.description,
            });
            if (res.data.success) {
                setSuccess('Permohonan dukungan berhasil dikirim.');
                setForm({ support_type_id: '', description: '' });
                loadHistory(trackingId);
            }
        } catch (err) {
            setError(err.response?.data?.message || 'Gagal mengirim permohonan.');
        } finally {
            setSubmitting(false);
        }
    };

    if (loading) return <LoadingSpinner />;

    return (
        <div className="min-h-screen bg-gray-50 py-8 px-4">
            <div className="max-w-lg mx-auto">
                <Link to="/tracking" className="text-indigo-600 hover:underline text-sm mb-4 inline-block">← Kembali</Link>
                <h1 className="text-2xl font-bold mb-6">Ajukan Permohonan Dukungan</h1>

                <form onSubmit={handleSubmit} className="bg-white p-6 rounded-lg shadow-sm space-y-4 mb-6">
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Tracking ID</label>
                        <input
                            type="text"
                            value={trackingId}
                            onChange={(e) => setTrackingId(e.target.value.toUpperCase())}
                            className="w-full px-4 py-2 border rounded-lg uppercase"
                            required
                        />
                    </div>
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Jenis Dukungan</label>
                        <div className="space-y-2">
                            {[
                                { id: 1, name: 'Konseling Psikologis' },
                                { id: 2, name: 'Pendampingan Akademik' },
                                { id: 3, name: 'Bantuan Hukum' },
                                { id: 4, name: 'Pendampingan Satgas' },
                            ].map((type) => (
                                <label key={type.id} className="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input
                                        type="radio"
                                        name="support_type"
                                        value={type.id}
                                        checked={form.support_type_id === type.id}
                                        onChange={(e) => setForm({ ...form, support_type_id: parseInt(e.target.value) })}
                                        className="mr-3"
                                    />
                                    {type.name}
                                </label>
                            ))}
                        </div>
                    </div>
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea
                            value={form.description}
                            onChange={(e) => setForm({ ...form, description: e.target.value })}
                            rows={4}
                            className="w-full px-4 py-2 border rounded-lg"
                            required
                        />
                    </div>
                    {error && <p className="text-red-600 text-sm">{error}</p>}
                    {success && <p className="text-green-600 text-sm">{success}</p>}
                    <button type="submit" disabled={submitting}
                        className="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition disabled:opacity-50">
                        {submitting ? 'Mengirim...' : 'Kirim Permohonan'}
                    </button>
                </form>

                {history.length > 0 && (
                    <div className="bg-white p-6 rounded-lg shadow-sm">
                        <h2 className="font-semibold mb-4">Riwayat Permohonan</h2>
                        {history.map((item) => (
                            <div key={item.id} className="border-b last:border-0 py-3">
                                <p className="font-medium text-sm">{item.support_type}</p>
                                <p className="text-sm text-gray-600">{item.description}</p>
                                <div className="flex justify-between mt-1">
                                    <span className={`text-xs px-2 py-0.5 rounded ${
                                        item.status === 'Completed' ? 'bg-green-100 text-green-800' :
                                        item.status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800'
                                    }`}>{item.status}</span>
                                    <span className="text-xs text-gray-400">{new Date(item.created_at).toLocaleDateString('id-ID')}</span>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>
        </div>
    );
}

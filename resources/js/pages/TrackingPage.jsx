import { useState } from 'react';
import { Link } from 'react-router-dom';
import { trackReport } from '../services/trackingService';
import { StatusBadge, SeverityBadge } from '../components/ui/Badge';
import { LoadingSpinner } from '../components/ui/LoadingSpinner';

const STEPS = ['Submitted', 'Under Review', 'Investigation', 'Action Taken', 'Resolved'];

export default function TrackingPage() {
    const [trackingId, setTrackingId] = useState('');
    const [loading, setLoading] = useState(false);
    const [result, setResult] = useState(null);
    const [error, setError] = useState('');

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (!trackingId.trim()) { setError('Masukkan Tracking ID.'); return; }

        setLoading(true);
        setError('');
        setResult(null);

        try {
            const res = await trackReport(trackingId.toUpperCase());
            setResult(res.data.data);
        } catch (err) {
            if (err.response?.status === 404) {
                setError('Laporan tidak ditemukan. Periksa kembali Tracking ID kamu.');
            } else {
                setError('Terjadi kesalahan. Silakan coba lagi.');
            }
        } finally {
            setLoading(false);
        }
    };

    const currentStepIndex = result ? STEPS.indexOf(result.status) : -1;

    return (
        <div className="min-h-screen bg-gray-50 py-8 px-4">
            <div className="max-w-lg mx-auto">
                <h1 className="text-2xl font-bold text-center mb-6">Cek Status Laporan</h1>
                <form onSubmit={handleSubmit} className="bg-white p-6 rounded-lg shadow-sm mb-6">
                    <div className="flex gap-2">
                        <input
                            type="text"
                            value={trackingId}
                            onChange={(e) => setTrackingId(e.target.value.toUpperCase())}
                            placeholder="Masukkan Tracking ID (CG-XXXXXXXX)"
                            className="flex-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 uppercase"
                            maxLength={20}
                        />
                        <button type="submit" disabled={loading}
                            className="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition disabled:opacity-50">
                            {loading ? '...' : 'Lacak'}
                        </button>
                    </div>
                    {error && <p className="text-red-600 text-sm mt-2">{error}</p>}
                </form>

                {loading && <LoadingSpinner />}

                {result && (
                    <div className="bg-white p-6 rounded-lg shadow-sm space-y-6">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-2xl font-bold">{result.tracking_id}</p>
                                <p className="text-sm text-gray-500">{result.category}</p>
                            </div>
                            <div className="flex gap-2">
                                <StatusBadge status={result.status} />
                                <SeverityBadge severity={result.severity} />
                            </div>
                        </div>

                        <div className="relative">
                            {STEPS.map((step, i) => (
                                <div key={step} className="flex items-start mb-6 last:mb-0">
                                    <div className="flex flex-col items-center mr-4">
                                        <div className={`w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium
                                            ${i <= currentStepIndex ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-500'}`}>
                                            {i + 1}
                                        </div>
                                        {i < STEPS.length - 1 && (
                                            <div className={`w-0.5 h-8 ${i < currentStepIndex ? 'bg-indigo-600' : 'bg-gray-200'}`} />
                                        )}
                                    </div>
                                    <div className="pt-1.5">
                                        <p className={`font-medium ${i <= currentStepIndex ? 'text-gray-900' : 'text-gray-400'}`}>
                                            {step}
                                        </p>
                                    </div>
                                </div>
                            ))}
                        </div>

                        <div className="flex gap-3">
                            <Link to={`/tracking/${result.tracking_id}/timeline`}
                                className="flex-1 text-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm">
                                Lihat Timeline
                            </Link>
                            <Link to={`/support?tracking_id=${result.tracking_id}`}
                                className="flex-1 text-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm">
                                Ajukan Bantuan
                            </Link>
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
}

import { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import { getTimeline } from '../services/trackingService';
import { LoadingSpinner } from '../components/ui/LoadingSpinner';

export default function TimelinePage() {
    const { trackingId } = useParams();
    const [timeline, setTimeline] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');

    useEffect(() => {
        getTimeline(trackingId)
            .then(r => { setTimeline(r.data.data); setLoading(false); })
            .catch(() => { setError('Gagal memuat timeline.'); setLoading(false); });
    }, [trackingId]);

    if (loading) return <LoadingSpinner />;

    return (
        <div className="min-h-screen bg-gray-50 py-8 px-4">
            <div className="max-w-lg mx-auto">
                <Link to="/tracking" className="text-indigo-600 hover:underline text-sm mb-4 inline-block">← Kembali</Link>
                <h1 className="text-2xl font-bold mb-6">Timeline Laporan</h1>
                <p className="text-sm text-gray-500 mb-6">Tracking ID: {trackingId}</p>

                {error && <p className="text-red-600">{error}</p>}

                {timeline.length === 0 && !error && (
                    <p className="text-gray-500 text-center py-8">Belum ada timeline.</p>
                )}

                <div className="space-y-0">
                    {timeline.map((entry, i) => (
                        <div key={i} className="flex items-start">
                            <div className="flex flex-col items-center mr-4">
                                <div className="w-3 h-3 rounded-full bg-indigo-600 mt-2" />
                                {i < timeline.length - 1 && <div className="w-0.5 h-full bg-indigo-200" />}
                            </div>
                            <div className="pb-8 flex-1">
                                <p className="text-xs text-gray-500">
                                    {new Date(entry.timestamp).toLocaleString('id-ID')}
                                </p>
                                <p className="font-medium text-sm mt-1">
                                    {entry.status_from ? `${entry.status_from} → ` : ''}{entry.status_to}
                                </p>
                                {entry.note && (
                                    <p className="text-sm text-gray-600 mt-1 bg-gray-50 p-3 rounded-lg">{entry.note}</p>
                                )}
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );
}

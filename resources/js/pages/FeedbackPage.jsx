import { useState } from 'react';
import api from '../services/api';

export default function FeedbackPage() {
    const [trackingId, setTrackingId] = useState('');
    const [rating, setRating] = useState(0);
    const [hover, setHover] = useState(0);
    const [satisfactionLevel, setSatisfactionLevel] = useState('');
    const [comment, setComment] = useState('');
    const [submitting, setSubmitting] = useState(false);
    const [success, setSuccess] = useState(false);
    const [error, setError] = useState('');

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (!trackingId) { setError('Masukkan Tracking ID.'); return; }
        if (rating === 0) { setError('Pilih rating.'); return; }
        if (!satisfactionLevel) { setError('Pilih tingkat kepuasan.'); return; }

        setSubmitting(true);
        setError('');

        try {
            await api.post('/feedback', {
                tracking_id: trackingId.toUpperCase(),
                rating,
                satisfaction_level: satisfactionLevel,
                comment,
            });
            setSuccess(true);
        } catch (err) {
            if (err.response?.status === 409) {
                setError('Kamu sudah memberikan feedback untuk laporan ini.');
            } else {
                setError(err.response?.data?.message || 'Gagal mengirim feedback.');
            }
        } finally {
            setSubmitting(false);
        }
    };

    if (success) {
        return (
            <div className="min-h-screen flex items-center justify-center bg-gray-50 px-4">
                <div className="bg-white p-8 rounded-lg shadow-md text-center max-w-md">
                    <div className="text-5xl mb-4">🙏</div>
                    <h1 className="text-2xl font-bold mb-2">Terima Kasih!</h1>
                    <p className="text-gray-500">Feedback kamu sangat berharga untuk meningkatkan layanan kami.</p>
                </div>
            </div>
        );
    }

    return (
        <div className="min-h-screen bg-gray-50 py-8 px-4">
            <div className="max-w-lg mx-auto">
                <h1 className="text-2xl font-bold mb-6">Feedback</h1>
                <form onSubmit={handleSubmit} className="bg-white p-6 rounded-lg shadow-sm space-y-6">
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
                        <label className="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                        <div className="flex gap-1">
                            {[1, 2, 3, 4, 5].map((star) => (
                                <button key={star} type="button"
                                    onClick={() => setRating(star)}
                                    onMouseEnter={() => setHover(star)}
                                    onMouseLeave={() => setHover(0)}
                                    className={`text-3xl transition ${
                                        star <= (hover || rating) ? 'text-yellow-400' : 'text-gray-300'
                                    }`}>
                                    ★
                                </button>
                            ))}
                        </div>
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Tingkat Kepuasan</label>
                        <select
                            value={satisfactionLevel}
                            onChange={(e) => setSatisfactionLevel(e.target.value)}
                            className="w-full px-4 py-2 border rounded-lg"
                            required
                        >
                            <option value="">Pilih...</option>
                            <option value="Very Dissatisfied">Very Dissatisfied</option>
                            <option value="Dissatisfied">Dissatisfied</option>
                            <option value="Neutral">Neutral</option>
                            <option value="Satisfied">Satisfied</option>
                            <option value="Very Satisfied">Very Satisfied</option>
                        </select>
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Komentar (opsional)</label>
                        <textarea
                            value={comment}
                            onChange={(e) => setComment(e.target.value)}
                            rows={4}
                            className="w-full px-4 py-2 border rounded-lg"
                        />
                    </div>

                    {error && <p className="text-red-600 text-sm">{error}</p>}

                    <button type="submit" disabled={submitting}
                        className="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition disabled:opacity-50">
                        {submitting ? 'Mengirim...' : 'Kirim Feedback'}
                    </button>
                </form>
            </div>
        </div>
    );
}

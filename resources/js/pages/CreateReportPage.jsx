import { useState, useEffect, useRef, useCallback } from 'react';
import { useNavigate } from 'react-router-dom';
import { getCategories, createReport, uploadEvidence, deleteEvidence } from '../services/reportService';
import { LoadingSpinner } from '../components/ui/LoadingSpinner';
import { isSessionActive } from '../hooks/useVerification';
import useSpeechToText from '../hooks/useSpeechToText';

export default function CreateReportPage() {
    const navigate = useNavigate();
    const [categories, setCategories] = useState([]);
    const [form, setForm] = useState({
        incident_category_id: '',
        description: '',
        location: '',
        incident_date: '',
        contact_email: '',
    });
    const [files, setFiles] = useState([]);
    const [loading, setLoading] = useState(true);
    const [submitting, setSubmitting] = useState(false);
    const [error, setError] = useState('');
    const [result, setResult] = useState(null);
    const [interimText, setInterimText] = useState('');
    const fileInputRef = useRef();

    const {
        isSupported,
        isListening,
        error: voiceError,
        setError: setVoiceError,
        startListening,
        stopListening,
    } = useSpeechToText();

    const handleVoiceResult = useCallback((finalText) => {
        setForm(prev => ({
            ...prev,
            description: prev.description + finalText,
        }));
        setInterimText('');
    }, []);

    const handleVoiceInterim = useCallback((interim) => {
        setInterimText(interim);
    }, []);

    const handleStartRecording = () => {
        setVoiceError('');
        startListening({
            onResult: handleVoiceResult,
            onInterim: handleVoiceInterim,
        });
    };

    const handleStopRecording = () => {
        stopListening();
        setInterimText('');
    };

    useEffect(() => {
        if (!isSessionActive()) {
            navigate('/verify', { replace: true });
            return;
        }
        getCategories().then(r => {
            setCategories(r.data.data);
            setLoading(false);
        }).catch(() => {
            setError('Gagal memuat kategori.');
            setLoading(false);
        });
    }, [navigate]);

    const handleFileSelect = (e) => {
        const newFiles = Array.from(e.target.files);
        const total = files.length + newFiles.length;
        if (total > 5) {
            setError('Maksimal 5 file.');
            return;
        }
        setFiles(prev => [...prev, ...newFiles].slice(0, 5));
    };

    const removeFile = (index) => {
        setFiles(prev => prev.filter((_, i) => i !== index));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (!form.incident_category_id) { setError('Pilih kategori insiden.'); return; }
        if (form.description.length < 20) { setError('Deskripsi minimal 20 karakter.'); return; }

        setSubmitting(true);
        setError('');

        try {
            const res = await createReport(form);
            const trackingId = res.data.data.tracking_id;

            for (const file of files) {
                try {
                    await uploadEvidence(trackingId, file);
                } catch { }
            }

            setResult(trackingId);
        } catch (err) {
            setError(err.response?.data?.message || 'Gagal mengirim laporan.');
        } finally {
            setSubmitting(false);
        }
    };

    if (loading) return <LoadingSpinner />;

    if (result) {
        return (
            <div className="min-h-screen flex items-center justify-center bg-gray-50 px-4">
                <div className="bg-white p-8 rounded-lg shadow-md w-full max-w-md text-center">
                    <div className="text-5xl mb-4">✅</div>
                    <h1 className="text-2xl font-bold mb-2">Laporan Terkirim!</h1>
                    <p className="text-gray-500 mb-6">Simpan Tracking ID berikut untuk memantau status laporan kamu.</p>
                    <div className="bg-indigo-50 p-4 rounded-lg mb-6">
                        <p className="text-3xl font-bold text-indigo-700 tracking-wider">{result}</p>
                    </div>
                    <button onClick={() => navigator.clipboard.writeText(result)}
                        className="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition mb-3">
                        Salin Tracking ID
                    </button>
                    <button onClick={() => navigate('/tracking')}
                        className="w-full bg-gray-100 text-gray-700 py-2 rounded-lg hover:bg-gray-200 transition">
                        Lacak Laporan
                    </button>
                </div>
            </div>
        );
    }

    return (
        <div className="min-h-screen bg-gray-50 py-8 px-4">
            <div className="max-w-2xl mx-auto">
                <h1 className="text-2xl font-bold mb-6">Buat Laporan Baru</h1>
                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="bg-white p-6 rounded-lg shadow-sm space-y-4">
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Kategori Insiden *</label>
                            <select
                                value={form.incident_category_id}
                                onChange={(e) => setForm({ ...form, incident_category_id: e.target.value })}
                                className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"
                                required
                            >
                                <option value="">Pilih kategori...</option>
                                {categories.map((cat) => (
                                    <option key={cat.id} value={cat.id}>{cat.name}</option>
                                ))}
                            </select>
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Deskripsi *</label>
                            <textarea
                                value={form.description}
                                onChange={(e) => setForm({ ...form, description: e.target.value })}
                                rows={5}
                                className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"
                                placeholder="Jelaskan secara detail apa yang terjadi..."
                                minLength={20}
                                required
                            />
                            <p className="text-xs text-gray-400 mt-1">{form.description.length} / 20 karakter minimum</p>
                            {isSupported && (
                                <div className="mt-2 flex items-center gap-3 flex-wrap">
                                    {!isListening ? (
                                        <button
                                            type="button"
                                            onClick={handleStartRecording}
                                            className="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition"
                                        >
                                            🎤 Mulai Rekam
                                        </button>
                                    ) : (
                                        <>
                                            <button
                                                type="button"
                                                onClick={handleStopRecording}
                                                className="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition"
                                            >
                                                ⏹ Stop
                                            </button>
                                            <span className="inline-flex items-center gap-1 text-sm text-red-600 font-medium">
                                                <span className="w-2 h-2 bg-red-500 rounded-full animate-pulse" />
                                                Merekam
                                            </span>
                                            <span className="text-sm text-gray-400 italic">Sedang mendengarkan...</span>
                                        </>
                                    )}
                                    {interimText && isListening && (
                                        <span className="text-sm text-gray-400 italic w-full">{interimText}</span>
                                    )}
                                    {voiceError && (
                                        <p className="text-xs text-red-600 w-full">{voiceError}</p>
                                    )}
                                </div>
                            )}
                            {!isSupported && (
                                <p className="text-xs text-amber-600 mt-1">
                                    Browser tidak mendukung fitur Voice-to-Text. Gunakan Chrome atau Edge.
                                </p>
                            )}
                        </div>
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                                <input
                                    type="text"
                                    value={form.location}
                                    onChange={(e) => setForm({ ...form, location: e.target.value })}
                                    className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"
                                    placeholder="Gedung, lantai, ruangan..."
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Tanggal Kejadian</label>
                                <input
                                    type="datetime-local"
                                    value={form.incident_date}
                                    onChange={(e) => setForm({ ...form, incident_date: e.target.value })}
                                    className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"
                                />
                            </div>
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Email Kontak (opsional)</label>
                            <input
                                type="email"
                                value={form.contact_email}
                                onChange={(e) => setForm({ ...form, contact_email: e.target.value })}
                                className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"
                                placeholder="Email untuk notifikasi lanjutan"
                            />
                        </div>
                    </div>

                    <div className="bg-white p-6 rounded-lg shadow-sm">
                        <label className="block text-sm font-medium text-gray-700 mb-2">
                            Upload Bukti (maks. 5 file, 20MB per file)
                        </label>
                        <div
                            onClick={() => fileInputRef.current?.click()}
                            className="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:border-indigo-500 transition"
                        >
                            <p className="text-gray-500">Klik untuk upload atau drag & drop file</p>
                            <p className="text-xs text-gray-400 mt-1">JPG, PNG, PDF, MP4, MP3</p>
                        </div>
                        <input
                            ref={fileInputRef}
                            type="file"
                            multiple
                            onChange={handleFileSelect}
                            className="hidden"
                            accept=".jpg,.jpeg,.png,.gif,.webp,.mp4,.webm,.mp3,.wav,.ogg,.pdf,.doc,.docx"
                        />
                        {files.length > 0 && (
                            <div className="mt-4 space-y-2">
                                {files.map((file, i) => (
                                    <div key={i} className="flex items-center justify-between bg-gray-50 p-2 rounded">
                                        <span className="text-sm truncate">{file.name}</span>
                                        <button type="button" onClick={() => removeFile(i)}
                                            className="text-red-500 text-sm hover:underline">Hapus</button>
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>

                    {error && <p className="text-red-600 text-sm">{error}</p>}

                    <button
                        type="submit"
                        disabled={submitting}
                        className="w-full bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition disabled:opacity-50"
                    >
                        {submitting ? 'Mengirim...' : 'Kirim Laporan (Anonim)'}
                    </button>
                </form>
            </div>
        </div>
    );
}

import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { sendOtp } from '../services/authService';
import { isSessionActive, saveVerificationState } from '../hooks/useVerification';

export default function VerificationPage() {
    const navigate = useNavigate();
    const [email, setEmail] = useState('');
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');

    useEffect(() => {
        if (isSessionActive()) {
            navigate('/reports/new', { replace: true });
        }
    }, [navigate]);

    const validateEmail = (email) => {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (!validateEmail(email)) {
            setError('Format email tidak valid.');
            return;
        }

        setLoading(true);
        setError('');
        setSuccess('');

        try {
            const res = await sendOtp(email);
            if (res.data.success) {
                const data = res.data.data;

                if (data.session_active) {
                    saveVerificationState({
                        token: data.verification_token,
                        verified: true,
                        expires_at: data.expires_at,
                    });
                    navigate('/reports/new', { replace: true });
                    return;
                }

                localStorage.setItem('verification_token', data.verification_token);
                setSuccess('Kode OTP telah dikirim ke email kamu.');
                setTimeout(() => navigate('/verify-otp'), 1000);
            }
        } catch (err) {
            const msg = err.response?.data?.message || 'Terjadi kesalahan. Silakan coba lagi.';
            setError(msg);
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="min-h-screen flex items-center justify-center bg-gray-50 px-4">
            <div className="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
                <h1 className="text-2xl font-bold text-center mb-2">Verifikasi Email</h1>
                <p className="text-gray-500 text-center mb-6 text-sm">
                    Masukkan email kampus kamu untuk menerima kode OTP
                </p>
                <form onSubmit={handleSubmit} className="space-y-4">
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Email Kampus</label>
                        <input
                            type="email"
                            value={email}
                            onChange={(e) => setEmail(e.target.value)}
                            placeholder="nama@student.university.ac.id"
                            className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            disabled={loading}
                        />
                    </div>
                    {error && <p className="text-red-600 text-sm">{error}</p>}
                    {success && <p className="text-green-600 text-sm">{success}</p>}
                    <button
                        type="submit"
                        disabled={loading || !email}
                        className="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {loading ? 'Mengirim OTP...' : 'Kirim Kode OTP'}
                    </button>
                </form>
            </div>
        </div>
    );
}

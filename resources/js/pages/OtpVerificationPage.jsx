import { useState, useEffect, useRef } from 'react';
import { useNavigate } from 'react-router-dom';
import { verifyOtp, resendOtp } from '../services/authService';
import { isSessionActive, saveVerificationState } from '../hooks/useVerification';

export default function OtpVerificationPage() {
    const navigate = useNavigate();
    const [otp, setOtp] = useState(['', '', '', '', '', '']);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const [cooldown, setCooldown] = useState(0);
    const inputRefs = useRef([]);

    useEffect(() => {
        if (isSessionActive()) {
            navigate('/reports/new', { replace: true });
            return;
        }
        const token = localStorage.getItem('verification_token');
        if (!token) {
            navigate('/verify', { replace: true });
        }
    }, [navigate]);

    useEffect(() => {
        if (cooldown > 0) {
            const timer = setTimeout(() => setCooldown(c => c - 1), 1000);
            return () => clearTimeout(timer);
        }
    }, [cooldown]);

    const handleChange = (index, value) => {
        if (value.length > 1) return;
        const newOtp = [...otp];
        newOtp[index] = value;
        setOtp(newOtp);

        if (value && index < 5) {
            inputRefs.current[index + 1]?.focus();
        }
    };

    const handleKeyDown = (index, e) => {
        if (e.key === 'Backspace' && !otp[index] && index > 0) {
            inputRefs.current[index - 1]?.focus();
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        const code = otp.join('');
        if (code.length !== 6) {
            setError('Masukkan 6 digit kode OTP.');
            return;
        }

        setLoading(true);
        setError('');

        try {
            const token = localStorage.getItem('verification_token');
            if (!token) {
                navigate('/verify');
                return;
            }
            const res = await verifyOtp(token, code);
            if (res.data.success) {
                const data = res.data.data || {};
                saveVerificationState({
                    token,
                    verified: true,
                    expires_at: data.expires_at,
                });
                navigate('/reports/new', { replace: true });
            }
        } catch (err) {
            const msg = err.response?.data?.message || 'Kode OTP tidak valid.';
            setError(msg);
        } finally {
            setLoading(false);
        }
    };

    const handleResend = async () => {
        setCooldown(60);
        setError('');
        try {
            const token = localStorage.getItem('verification_token');
            await resendOtp(token);
        } catch {
            setError('Gagal mengirim ulang OTP.');
        }
    };

    return (
        <div className="min-h-screen flex items-center justify-center bg-gray-50 px-4">
            <div className="bg-white p-8 rounded-lg shadow-md w-full max-w-md text-center">
                <h1 className="text-2xl font-bold mb-2">Verifikasi OTP</h1>
                <p className="text-gray-500 mb-6 text-sm">Masukkan kode 6 digit yang dikirim ke email kamu</p>
                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="flex justify-center gap-2">
                        {otp.map((digit, i) => (
                            <input
                                key={i}
                                ref={(el) => (inputRefs.current[i] = el)}
                                type="text"
                                maxLength={1}
                                value={digit}
                                onChange={(e) => handleChange(i, e.target.value)}
                                onKeyDown={(e) => handleKeyDown(i, e)}
                                className="w-12 h-12 text-center text-xl border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                disabled={loading}
                            />
                        ))}
                    </div>
                    {error && <p className="text-red-600 text-sm">{error}</p>}
                    <button
                        type="submit"
                        disabled={loading || otp.join('').length !== 6}
                        className="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition disabled:opacity-50"
                    >
                        {loading ? 'Memverifikasi...' : 'Verifikasi'}
                    </button>
                </form>
                <div className="mt-4">
                    {cooldown > 0 ? (
                        <p className="text-sm text-gray-500">Kirim ulang dalam {cooldown} detik</p>
                    ) : (
                        <button onClick={handleResend} className="text-sm text-indigo-600 hover:underline">
                            Kirim ulang OTP
                        </button>
                    )}
                </div>
            </div>
        </div>
    );
}

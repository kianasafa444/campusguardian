import { Link, useNavigate } from 'react-router-dom';
import { isSessionActive } from '../../hooks/useVerification';

export default function Navbar() {
    const navigate = useNavigate();

    const handleBuatLaporan = (e) => {
        e.preventDefault();
        if (isSessionActive()) {
            navigate('/reports/new');
        } else {
            navigate('/verify');
        }
    };

    return (
        <nav className="bg-white shadow-sm border-b">
            <div className="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
                <Link to="/" className="text-xl font-bold text-indigo-900">CampusGuardian</Link>
                <div className="flex items-center gap-4 text-sm">
                    <Link to="/" className="text-gray-600 hover:text-indigo-600">Beranda</Link>
                    <a href="/verify" onClick={handleBuatLaporan} className="text-gray-600 hover:text-indigo-600">Buat Laporan</a>
                    <Link to="/tracking" className="text-gray-600 hover:text-indigo-600">Cek Status</Link>
                    <Link to="/resources" className="text-gray-600 hover:text-indigo-600">Resource</Link>
                </div>
            </div>
        </nav>
    );
}

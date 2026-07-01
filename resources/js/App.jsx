import { BrowserRouter, Routes, Route } from 'react-router-dom';
import Navbar from './components/layout/Navbar';
import LandingPage from './pages/LandingPage';
import VerificationPage from './pages/VerificationPage';
import OtpVerificationPage from './pages/OtpVerificationPage';
import CreateReportPage from './pages/CreateReportPage';
import TrackingPage from './pages/TrackingPage';
import TimelinePage from './pages/TimelinePage';
import SupportPage from './pages/SupportPage';
import FeedbackPage from './pages/FeedbackPage';
import ResourceCenterPage from './pages/ResourceCenterPage';

export default function App() {
    return (
        <BrowserRouter>
            <div className="min-h-screen bg-gray-50">
                <Navbar />
                <Routes>
                    <Route path="/" element={<LandingPage />} />
                    <Route path="/verify" element={<VerificationPage />} />
                    <Route path="/verify-otp" element={<OtpVerificationPage />} />
                    <Route path="/reports/new" element={<CreateReportPage />} />
                    <Route path="/tracking" element={<TrackingPage />} />
                    <Route path="/tracking/:trackingId/timeline" element={<TimelinePage />} />
                    <Route path="/support" element={<SupportPage />} />
                    <Route path="/feedback" element={<FeedbackPage />} />
                    <Route path="/resources" element={<ResourceCenterPage />} />
                </Routes>
            </div>
        </BrowserRouter>
    );
}

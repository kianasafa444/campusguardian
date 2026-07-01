import api from './api';

export const sendOtp = (email) =>
    api.post('/auth/send-otp', { email });

export const verifyOtp = (verificationToken, otp) =>
    api.post('/auth/verify-otp', { verification_token: verificationToken, otp });

export const resendOtp = (verificationToken) =>
    api.post('/auth/resend-otp', { verification_token: verificationToken });

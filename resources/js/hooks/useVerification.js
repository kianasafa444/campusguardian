const STATE_KEY = 'verification_state';

export function isSessionActive() {
  const token = localStorage.getItem('verification_token');
  if (!token) return false;
  const raw = localStorage.getItem(STATE_KEY);
  if (!raw) return false;
  try {
    const state = JSON.parse(raw);
    if (!state.verified) return false;
    if (state.expires_at && new Date(state.expires_at) <= new Date()) {
      clearVerificationState();
      return false;
    }
    return true;
  } catch {
    return false;
  }
}

export function saveVerificationState({ token, verified, expires_at }) {
  if (token) localStorage.setItem('verification_token', token);
  const state = {
    verified: !!verified,
    expires_at: expires_at || null,
  };
  localStorage.setItem(STATE_KEY, JSON.stringify(state));
}

export function clearVerificationState() {
  localStorage.removeItem('verification_token');
  localStorage.removeItem(STATE_KEY);
}

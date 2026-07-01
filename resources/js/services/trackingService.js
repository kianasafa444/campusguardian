import api from './api';

export const trackReport = (trackingId) =>
    api.post('/tracking', { tracking_id: trackingId });

export const getTimeline = (trackingId) =>
    api.get(`/tracking/${trackingId}/timeline`);

import api from './api';

export const getCategories = () =>
    api.get('/categories');

export const createReport = (data) =>
    api.post('/reports', data);

export const uploadEvidence = (trackingId, file) => {
    const formData = new FormData();
    formData.append('evidence', file);
    return api.post(`/reports/${trackingId}/evidences`, formData);
};

export const deleteEvidence = (trackingId, evidenceId) =>
    api.delete(`/reports/${trackingId}/evidences/${evidenceId}`);

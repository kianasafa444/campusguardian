import api from './api';

export const getResourceCategories = () =>
    api.get('/resource-categories');

export const getResources = (categoryId) =>
    api.get('/resources', { params: categoryId ? { resource_category_id: categoryId } : {} });

export const getResourceDetail = (slug) =>
    api.get(`/resources/${slug}`);

export const getEmergencyContacts = () =>
    api.get('/emergency-contacts');

export const getFaq = () =>
    api.get('/faq');

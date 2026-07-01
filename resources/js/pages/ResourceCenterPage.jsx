import { useState, useEffect } from 'react';
import { getResourceCategories, getResources, getFaq, getEmergencyContacts } from '../services/resourceService';
import { LoadingSpinner } from '../components/ui/LoadingSpinner';

export default function ResourceCenterPage() {
    const [categories, setCategories] = useState([]);
    const [resources, setResources] = useState([]);
    const [faqs, setFaqs] = useState([]);
    const [contacts, setContacts] = useState([]);
    const [activeCategory, setActiveCategory] = useState(null);
    const [activeTab, setActiveTab] = useState('categories');
    const [loading, setLoading] = useState(true);
    const [openFaq, setOpenFaq] = useState(null);

    useEffect(() => {
        Promise.all([
            getResourceCategories(),
            getFaq(),
            getEmergencyContacts(),
        ]).then(([catRes, faqRes, contactRes]) => {
            setCategories(catRes.data.data);
            setFaqs(faqRes.data.data);
            setContacts(contactRes.data.data);
            setLoading(false);
        }).catch(() => setLoading(false));
    }, []);

    const loadResources = async (categoryId) => {
        setActiveCategory(categoryId);
        setActiveTab('resources');
        try {
            const res = await getResources(categoryId);
            setResources(res.data.data);
        } catch {}
    };

    if (loading) return <LoadingSpinner />;

    const tabs = [
        { id: 'categories', label: 'Kategori' },
        { id: 'faq', label: 'FAQ' },
        { id: 'contacts', label: 'Kontak Darurat' },
    ];

    return (
        <div className="min-h-screen bg-gray-50 py-8 px-4">
            <div className="max-w-4xl mx-auto">
                <h1 className="text-2xl font-bold mb-6">Resource Center</h1>

                <div className="flex gap-2 mb-6">
                    {tabs.map((tab) => (
                        <button key={tab.id}
                            onClick={() => setActiveTab(tab.id)}
                            className={`px-4 py-2 rounded-lg text-sm font-medium transition ${
                                activeTab === tab.id ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-100'
                            }`}>
                            {tab.label}
                        </button>
                    ))}
                </div>

                {activeTab === 'categories' && (
                    <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                        {categories.map((cat) => (
                            <button key={cat.id}
                                onClick={() => loadResources(cat.id)}
                                className="bg-white p-6 rounded-lg shadow-sm border text-left hover:shadow-md transition">
                                <h3 className="font-semibold text-lg">{cat.name}</h3>
                                <p className="text-sm text-gray-500 mt-1">{cat.description}</p>
                            </button>
                        ))}
                        {activeCategory && (
                            <div className="col-span-full mt-4">
                                <button onClick={() => { setActiveCategory(null); setResources([]); }}
                                    className="text-sm text-indigo-600 hover:underline">
                                    ← Kembali ke kategori
                                </button>
                                <div className="mt-4 space-y-3">
                                    {resources.map((r) => (
                                        <div key={r.id} className="bg-white p-4 rounded-lg shadow-sm border">
                                            <h4 className="font-medium">{r.title}</h4>
                                            {r.excerpt && <p className="text-sm text-gray-500 mt-1">{r.excerpt}</p>}
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}
                    </div>
                )}

                {activeTab === 'faq' && (
                    <div className="space-y-2">
                        {faqs.map((faq) => (
                            <div key={faq.id} className="bg-white rounded-lg shadow-sm border">
                                <button
                                    onClick={() => setOpenFaq(openFaq === faq.id ? null : faq.id)}
                                    className="w-full px-6 py-4 text-left flex justify-between items-center"
                                >
                                    <span className="font-medium">{faq.title}</span>
                                    <span className={`transform transition ${openFaq === faq.id ? 'rotate-180' : ''}`}>▼</span>
                                </button>
                                {openFaq === faq.id && (
                                    <div className="px-6 pb-4 text-gray-600 text-sm border-t pt-4">
                                        {faq.content}
                                    </div>
                                )}
                            </div>
                        ))}
                    </div>
                )}

                {activeTab === 'contacts' && (
                    <div className="grid md:grid-cols-2 gap-4">
                        {contacts.map((contact) => (
                            <div key={contact.id} className="bg-white p-6 rounded-lg shadow-sm border">
                                <h3 className="font-semibold text-lg mb-2">{contact.title}</h3>
                                <p className="text-gray-600 text-sm whitespace-pre-line">{contact.content}</p>
                            </div>
                        ))}
                    </div>
                )}
            </div>
        </div>
    );
}

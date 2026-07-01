export function StatusBadge({ status }) {
    const colors = {
        'Submitted': 'bg-blue-100 text-blue-800',
        'Under Review': 'bg-purple-100 text-purple-800',
        'Investigation': 'bg-yellow-100 text-yellow-800',
        'Action Taken': 'bg-teal-100 text-teal-800',
        'Resolved': 'bg-green-100 text-green-800',
        'Closed': 'bg-gray-100 text-gray-800',
        'Rejected': 'bg-red-100 text-red-800',
    };
    return (
        <span className={`px-3 py-1 rounded-full text-xs font-medium ${colors[status] || 'bg-gray-100 text-gray-800'}`}>
            {status}
        </span>
    );
}

export function SeverityBadge({ severity }) {
    const colors = {
        'Emergency': 'bg-red-100 text-red-800',
        'High': 'bg-orange-100 text-orange-800',
        'Medium': 'bg-yellow-100 text-yellow-800',
        'Low': 'bg-green-100 text-green-800',
    };
    return (
        <span className={`px-3 py-1 rounded-full text-xs font-medium ${colors[severity] || 'bg-gray-100 text-gray-800'}`}>
            {severity}
        </span>
    );
}

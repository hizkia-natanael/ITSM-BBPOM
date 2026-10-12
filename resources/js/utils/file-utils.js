export function formatBytes(bytes) {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}

export function getFileMeta(file) {
    const ext = file.name.split('.').pop().toLowerCase();
    if (['jpg', 'jpeg', 'png', 'webp', 'gif'].includes(ext)) {
        return { iconClass: 'ri-image-2-line text-emerald-700', iconBg: 'bg-emerald-50' };
    }
    if (ext === 'pdf') {
        return { iconClass: 'ri-file-pdf-2-line text-rose-700', iconBg: 'bg-rose-50' };
    }
    if (['doc', 'docx'].includes(ext)) {
        return { iconClass: 'ri-file-word-2-line text-blue-700', iconBg: 'bg-blue-50' };
    }
    if (['xls', 'xlsx'].includes(ext)) {
        return { iconClass: 'ri-file-excel-2-line text-emerald-800', iconBg: 'bg-emerald-50' };
    }
    if (['zip', 'rar', '7z'].includes(ext)) {
        return { iconClass: 'ri-file-zip-line text-amber-700', iconBg: 'bg-amber-50' };
    }
    return { iconClass: 'ri-file-text-line text-slate-600', iconBg: 'bg-slate-100' };
}

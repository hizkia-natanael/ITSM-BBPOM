<?php

namespace App\Models\Concerns;

trait HasAttachmentDocumentStyles
{
    public function getIconClassAttribute(): string
    {
        return match (true) {
            $this->is_image => 'ri-image-2-line text-emerald-600',
            $this->is_pdf => 'ri-file-pdf-2-line text-rose-600',
            in_array($this->file_extension, ['doc', 'docx']) => 'ri-file-word-2-line text-blue-600',
            in_array($this->file_extension, ['xls', 'xlsx', 'csv']) => 'ri-file-excel-2-line text-emerald-700',
            in_array($this->file_extension, ['zip', 'rar', '7z', 'tar', 'gz']) => 'ri-file-zip-line text-amber-600',
            in_array($this->file_extension, ['ppt', 'pptx']) => 'ri-file-ppt-2-line text-orange-600',
            in_array($this->file_extension, ['ai', 'psd', 'cdr']) => 'ri-palette-line text-purple-600',
            default => 'ri-file-text-line text-slate-600',
        };
    }

    public function getPureIconClassAttribute(): string
    {
        return match (true) {
            $this->is_image => 'ri-image-2-line',
            $this->is_pdf => 'ri-file-pdf-2-line',
            in_array($this->file_extension, ['doc', 'docx']) => 'ri-file-word-2-line',
            in_array($this->file_extension, ['xls', 'xlsx', 'csv']) => 'ri-file-excel-2-line',
            in_array($this->file_extension, ['zip', 'rar', '7z', 'tar', 'gz']) => 'ri-file-zip-line',
            in_array($this->file_extension, ['ppt', 'pptx']) => 'ri-file-ppt-2-line',
            in_array($this->file_extension, ['ai', 'psd', 'cdr']) => 'ri-palette-line',
            default => 'ri-file-text-line',
        };
    }

    public function getBadgeClassAttribute(): string
    {
        return match (true) {
            $this->is_image => 'bg-emerald-50 text-emerald-700 border-emerald-200/60',
            $this->is_pdf => 'bg-rose-50 text-rose-700 border-rose-200/60',
            in_array($this->file_extension, ['doc', 'docx']) => 'bg-blue-50 text-blue-700 border-blue-200/60',
            in_array($this->file_extension, ['zip', 'rar', '7z']) => 'bg-amber-50 text-amber-700 border-amber-200/60',
            default => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }

    public function getDocumentColorClassAttribute(): string
    {
        return match (true) {
            $this->is_image => 'bg-emerald-600 text-white',
            $this->is_pdf => 'bg-rose-600 text-white',
            in_array($this->file_extension, ['doc', 'docx']) => 'bg-blue-600 text-white',
            in_array($this->file_extension, ['xls', 'xlsx', 'csv']) => 'bg-teal-600 text-white',
            in_array($this->file_extension, ['zip', 'rar', '7z', 'tar', 'gz']) => 'bg-amber-600 text-white',
            in_array($this->file_extension, ['ppt', 'pptx']) => 'bg-orange-600 text-white',
            in_array($this->file_extension, ['ai', 'psd', 'cdr']) => 'bg-purple-600 text-white',
            default => 'bg-slate-600 text-white',
        };
    }

    public function getDocumentHexColorAttribute(): string
    {
        return match (true) {
            $this->is_image => '#059669',
            $this->is_pdf => '#e11d48',
            in_array($this->file_extension, ['doc', 'docx']) => '#2563eb',
            in_array($this->file_extension, ['xls', 'xlsx', 'csv']) => '#0d9488',
            in_array($this->file_extension, ['zip', 'rar', '7z', 'tar', 'gz']) => '#d97706',
            in_array($this->file_extension, ['ppt', 'pptx']) => '#ea580c',
            in_array($this->file_extension, ['ai', 'psd', 'cdr']) => '#9333ea',
            default => '#475569',
        };
    }

    public function getDocumentCardBgHexAttribute(): string
    {
        return match (true) {
            $this->is_image => '#f0fdf4',
            $this->is_pdf => '#fff1f2',
            in_array($this->file_extension, ['doc', 'docx']) => '#eff6ff',
            in_array($this->file_extension, ['xls', 'xlsx', 'csv']) => '#f0fdfa',
            in_array($this->file_extension, ['zip', 'rar', '7z', 'tar', 'gz']) => '#fffbeb',
            in_array($this->file_extension, ['ppt', 'pptx']) => '#fff7ed',
            in_array($this->file_extension, ['ai', 'psd', 'cdr']) => '#faf5ff',
            default => '#f8fafc',
        };
    }

    public function getDocumentCardBorderHexAttribute(): string
    {
        return match (true) {
            $this->is_image => '#bbf7d0',
            $this->is_pdf => '#fecdd3',
            in_array($this->file_extension, ['doc', 'docx']) => '#bfdbfe',
            in_array($this->file_extension, ['xls', 'xlsx', 'csv']) => '#99f6e4',
            in_array($this->file_extension, ['zip', 'rar', '7z', 'tar', 'gz']) => '#fde68a',
            in_array($this->file_extension, ['ppt', 'pptx']) => '#fed7aa',
            in_array($this->file_extension, ['ai', 'psd', 'cdr']) => '#e9d5ff',
            default => '#e2e8f0',
        };
    }

    public function getDocumentBgClassAttribute(): string
    {
        return match (true) {
            $this->is_image => 'bg-emerald-50/70 border-emerald-200/70',
            $this->is_pdf => 'bg-rose-50/70 border-rose-200/70',
            in_array($this->file_extension, ['doc', 'docx']) => 'bg-blue-50/70 border-blue-200/70',
            in_array($this->file_extension, ['xls', 'xlsx', 'csv']) => 'bg-teal-50/70 border-teal-200/70',
            in_array($this->file_extension, ['zip', 'rar', '7z', 'tar', 'gz']) => 'bg-amber-50/70 border-amber-200/70',
            in_array($this->file_extension, ['ppt', 'pptx']) => 'bg-orange-50/70 border-orange-200/70',
            in_array($this->file_extension, ['ai', 'psd', 'cdr']) => 'bg-purple-50/70 border-purple-200/70',
            default => 'bg-slate-100/70 border-slate-200/70',
        };
    }
}

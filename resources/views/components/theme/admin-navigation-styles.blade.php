<style>
/* Tombol Toggle 1 Ikon Tunggal: Sembunyikan Yang Tidak Aktif (Zero Duplicate) */
.fi-topbar-collapse-sidebar-btn-ctn { display: inline-flex !important; align-items: center !important; margin: 0 !important; }
.fi-topbar-collapse-sidebar-btn-ctn [style*="display: none"],
.fi-topbar-collapse-sidebar-btn-ctn button[style*="display: none"],
html:has(.fi-sidebar:not(.fi-sidebar-open)) .fi-topbar-close-collapse-sidebar-btn,
body:has(.fi-sidebar:not(.fi-sidebar-open)) .fi-topbar-close-collapse-sidebar-btn,
html:has(.fi-sidebar.fi-sidebar-open) .fi-topbar-open-collapse-sidebar-btn,
body:has(.fi-sidebar.fi-sidebar-open) .fi-topbar-open-collapse-sidebar-btn { display: none !important; }
.fi-topbar-collapse-sidebar-btn-ctn button:not([style*="display: none"]) {
    width: 2.25rem !important; height: 2.25rem !important; border-radius: 0.75rem !important;
    border: 1px solid #e8edf2 !important; background-color: #ffffff !important; color: #64748b !important;
    display: inline-flex !important; align-items: center !important; justify-content: center !important;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03) !important; transition: all 0.2s ease !important; cursor: pointer !important;
    padding: 0 !important; outline: none !important;
}
.fi-topbar-collapse-sidebar-btn-ctn button:hover,
html:has(.fi-sidebar:not(.fi-sidebar-open)) .fi-topbar-collapse-sidebar-btn-ctn button,
body:has(.fi-sidebar:not(.fi-sidebar-open)) .fi-topbar-collapse-sidebar-btn-ctn button {
    background-color: #ebfaf6 !important; border-color: #cbeee5 !important; color: #008775 !important;
}
.fi-topbar-collapse-sidebar-btn-ctn button svg { display: none !important; }
.fi-topbar-collapse-sidebar-btn-ctn button::after {
    content: '\ef3d' !important; font-family: 'remixicon' !important; font-size: 1.15rem !important; line-height: 1 !important;
    color: currentColor !important; display: block !important; transform: rotate(0deg) !important;
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
}
html:has(.fi-sidebar:not(.fi-sidebar-open)) .fi-topbar-collapse-sidebar-btn-ctn button::after,
body:has(.fi-sidebar:not(.fi-sidebar-open)) .fi-topbar-collapse-sidebar-btn-ctn button::after,
.fi-topbar-open-collapse-sidebar-btn::after { transform: rotate(180deg) !important; }

/* Navigasi Sidebar Rata Atas SMPD */
.fi-sidebar-nav {
    display: flex !important; flex-direction: column !important;
    justify-content: flex-start !important; align-items: stretch !important;
    gap: 0.5rem !important; padding: 0.85rem !important;
    scrollbar-width: thin !important; scrollbar-color: #cbd5e1 transparent !important;
}
.fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-nav { padding: 0.85rem 0.35rem !important; }
.fi-sidebar-nav-groups {
    display: flex !important; flex-direction: column !important;
    justify-content: flex-start !important; gap: 0.5rem !important;
    margin: 0 !important; margin-bottom: auto !important;
}
.fi-sidebar-group { margin-bottom: 0.45rem !important; }
.fi-sidebar-group-label, .fi-sidebar-group-btn span {
    font-size: 0.65rem !important; font-weight: 800 !important; color: #94a3b8 !important;
    letter-spacing: 0.08em !important; text-transform: uppercase !important;
    padding-left: 0.75rem !important; margin-bottom: 0.25rem !important; display: block !important;
}

/* Item Menu Sidebar & Signature Active State dengan Left Indicator Bar */
.fi-sidebar-item { margin: 0.15rem 0 !important; }
.fi-sidebar-item-button, .fi-sidebar-item-btn {
    position: relative !important; border-radius: 0.75rem !important;
    padding: 0.55rem 0.85rem !important; font-size: 0.875rem !important;
    font-weight: 500 !important; color: #475569 !important; gap: 0.75rem !important;
    transition: all 0.15s ease !important; display: flex !important; align-items: center !important;
}
.fi-sidebar-item-button:hover, .fi-sidebar-item-btn:hover { background-color: #f8fafc !important; color: #0f172a !important; }
.fi-sidebar-item-icon {
    width: 1.15rem !important; height: 1.15rem !important; color: #94a3b8 !important;
    transition: color 0.15s ease !important; flex-shrink: 0 !important;
}
.fi-sidebar-item-button:hover .fi-sidebar-item-icon,
.fi-sidebar-item-btn:hover .fi-sidebar-item-icon { color: #64748b !important; }
.fi-sidebar-item.fi-active .fi-sidebar-item-btn,
.fi-sidebar-item-active .fi-sidebar-item-btn {
    background-color: #ebfaf6 !important; color: #008775 !important; font-weight: 600 !important; border-radius: 0.75rem !important;
}
.fi-sidebar-item.fi-active .fi-sidebar-item-btn::before,
.fi-sidebar-item-active .fi-sidebar-item-btn::before {
    content: '' !important; position: absolute !important; left: 0 !important;
    top: 0.5rem !important; bottom: 0.5rem !important; width: 4px !important;
    background-color: #008775 !important; border-top-right-radius: 9999px !important; border-bottom-right-radius: 9999px !important;
}
.fi-sidebar-item.fi-active .fi-sidebar-item-icon,
.fi-sidebar-item-active .fi-sidebar-item-icon { color: #008775 !important; }
.fi-sidebar-item.fi-active .fi-sidebar-item-label { color: #008775 !important; font-weight: 600 !important; }
.fi-sidebar-item-badge {
    border-radius: 9999px !important; font-size: 0.6875rem !important; font-weight: 700 !important;
    padding: 0.15rem 0.5rem !important; background-color: #ebfaf6 !important; color: #008775 !important; border: 1px solid #cbeee5 !important;
}
</style>

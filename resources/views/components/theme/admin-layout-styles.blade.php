<style>
/* BBPOM SMPD (ADITS v2) Signature Layout: Synchronized Offset & Fixed Frame */
@media (min-width: 1024px) {
    .fi-sidebar {
        position: fixed !important; top: 0 !important; left: 0 !important; bottom: 0 !important;
        height: 100vh !important; height: 100dvh !important; z-index: 40 !important;
        background-color: #ffffff !important; border-right: 1px solid #e8edf2 !important;
        box-shadow: none !important; transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
    .fi-topbar-ctn, .fi-main-ctn {
        margin-left: 16.5rem !important;
        transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
    html:has(.fi-sidebar:not(.fi-sidebar-open)) .fi-topbar-ctn,
    body:has(.fi-sidebar:not(.fi-sidebar-open)) .fi-topbar-ctn,
    html:has(.fi-sidebar:not(.fi-sidebar-open)) .fi-main-ctn,
    body:has(.fi-sidebar:not(.fi-sidebar-open)) .fi-main-ctn { margin-left: 4.5rem !important; }
    html:has(.fi-sidebar:not(.fi-sidebar-open):hover) .fi-topbar-ctn,
    body:has(.fi-sidebar:not(.fi-sidebar-open):hover) .fi-topbar-ctn,
    html:has(.fi-sidebar:not(.fi-sidebar-open):hover) .fi-main-ctn,
    body:has(.fi-sidebar:not(.fi-sidebar-open):hover) .fi-main-ctn { margin-left: 16.5rem !important; }
    .fi-sidebar:not(.fi-sidebar-open):hover {
        width: 16.5rem !important; z-index: 50 !important;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
    }
    .fi-sidebar:not(.fi-sidebar-open):hover .fi-sidebar-header { justify-content: center !important; }
    .fi-sidebar:not(.fi-sidebar-open):hover .fi-sidebar-item-label { display: inline-block !important; }
    .fi-sidebar:not(.fi-sidebar-open):hover .fi-sidebar-nav { padding: 0.85rem !important; }
    html:has(.fi-sidebar:not(.fi-sidebar-open):hover) .fi-topbar-collapse-sidebar-btn-ctn button::after,
    body:has(.fi-sidebar:not(.fi-sidebar-open):hover) .fi-topbar-collapse-sidebar-btn-ctn button::after {
        transform: rotate(0deg) !important;
    }
}

/* Sidebar Brand Header: Adaptive Open (ELITE + Role Badge) vs Closed (Pure BPOM Emblem) */
.fi-sidebar-header {
    height: 4.25rem !important; min-height: 4.25rem !important;
    display: flex !important; align-items: center !important; justify-content: center !important;
    padding: 0 1rem !important; border-bottom: 1px solid #e8edf2 !important;
    background-color: #ffffff !important; box-sizing: border-box !important; overflow: hidden !important;
}
.fi-sidebar-header-logo-ctn {
    display: flex !important; align-items: center !important; justify-content: center !important;
    width: 100% !important; flex: 1 !important; min-width: 0 !important;
}
.fi-sidebar-header-logo-ctn a, .fi-sidebar-header-logo-ctn .fi-logo {
    display: inline-flex !important; align-items: center !important; justify-content: center !important;
    text-decoration: none !important; height: auto !important; max-width: 100% !important;
}
.bbpom-brand-wrapper {
    display: inline-flex !important; flex-direction: row !important; align-items: center !important;
    justify-content: center !important; gap: 0.65rem !important; height: 100% !important;
    text-decoration: none !important; vertical-align: middle !important;
}
.bbpom-brand-img {
    height: 2.25rem !important; max-height: 36px !important; width: auto !important;
    object-fit: contain !important; display: block !important; flex-shrink: 0 !important;
    transition: transform 0.2s ease !important;
}
.bbpom-brand-text {
    display: flex !important; flex-direction: column !important; justify-content: center !important;
    text-align: left !important; line-height: 1.15 !important; flex-shrink: 0 !important;
    white-space: nowrap !important;
}
.bbpom-brand-title-row {
    display: flex !important; align-items: center !important; gap: 0.4rem !important; line-height: 1 !important;
}
.bbpom-brand-name {
    font-size: 0.95rem !important; font-weight: 900 !important; color: #065f46 !important;
    letter-spacing: 0.04em !important; line-height: 1.1 !important;
}
.bbpom-brand-divider {
    width: 1px !important; height: 0.75rem !important; background-color: #cbd5e1 !important;
    display: inline-block !important; flex-shrink: 0 !important;
}
.bbpom-brand-role-badge {
    display: inline-flex !important; align-items: center !important; padding: 0.125rem 0.45rem !important;
    border-radius: 9999px !important; font-size: 0.6rem !important; font-weight: 800 !important;
    color: #065f46 !important; background-color: #ecfdf5 !important;
    border: 1px solid rgba(16, 185, 129, 0.28) !important;
    letter-spacing: 0.04em !important; text-transform: uppercase !important; line-height: 1.1 !important;
}
.bbpom-brand-subtitle {
    display: block !important; font-size: 0.65rem !important; font-weight: 700 !important;
    color: #047857 !important; letter-spacing: 0.01em !important; line-height: 1.2 !important;
    margin-top: 2px !important; white-space: nowrap !important;
}
.fi-sidebar:not(.fi-sidebar-open):not(:hover) .fi-sidebar-header {
    padding: 0 0.5rem !important; justify-content: center !important;
}
.fi-sidebar:not(.fi-sidebar-open):not(:hover) .bbpom-brand-wrapper {
    gap: 0 !important; justify-content: center !important;
}
.fi-sidebar:not(.fi-sidebar-open):not(:hover) .bbpom-brand-text {
    display: none !important;
}
.fi-sidebar:not(.fi-sidebar-open):not(:hover) .bbpom-brand-img {
    height: 2.35rem !important; max-height: 38px !important; margin: 0 auto !important;
}

/* Navbar Sebelah Sidebar (Logo Navbar Dinonaktifkan, Terpusat di Sidebar) */
.fi-topbar {
    position: sticky !important; top: 0 !important; z-index: 30 !important;
    height: 4.25rem !important; min-height: 4.25rem !important;
    background-color: rgba(255, 255, 255, 0.95) !important; backdrop-filter: blur(12px) !important;
    border-bottom: 1px solid #e8edf2 !important; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.02) !important;
    padding-left: 1rem !important; padding-right: 1.5rem !important;
}
.fi-topbar-start { display: flex !important; align-items: center !important; gap: 0.75rem !important; }
.fi-topbar-start a, .fi-topbar-start .fi-logo, .fi-topbar-start .fi-logo-light { display: none !important; }
</style>

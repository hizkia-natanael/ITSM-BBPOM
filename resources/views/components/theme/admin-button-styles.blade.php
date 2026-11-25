<style>
/* BBPOM Crisp Corporate Solid Button System (Filament v4, WCAG AA >= 4.5:1) */
.fi-btn {
    font-weight: 600 !important;
    border-radius: 0.75rem !important;
    transition: background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease, box-shadow 0.15s ease, transform 0.15s ease !important;
}

/* Primary & Success Solid: Emerald-700 (#047857) + Pure White Semibold (5.48:1 AA) */
.fi-btn.fi-color-primary:not(.fi-outlined):not(label),
.fi-btn.fi-color-success:not(.fi-outlined):not(label),
input:checked + label.fi-btn.fi-color-primary:not(.fi-outlined),
input:checked + label.fi-btn.fi-color-success:not(.fi-outlined),
.bbpom-header-create-btn {
    background: #047857 !important;
    color: #ffffff !important;
    font-weight: 600 !important;
    border: 1px solid rgba(6, 78, 59, 0.28) !important;
    box-shadow: 0 1px 2px rgba(4, 120, 87, 0.22), inset 0 1px 0 rgba(255, 255, 255, 0.14) !important;
}
.fi-btn.fi-color-primary:not(.fi-outlined):not(label):hover:not(:disabled):not(.fi-disabled),
.fi-btn.fi-color-success:not(.fi-outlined):not(label):hover:not(:disabled):not(.fi-disabled),
input:checked + label.fi-btn.fi-color-primary:not(.fi-outlined):hover,
input:checked + label.fi-btn.fi-color-success:not(.fi-outlined):hover,
.bbpom-header-create-btn:hover:not(:disabled):not(.fi-disabled) {
    background: #065f46 !important;
    color: #ffffff !important;
    border-color: rgba(6, 78, 59, 0.45) !important;
    box-shadow: 0 4px 10px -2px rgba(4, 120, 87, 0.34), inset 0 1px 0 rgba(255, 255, 255, 0.16) !important;
    transform: translateY(-1px) !important;
}
.fi-btn.fi-color-primary:not(.fi-outlined):not(label):active:not(:disabled):not(.fi-disabled),
.fi-btn.fi-color-success:not(.fi-outlined):not(label):active:not(:disabled):not(.fi-disabled),
.bbpom-header-create-btn:active:not(:disabled):not(.fi-disabled) {
    background: #064e3b !important;
    color: #ffffff !important;
    transform: translateY(0) !important;
}
.fi-btn.fi-color-primary:not(.fi-outlined):not(label):focus-visible,
.fi-btn.fi-color-success:not(.fi-outlined):not(label):focus-visible,
.bbpom-header-create-btn:focus-visible { outline: 2px solid #047857 !important; outline-offset: 2px !important; }

/* Warning Solid: Amber-700 (#B45309) + Pure White Semibold (5.02:1 AA) */
.fi-btn.fi-color-warning:not(.fi-outlined):not(label),
input:checked + label.fi-btn.fi-color-warning:not(.fi-outlined) {
    background: #b45309 !important;
    color: #ffffff !important;
    font-weight: 600 !important;
    border: 1px solid rgba(120, 53, 15, 0.28) !important;
    box-shadow: 0 1px 2px rgba(180, 83, 9, 0.22), inset 0 1px 0 rgba(255, 255, 255, 0.14) !important;
}
.fi-btn.fi-color-warning:not(.fi-outlined):not(label):hover:not(:disabled):not(.fi-disabled) {
    background: #92400e !important; color: #ffffff !important; transform: translateY(-1px) !important;
}
.fi-btn.fi-color-warning:not(.fi-outlined):not(label):focus-visible { outline: 2px solid #b45309 !important; outline-offset: 2px !important; }

/* Danger Solid: Rose-600 (#E11D48) + Pure White Semibold (4.70:1 AA) */
.fi-btn.fi-color-danger:not(.fi-outlined):not(label),
input:checked + label.fi-btn.fi-color-danger:not(.fi-outlined) {
    background: #e11d48 !important;
    color: #ffffff !important;
    font-weight: 600 !important;
    border: 1px solid rgba(159, 18, 57, 0.28) !important;
    box-shadow: 0 1px 2px rgba(225, 29, 72, 0.22), inset 0 1px 0 rgba(255, 255, 255, 0.14) !important;
}
.fi-btn.fi-color-danger:not(.fi-outlined):not(label):hover:not(:disabled):not(.fi-disabled) {
    background: #be123c !important; color: #ffffff !important; transform: translateY(-1px) !important;
}
.fi-btn.fi-color-danger:not(.fi-outlined):not(label):focus-visible { outline: 2px solid #e11d48 !important; outline-offset: 2px !important; }

/* Info Solid: Blue-600 (#2563EB) + Pure White Semibold (5.17:1 AA) */
.fi-btn.fi-color-info:not(.fi-outlined):not(label),
input:checked + label.fi-btn.fi-color-info:not(.fi-outlined) {
    background: #2563eb !important;
    color: #ffffff !important;
    font-weight: 600 !important;
    border: 1px solid rgba(30, 64, 175, 0.28) !important;
    box-shadow: 0 1px 2px rgba(37, 99, 235, 0.22), inset 0 1px 0 rgba(255, 255, 255, 0.14) !important;
}
.fi-btn.fi-color-info:not(.fi-outlined):not(label):hover:not(:disabled):not(.fi-disabled) {
    background: #1d4ed8 !important; color: #ffffff !important; transform: translateY(-1px) !important;
}
.fi-btn.fi-color-info:not(.fi-outlined):not(label):focus-visible { outline: 2px solid #2563eb !important; outline-offset: 2px !important; }

/* Crisp white direct labels & icons on all solid semantic buttons (keeps badge slot untouched) */
.fi-btn.fi-color:not(.fi-outlined):not(label) > .fi-icon,
.fi-btn.fi-color:not(.fi-outlined):not(label) > svg,
.fi-btn.fi-color:not(.fi-outlined):not(label) > span,
.fi-btn.fi-color:not(.fi-outlined):not(label) .fi-btn-label,
.bbpom-header-create-btn > .fi-icon,
.bbpom-header-create-btn > svg,
.bbpom-header-create-btn > span { color: #ffffff !important; }

/* Outlined Primary & Success: Emerald-700 on White */
.fi-btn.fi-outlined.fi-color-primary,
.fi-btn.fi-outlined.fi-color-success {
    background-color: #ffffff !important; color: #047857 !important; border: 1px solid #047857 !important;
}
.fi-btn.fi-outlined.fi-color-primary:hover:not(:disabled):not(.fi-disabled),
.fi-btn.fi-outlined.fi-color-success:hover:not(:disabled):not(.fi-disabled) {
    background-color: #ecfdf5 !important; color: #065f46 !important; border-color: #065f46 !important;
}
.fi-btn.fi-outlined.fi-color-primary > .fi-icon,
.fi-btn.fi-outlined.fi-color-success > .fi-icon { color: currentColor !important; }
</style>

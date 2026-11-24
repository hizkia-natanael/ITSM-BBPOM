<style>
/* BBPOM Backup Page Scoped Styles */
.bk-container { display: flex; flex-direction: column; gap: 1.5rem; color: #0f172a; font-family: inherit; }
.bk-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04); }
.bk-header-card { background: #ffffff; border: 1px solid #e2e8f0; border-left: 4px solid #059669; border-radius: 1rem; padding: 1.25rem 1.5rem; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04); display: flex; flex-direction: column; gap: 1rem; justify-content: space-between; align-items: stretch; }
@media (min-width: 1024px) { .bk-header-card { flex-direction: row; align-items: center; gap: 1.5rem; } }
.bk-header-actions { display: flex !important; flex-direction: row !important; align-items: center !important; gap: 0.5rem !important; flex-wrap: nowrap !important; flex-shrink: 0 !important; overflow-x: auto !important; -webkit-overflow-scrolling: touch; scrollbar-width: none; }
.bk-header-actions::-webkit-scrollbar { display: none; }
.bk-header-actions form { margin: 0 !important; padding: 0 !important; display: inline-flex !important; align-items: center !important; flex-shrink: 0 !important; }
.bk-grid-4 { display: grid; grid-template-columns: repeat(1, minmax(0, 1fr)); gap: 1rem; }
@media (min-width: 640px) { .bk-grid-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
@media (min-width: 1024px) { .bk-grid-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); } }
.bk-grid-2 { display: grid; grid-template-columns: repeat(1, minmax(0, 1fr)); gap: 1rem; }
@media (min-width: 640px) { .bk-grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
.bk-stat-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1.25rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03); display: flex; flex-direction: column; justify-content: space-between; transition: all 0.2s ease; }
.bk-stat-card:hover { border-color: #cbd5e1; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
.bk-btn { display: inline-flex; align-items: center; justify-content: center; gap: 0.45rem; padding: 0 0.875rem; height: 2.375rem; box-sizing: border-box; border-radius: 0.5rem; font-size: 0.775rem; font-weight: 600; line-height: 1; text-decoration: none; cursor: pointer; border: 1px solid transparent; transition: all 0.15s ease; white-space: nowrap; flex-shrink: 0; }
.bk-btn-primary { background: #059669; color: #ffffff; box-shadow: 0 1px 2px 0 rgba(5, 150, 105, 0.2); }
.bk-btn-primary:hover { background: #047857; }
.bk-btn-blue { background: #2563eb; color: #ffffff; box-shadow: 0 1px 2px 0 rgba(37, 99, 235, 0.2); }
.bk-btn-blue:hover { background: #1d4ed8; }
.bk-btn-secondary { background: #f8fafc; color: #334155; border-color: #cbd5e1; }
.bk-btn-secondary:hover { background: #f1f5f9; color: #0f172a; }
.bk-btn-rose { background: #fff1f2; color: #be123c; border-color: #fecdd3; }
.bk-btn-rose:hover { background: #ffe4e6; }
.bk-btn-icon { padding: 0.375rem; border-radius: 0.375rem; font-size: 0.875rem; line-height: 1; border: 1px solid transparent; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; transition: all 0.15s ease; }
.bk-btn-icon-emerald { color: #059669; background: #ecfdf5; border-color: #a7f3d0; }
.bk-btn-icon-emerald:hover { background: #d1fae5; color: #047857; }
.bk-btn-icon-blue { color: #2563eb; background: #eff6ff; border-color: #bfdbfe; }
.bk-btn-icon-blue:hover { background: #dbeafe; color: #1d4ed8; }
.bk-btn-icon-rose { color: #e11d48; background: #fff1f2; border-color: #fecdd3; }
.bk-btn-icon-rose:hover { background: #ffe4e6; color: #be123c; }
.bk-badge { display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.2rem 0.5rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 700; line-height: 1; }
.bk-badge-emerald { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
.bk-badge-blue { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }
.bk-badge-amber { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
.bk-badge-rose { background: #fff1f2; color: #9f1239; border: 1px solid #fecdd3; }
.bk-badge-slate { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; }
.bk-form-group { display: flex; flex-direction: column; gap: 0.375rem; }
.bk-label { font-size: 0.75rem; font-weight: 700; color: #334155; }
.bk-hint { font-size: 0.6875rem; color: #64748b; margin-top: 0.125rem; }
.bk-input { width: 100%; border: 1px solid #cbd5e1; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.75rem; color: #0f172a; background: #ffffff; transition: border-color 0.15s ease; box-sizing: border-box; }
.bk-input:focus { outline: none; border-color: #059669; box-shadow: 0 0 0 2px rgba(5, 150, 105, 0.15); }
.bk-table-container { overflow-x: auto; border-radius: 0.75rem; border: 1px solid #f1f5f9; }
.bk-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 0.75rem; }
.bk-table th { background: #f8fafc; padding: 0.75rem; font-weight: 700; color: #475569; border-bottom: 1px solid #e2e8f0; white-space: nowrap; }
.bk-table td { padding: 0.75rem; border-bottom: 1px solid #f1f5f9; color: #334155; vertical-align: middle; }
.bk-table tbody tr:hover { background: #f8fafc; }
.bk-modal-overlay { position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 50; display: flex; align-items: center; justify-content: center; padding: 1rem; }
.bk-modal-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem; width: 100%; max-width: 28rem; padding: 1.5rem; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
@keyframes bk-spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
.bk-spin { animation: bk-spin 1s linear infinite; }
</style>

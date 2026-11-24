<style>
/* ELITE Backup Page Scoped Styles (SMPD/BAST Parity) */
.bk-container { display: flex; flex-direction: column; gap: 1.5rem; color: #0f172a; font-family: inherit; }
.bk-card { background: #ffffff; border: 1px solid rgba(226, 232, 240, 0.85); border-radius: 1rem; box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04); overflow: hidden; }
.bk-card-header { border-bottom: 1px solid #f1f5f9; padding: 1.25rem 1.5rem 1rem; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem; }
.bk-card-body { padding: 1.25rem 1.5rem 1.5rem; }
.bk-icon-box { width: 2.5rem; height: 2.5rem; border-radius: 0.75rem; background: #ecfdf5; border: 1px solid rgba(167, 243, 208, 0.8); color: #047857; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; flex-shrink: 0; }
.bk-header-bar { display: flex; flex-direction: column; justify-content: space-between; gap: 1rem; }
@media (min-width: 768px) { .bk-header-bar { flex-direction: row; align-items: center; } }
.bk-header-actions { display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; flex-shrink: 0; }
.bk-header-actions form { margin: 0; display: inline-flex; }
.bk-grid-4 { display: grid; grid-template-columns: repeat(1, minmax(0, 1fr)); gap: 1rem; }
@media (min-width: 640px) { .bk-grid-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
@media (min-width: 1280px) { .bk-grid-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); } }
.bk-grid-5 { display: grid; grid-template-columns: repeat(1, minmax(0, 1fr)); gap: 1rem; align-items: start; }
@media (min-width: 768px) { .bk-grid-5 { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
@media (min-width: 1280px) { .bk-grid-5 { grid-template-columns: repeat(5, minmax(0, 1fr)); } }
.bk-grid-2 { display: grid; grid-template-columns: repeat(1, minmax(0, 1fr)); gap: 1rem; align-items: start; }
@media (min-width: 768px) { .bk-grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
.bk-stat-card { background: #ffffff; border: 1px solid rgba(226, 232, 240, 0.85); border-radius: 1rem; padding: 1.125rem 1.25rem; box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03); display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; transition: border-color 0.2s ease, box-shadow 0.2s ease; }
.bk-stat-card:hover { border-color: #cbd5e1; box-shadow: 0 4px 10px -2px rgba(15, 23, 42, 0.06); }
.bk-stat-icon { width: 2.75rem; height: 2.75rem; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0; }
.bk-btn { display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem; padding: 0 0.875rem; height: 2.25rem; border-radius: 0.75rem; font-size: 0.75rem; font-weight: 600; line-height: 1; text-decoration: none; cursor: pointer; border: 1px solid transparent; transition: all 0.15s ease; white-space: nowrap; }
.bk-btn:disabled { opacity: 0.6; cursor: not-allowed; }
.bk-btn-sm { height: 2rem; padding: 0 0.75rem; font-size: 0.75rem; }
.bk-btn-primary { background: #047857; color: #ffffff; box-shadow: 0 1px 2px rgba(4, 120, 87, 0.2); }
.bk-btn-primary:hover:not(:disabled) { background: #065f46; }
.bk-btn-secondary { background: #ffffff; color: #334155; border-color: #e2e8f0; }
.bk-btn-secondary:hover:not(:disabled) { background: #f8fafc; color: #0f172a; }
.bk-btn-emerald-soft { background: #ecfdf5; color: #065f46; border-color: #a7f3d0; }
.bk-btn-emerald-soft:hover:not(:disabled) { background: #d1fae5; }
.bk-btn-rose-outline { background: #ffffff; color: #e11d48; border-color: #fecdd3; }
.bk-btn-rose-outline:hover:not(:disabled) { background: #fff1f2; color: #be123c; }
.bk-btn-destructive { background: #e11d48; color: #ffffff; }
.bk-btn-destructive:hover:not(:disabled) { background: #be123c; }
.bk-btn-icon-rose { padding: 0.45rem; border-radius: 0.5rem; color: #e11d48; background: transparent; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: background 0.15s ease; }
.bk-btn-icon-rose:hover { background: #fff1f2; color: #be123c; }
.bk-badge { display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.225rem 0.6rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 600; line-height: 1.2; white-space: nowrap; }
.bk-badge-emerald { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
.bk-badge-blue { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }
.bk-badge-amber { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
.bk-badge-rose { background: #fff1f2; color: #9f1239; border: 1px solid #fecdd3; }
.bk-badge-slate { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; }
.bk-pill-toggle { width: 100%; height: 2.5rem; padding: 0 0.875rem; border-radius: 0.75rem; border: 1px solid #cbd5e1; background: #ffffff; color: #334155; font-size: 0.75rem; font-weight: 600; display: flex; align-items: center; justify-content: space-between; cursor: pointer; transition: all 0.15s ease; }
.bk-pill-toggle-active { background: #ecfdf5; color: #065f46; border-color: #6ee7b7; }
.bk-label { display: flex; align-items: center; gap: 0.35rem; min-height: 1.25rem; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 0.375rem; }
.bk-input { width: 100%; height: 2.5rem; border: 1px solid #cbd5e1; border-radius: 0.75rem; padding: 0 0.75rem; font-size: 0.8125rem; color: #0f172a; background: #ffffff; box-sizing: border-box; transition: border-color 0.15s ease, box-shadow 0.15s ease; }
.bk-input:focus { outline: none; border-color: #059669; box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.12); }
.bk-input-mono { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 0.75rem; }
.bk-hint { font-size: 0.6875rem; color: #94a3b8; margin-top: 0.375rem; line-height: 1.35; }
.bk-error-text { font-size: 0.75rem; color: #e11d48; margin-top: 0.25rem; }
.bk-oauth-panel { border-radius: 1rem; border: 1px solid rgba(226, 232, 240, 0.9); background: rgba(248, 250, 252, 0.65); padding: 1.125rem 1.25rem; display: flex; flex-direction: column; gap: 1rem; }
.bk-uri-box { border-radius: 0.75rem; border: 1px solid #a7f3d0; background: rgba(236, 253, 245, 0.75); padding: 0.65rem 0.875rem; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.5rem; font-size: 0.75rem; }
.bk-uri-code { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 0.6875rem; background: #ffffff; padding: 0.2rem 0.5rem; border-radius: 0.5rem; border: 1px solid #a7f3d0; color: #1e293b; word-break: break-all; }
.bk-table-wrap { overflow-x: auto; }
.bk-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 0.75rem; }
.bk-table th { background: #f8fafc; padding: 0.75rem 1rem; font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; color: #64748b; border-bottom: 1px solid #e2e8f0; white-space: nowrap; }
.bk-table td { padding: 0.85rem 1rem; border-bottom: 1px solid #f1f5f9; color: #334155; vertical-align: middle; }
.bk-table tbody tr:hover { background: #f8fafc; }
.bk-error-pill { display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.2rem 0.5rem; border-radius: 0.375rem; background: #fff1f2; color: #be123c; border: 1px solid #fecdd3; font-size: 0.6875rem; font-weight: 500; margin-top: 0.35rem; }
.bk-error-truncate { max-width: 12.5rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.bk-error-detail-btn { background: none; border: none; padding: 0; color: #9f1239; font-weight: 700; text-decoration: underline; cursor: pointer; font-size: 0.6875rem; }
@keyframes bk-spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
.bk-spin { animation: bk-spin 1s linear infinite; }
</style>

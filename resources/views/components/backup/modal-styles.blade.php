<style>
/* Frosted-Glass Modals with ELITE Slate-to-Emerald Header */
.bk-modal-overlay {
    position: fixed;
    inset: 0;
    z-index: 60;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    background: rgba(15, 23, 42, 0.62);
    backdrop-filter: blur(8px);
}
.bk-modal-card {
    width: 100%;
    max-width: 28rem;
    background: #ffffff;
    border: 1px solid rgba(226, 232, 240, 0.9);
    border-radius: 1rem;
    box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.3);
    overflow: hidden;
}
.bk-modal-card-lg { max-width: 32rem; }
.bk-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    padding: 0.95rem 1.35rem;
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 55%, #022c22 100%);
    border-bottom: 1px solid rgba(51, 65, 85, 0.6);
}
.bk-modal-header h3 { color: #ffffff !important; }
.bk-modal-header p { color: #a7f3d0 !important; }
.bk-modal-body { padding: 1.25rem 1.375rem; display: flex; flex-direction: column; gap: 1rem; }
.bk-modal-footer {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 0.5rem;
    padding: 0.875rem 1.375rem;
    background: #f8fafc;
    border-top: 1px solid #f1f5f9;
}
.bk-modal-close {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.14);
    color: #cbd5e1;
    padding: 0.35rem;
    border-radius: 0.5rem;
    cursor: pointer;
    font-size: 1.1rem;
    line-height: 1;
}
.bk-modal-close:hover { color: #ffffff; background: rgba(255, 255, 255, 0.16); }
.bk-alert-emerald { border-radius: 0.75rem; border: 1px solid #a7f3d0; background: rgba(236, 253, 245, 0.75); padding: 0.75rem; font-size: 0.75rem; color: #334155; display: flex; align-items: flex-start; gap: 0.6rem; line-height: 1.5; }
.bk-alert-amber { border-radius: 0.75rem; border: 1px solid #fde68a; background: rgba(255, 251, 235, 0.85); padding: 0.75rem; font-size: 0.75rem; color: #78350f; display: flex; align-items: flex-start; gap: 0.6rem; line-height: 1.5; }
.bk-alert-rose { border-radius: 0.75rem; border: 1px solid #fecdd3; background: #fff1f2; padding: 0.6rem 0.75rem; font-size: 0.75rem; color: #be123c; }
.bk-error-pre { background: rgba(255, 241, 242, 0.75); border: 1px solid #fecdd3; border-radius: 0.75rem; padding: 0.875rem; max-height: 14rem; overflow-y: auto; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 0.75rem; color: #9f1239; white-space: pre-wrap; word-break: break-all; line-height: 1.55; }
</style>

.bbpom-admin-bell-wrapper {
    position: relative;
    display: inline-flex;
    align-items: center;
}
.bbpom-admin-bell-btn {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 0.75rem;
    color: #475569;
    background: transparent;
    border: none;
    cursor: pointer;
    transition: all 0.15s ease-in-out;
}
.bbpom-admin-bell-btn:hover {
    color: #047857;
    background-color: #ecfdf5;
}
.bbpom-admin-bell-count {
    position: absolute;
    top: 2px;
    right: 2px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 1.125rem;
    height: 1.125rem;
    padding: 0 0.25rem;
    border-radius: 9999px;
    background-color: #e11d48;
    color: #ffffff;
    font-size: 0.625rem;
    font-weight: 900;
    line-height: 1;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}
.bbpom-admin-bell-ping {
    position: absolute;
    inset: 0;
    border-radius: 9999px;
    background-color: #fb7185;
    opacity: 0.75;
    animation: bbpomPing 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;
}
@keyframes bbpomPing {
    75%, 100% {
        transform: scale(2);
        opacity: 0;
    }
}
.bbpom-admin-popover {
    position: absolute;
    right: 0;
    top: calc(100% + 0.5rem);
    width: 25.5rem;
    max-width: calc(100vw - 1.5rem);
    border-radius: 1.25rem;
    background-color: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(226, 232, 240, 0.9);
    box-shadow: 0 20px 30px -8px rgba(15, 23, 42, 0.18), 0 8px 12px -4px rgba(15, 23, 42, 0.08);
    z-index: 99999;
    overflow: hidden;
    font-family: inherit;
    text-align: left;
}
.bbpom-admin-popover-header {
    padding: 0.875rem 1.125rem;
    background-color: #ffffff;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
    white-space: nowrap;
}
.bbpom-admin-header-title {
    font-size: 0.875rem;
    font-weight: 700;
    color: #0f172a;
    letter-spacing: -0.01em;
    margin: 0;
    white-space: nowrap;
    flex-shrink: 0;
}
.bbpom-admin-badge-new {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.15rem 0.5rem;
    border-radius: 9999px;
    font-size: 0.625rem;
    font-weight: 700;
    background-color: #d1fae5;
    color: #065f46;
    border: 1px solid #a7f3d0;
    white-space: nowrap;
    flex-shrink: 0;
    line-height: 1;
}
.bbpom-admin-header-actions {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    white-space: nowrap;
    flex-shrink: 0;
}
.bbpom-admin-sound-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 1.75rem;
    height: 1.75rem;
    border-radius: 0.5rem;
    border: none;
    background: transparent;
    color: #94a3b8;
    cursor: pointer;
    transition: all 0.15s ease;
    flex-shrink: 0;
}
.bbpom-admin-sound-btn:hover {
    color: #047857;
    background-color: #ecfdf5;
}
.bbpom-admin-mark-all-btn {
    background: transparent;
    border: none;
    color: #64748b;
    font-size: 0.6875rem;
    font-weight: 500;
    cursor: pointer;
    transition: color 0.15s ease;
    padding: 0;
    white-space: nowrap;
    flex-shrink: 0;
}
.bbpom-admin-mark-all-btn:hover {
    color: #047857;
}
.bbpom-admin-view-all-link {
    font-size: 0.75rem;
    font-weight: 600;
    color: #059669;
    text-decoration: none;
    transition: color 0.15s ease;
    white-space: nowrap;
    flex-shrink: 0;
}
.bbpom-admin-view-all-link:hover {
    color: #047857;
}
.bbpom-admin-toast {
    padding: 0.5rem 1rem;
    background-color: #ecfdf5;
    border-bottom: 1px solid #d1fae5;
    color: #065f46;
    font-size: 0.6875rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.375rem;
}
.bbpom-admin-feed {
    max-height: 22rem;
    overflow-y: auto;
}
.bbpom-admin-feed::-webkit-scrollbar {
    width: 4px;
}
.bbpom-admin-feed::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}
.bbpom-admin-item {
    display: flex;
    align-items: flex-start;
    gap: 0.875rem;
    padding: 0.875rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    transition: background-color 0.15s ease;
    position: relative;
    text-decoration: none;
}
.bbpom-admin-item.unread {
    background-color: rgba(236, 253, 245, 0.45);
}
.bbpom-admin-item.read {
    background-color: #ffffff;
}
.bbpom-admin-item:hover {
    background-color: #f8fafc;
}
.bbpom-admin-item.unread:hover {
    background-color: rgba(236, 253, 245, 0.7);
}
.bbpom-admin-avatar {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1.125rem;
    transition: transform 0.15s ease;
}
.bbpom-admin-item:hover .bbpom-admin-avatar {
    transform: scale(1.05);
}
.bbpom-admin-avatar-amber {
    background-color: #fef3c7;
    color: #b45309;
    border: 1px solid rgba(253, 230, 138, 0.8);
}
.bbpom-admin-avatar-sky {
    background-color: #e0f2fe;
    color: #0369a1;
    border: 1px solid rgba(186, 230, 253, 0.8);
}
.bbpom-admin-content {
    flex: 1;
    min-width: 0;
    padding-right: 0.25rem;
}
.bbpom-admin-title-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
}
.bbpom-admin-item-title {
    font-size: 0.75rem;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.3;
    text-decoration: none;
    margin: 0;
    display: block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.bbpom-admin-item-title:hover {
    color: #047857;
}
.bbpom-admin-item-desc {
    font-size: 0.6875rem;
    color: #475569;
    line-height: 1.45;
    margin: 0.25rem 0 0 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.bbpom-admin-actions-row {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.5rem;
    padding-top: 0.5rem;
    border-top: 1px solid #f1f5f9;
}
.bbpom-admin-claim-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.625rem;
    border-radius: 0.375rem;
    font-size: 0.6875rem;
    font-weight: 700;
    background-color: #059669;
    color: #ffffff;
    border: none;
    cursor: pointer;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    transition: background-color 0.15s ease;
}
.bbpom-admin-claim-btn:hover {
    background-color: #047857;
}
.bbpom-admin-claim-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
.bbpom-admin-detail-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.6875rem;
    font-weight: 500;
    color: #475569;
    text-decoration: none;
    transition: all 0.15s ease;
}
.bbpom-admin-detail-btn:hover {
    color: #047857;
    background-color: #f1f5f9;
}
.bbpom-admin-time {
    display: block;
    font-size: 0.625rem;
    font-weight: 500;
    color: #94a3b8;
    margin-top: 0.35rem;
}
.bbpom-admin-unread-dot {
    width: 0.625rem;
    height: 0.625rem;
    border-radius: 9999px;
    background-color: #10b981;
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.18);
    flex-shrink: 0;
    align-self: center;
    margin-left: 0.25rem;
}
.bbpom-admin-empty {
    padding: 2.5rem 1rem;
    text-align: center;
}
.bbpom-admin-empty-icon {
    width: 3rem;
    height: 3rem;
    margin: 0 auto 0.625rem auto;
    border-radius: 9999px;
    background-color: #ecfdf5;
    border: 1px solid #d1fae5;
    color: #059669;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}
.bbpom-admin-empty-title {
    font-size: 0.75rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 0.25rem 0;
}
.bbpom-admin-empty-desc {
    font-size: 0.6875rem;
    color: #64748b;
    margin: 0 auto;
    max-width: 16rem;
    line-height: 1.4;
}

<style>
.bbpom-survey-summary-card {
    background: linear-gradient(145deg, #0f172a 0%, #1e293b 55%, #064e3b 100%);
    border: 1px solid rgba(52, 211, 153, 0.25);
    border-radius: 1rem;
    padding: 1.25rem 1.5rem;
    color: #ffffff;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 1.25rem;
    box-shadow: 0 4px 14px -2px rgba(15, 23, 42, 0.2);
    box-sizing: border-box;
}
.bbpom-survey-badge-kpi {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.25rem 0.65rem;
    border-radius: 9999px;
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    background: rgba(16, 185, 129, 0.2);
    color: #6ee7b7;
    border: 1px solid rgba(52, 211, 153, 0.3);
}
.bbpom-survey-score-display {
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
    margin-top: 0.4rem;
}
.bbpom-survey-score-value {
    font-size: 2.5rem;
    font-weight: 900;
    line-height: 1;
    color: #ffffff;
    letter-spacing: -0.03em;
}
.bbpom-survey-score-max {
    font-size: 1.1rem;
    font-weight: 700;
    color: #34d399;
}
.bbpom-survey-stars-row {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    margin-top: 0.35rem;
}
.bbpom-survey-star-active { color: #f59e0b; font-size: 1rem; }
.bbpom-survey-star-inactive { color: #475569; font-size: 1rem; }
.bbpom-survey-count-pill {
    margin-left: 0.5rem;
    font-size: 0.725rem;
    color: #cbd5e1;
    font-weight: 500;
}
.bbpom-survey-metrics-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}
.bbpom-survey-metric-row { display: flex; flex-direction: column; gap: 0.35rem; }
.bbpom-survey-metric-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.725rem;
}
.bbpom-survey-metric-label {
    color: #cbd5e1;
    display: flex;
    align-items: center;
    gap: 0.35rem;
}
.bbpom-survey-metric-label i { color: #34d399; font-size: 0.85rem; }
.bbpom-survey-metric-val { font-weight: 700; color: #6ee7b7; }
.bbpom-survey-progress-track {
    width: 100%;
    height: 0.375rem;
    background: rgba(51, 65, 85, 0.7);
    border-radius: 9999px;
    overflow: hidden;
}
.bbpom-survey-progress-fill {
    height: 100%;
    border-radius: 9999px;
    background: linear-gradient(90deg, #10b981 0%, #34d399 100%);
    transition: width 0.3s ease;
}
.bbpom-survey-feed-container {
    display: flex;
    flex-direction: column;
    gap: 0.65rem;
    max-height: 380px;
    overflow-y: auto;
    padding-right: 0.25rem;
}
.bbpom-survey-item-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    padding: 0.85rem 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
    transition: border-color 0.15s ease, transform 0.15s ease;
    box-sizing: border-box;
}
.bbpom-survey-item-card:hover {
    border-color: #6ee7b7;
    transform: translateY(-1px);
    box-shadow: 0 3px 8px -1px rgba(0, 0, 0, 0.06);
}
.bbpom-survey-item-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
}
.bbpom-survey-item-left {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    min-width: 0;
    flex: 1 1 auto;
}
.bbpom-survey-avatar {
    width: 2rem;
    height: 2rem;
    border-radius: 0.5rem;
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    color: #ffffff;
    font-size: 0.75rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.bbpom-survey-user-info { min-width: 0; flex: 1 1 auto; }
.bbpom-survey-user-name {
    font-size: 0.8rem;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.2;
    margin: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.bbpom-survey-ticket-link {
    font-size: 0.675rem;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-weight: 600;
    color: #047857;
    text-decoration: none;
}
.bbpom-survey-ticket-link:hover { text-decoration: underline; }
.bbpom-survey-item-meta { display: flex; align-items: center; gap: 0.5rem; flex-shrink: 0; }
.bbpom-survey-rating-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.2rem 0.5rem;
    border-radius: 0.375rem;
    background: #fffbeb;
    border: 1px solid #fde68a;
    color: #92400e;
    font-size: 0.725rem;
    font-weight: 800;
}
.bbpom-survey-time { font-size: 0.675rem; color: #94a3b8; white-space: nowrap; }
.bbpom-survey-feedback-quote {
    background: #f8fafc;
    border-left: 3px solid #059669;
    border-radius: 0.375rem;
    padding: 0.5rem 0.75rem;
    font-size: 0.75rem;
    color: #334155;
    font-style: italic;
    line-height: 1.45;
}
.bbpom-survey-breakdown-row {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
    font-size: 0.675rem;
    color: #64748b;
    padding-top: 0.35rem;
    border-top: 1px dashed #f1f5f9;
}
</style>

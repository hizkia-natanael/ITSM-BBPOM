<style>
.bbpom-survey-container {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 1rem;
    padding: 1.25rem 1.5rem;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    box-sizing: border-box;
    width: 100%;
}
.bbpom-survey-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    flex-wrap: wrap;
    padding-bottom: 1rem;
    border-bottom: 1px solid #f1f5f9;
}
.bbpom-survey-header-left {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.bbpom-survey-icon-box {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 0.75rem;
    background: rgba(245, 158, 11, 0.1);
    color: #d97706;
    border: 1px solid rgba(245, 158, 11, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}
.bbpom-survey-title {
    font-size: 0.95rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
    margin: 0;
}
.bbpom-survey-subtitle {
    font-size: 0.75rem;
    color: #64748b;
    margin: 0.2rem 0 0 0;
    line-height: 1.3;
}
.bbpom-survey-view-all-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.4rem 0.85rem;
    border-radius: 0.75rem;
    font-size: 0.75rem;
    font-weight: 700;
    color: #065f46;
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    text-decoration: none;
    transition: all 0.15s ease;
}
.bbpom-survey-view-all-btn:hover {
    background: #d1fae5;
    border-color: #6ee7b7;
    color: #047857;
}
.bbpom-survey-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.25rem;
    align-items: stretch;
}
@media (min-width: 1024px) {
    .bbpom-survey-grid {
        grid-template-columns: 5fr 7fr;
    }
}
.bbpom-survey-empty-card {
    height: 100%;
    min-height: 200px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
    text-align: center;
    border-radius: 1rem;
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    box-sizing: border-box;
}
.bbpom-survey-empty-icon {
    width: 2.75rem;
    height: 2.75rem;
    border-radius: 0.75rem;
    background: #fffbeb;
    color: #d97706;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    margin-bottom: 0.5rem;
}
.bbpom-survey-empty-title {
    font-size: 0.8rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 0.25rem 0;
}
.bbpom-survey-empty-desc {
    font-size: 0.725rem;
    color: #64748b;
    max-width: 20rem;
    margin: 0;
    line-height: 1.4;
}
</style>

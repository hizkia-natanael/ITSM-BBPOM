<script>
window.eliteTicketFlowTracker = function eliteTicketFlowTracker(config) {
    const cfg = config || {};
    return {
        ticketId: cfg.ticketId || null,
        status: cfg.initialStatus || 'pending',
        updatedAt: cfg.initialUpdatedAt || '',

        init() {
            window.addEventListener('ticket-status-updated', (event) => {
                const detail = event.detail;
                if (!detail || Number(detail.ticketId) !== Number(this.ticketId)) {
                    return;
                }
                this.status = detail.status;
                if (detail.updatedAtFormatted) {
                    this.updatedAt = detail.updatedAtFormatted;
                }
            });
        },

        isDitolak() {
            return this.status === 'ditolak';
        },

        progressPercent() {
            if (this.status === 'selesai') return 100;
            if (this.status === 'diproses') return 66.66;
            if (this.status === 'diterima') return 33.33;
            return 0;
        }
    };
};
</script>

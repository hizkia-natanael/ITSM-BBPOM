@once
    <script>
        window.bbpomOnlineCounter = function bbpomOnlineCounter(initialCount, endpointUrl) {
            return {
                onlineCount: Number(initialCount) || 1,
                endpointUrl: endpointUrl || '',
                timer: null,
                init() {
                    this.startPolling();
                    document.addEventListener('visibilitychange', () => {
                        if (document.hidden) {
                            this.stopPolling();
                        } else {
                            this.refreshCount();
                            this.startPolling();
                        }
                    });
                },
                startPolling() {
                    this.stopPolling();
                    this.timer = setInterval(() => {
                        this.refreshCount();
                    }, 30000);
                },
                stopPolling() {
                    if (this.timer) {
                        clearInterval(this.timer);
                        this.timer = null;
                    }
                },
                async refreshCount() {
                    if (!this.endpointUrl) return;
                    try {
                        const res = await fetch(this.endpointUrl, {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        if (!res.ok) return;
                        const data = await res.json();
                        if (data) {
                            const nextCount = Number(data.total_visitors ?? data.online_count);
                            if (nextCount) {
                                this.onlineCount = nextCount;
                            }
                        }
                    } catch (e) {
                        // Silent fallback on transient network hiccup
                    }
                }
            };
        };
    </script>
@endonce

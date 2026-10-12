<div
    id="bbpom-desktop-push-banner"
    x-data="desktopPushController()"
    x-init="initPush()"
    style="padding: 0.5rem 0.65rem; border-top: 1px solid #f1f5f9; background-color: #f8fafc; border-radius: 0.5rem; margin-top: 0.5rem; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.4rem;"
>
    <div style="display: flex; align-items: center; gap: 0.35rem;">
        <div
            style="display: flex; align-items: center; justify-content: center; width: 1.25rem; height: 1.25rem; border-radius: 0.35rem;"
            :style="isSubscribed ? 'background-color: #d1fae5; color: #047857;' : 'background-color: #e2e8f0; color: #64748b;'"
        >
            <i :class="isSubscribed ? 'ri-notification-3-fill' : 'ri-notification-off-line'" style="font-size: 0.75rem;"></i>
        </div>
        <div style="font-size: 0.6875rem;">
            <span style="font-weight: 600; color: #475569;">Push Desktop:</span>
            <span
                style="font-weight: 700; margin-left: 0.15rem;"
                :style="isSubscribed ? 'color: #047857;' : 'color: #64748b;'"
                x-text="isSubscribed ? 'Aktif' : 'Nonaktif'"
            ></span>
        </div>
    </div>

    <div style="display: flex; align-items: center; gap: 0.3rem;">
        <span
            x-show="testMessage"
            x-text="testMessage"
            style="font-size: 0.65rem; color: #047857; font-weight: 700; margin-right: 0.15rem;"
        ></span>

        <button
            type="button"
            @click="triggerTestPush()"
            :disabled="isTesting"
            style="display: inline-flex; align-items: center; gap: 0.2rem; background-color: #ffffff; border: 1px solid #cbd5e1; border-radius: 0.375rem; padding: 0.18rem 0.45rem; font-size: 0.65rem; font-weight: 600; color: #475569; cursor: pointer; transition: all 0.15s ease;"
            onmouseover="this.style.backgroundColor='#f1f5f9';"
            onmouseout="this.style.backgroundColor='#ffffff';"
        >
            <i class="ri-send-plane-line" style="font-size: 0.6875rem;"></i>
            <span x-text="isTesting ? 'Menguji...' : 'Uji Pop-up'"></span>
        </button>

        <button
            type="button"
            @click="togglePush()"
            :disabled="isLoading"
            style="display: inline-flex; align-items: center; gap: 0.2rem; background-color: #ffffff; border: 1px solid #cbd5e1; border-radius: 0.375rem; padding: 0.18rem 0.45rem; font-size: 0.65rem; font-weight: 600; color: #475569; cursor: pointer; transition: all 0.15s ease;"
            onmouseover="this.style.backgroundColor='#f1f5f9';"
            onmouseout="this.style.backgroundColor='#ffffff';"
        >
            <i :class="isSubscribed ? 'ri-close-line' : 'ri-checkbox-circle-line'" style="font-size: 0.6875rem;"></i>
            <span x-text="isLoading ? '...' : (isSubscribed ? 'Nonaktifkan' : 'Aktifkan')"></span>
        </button>
    </div>
</div>

<script>
@include('filament.notifications.push-controller-script')
</script>

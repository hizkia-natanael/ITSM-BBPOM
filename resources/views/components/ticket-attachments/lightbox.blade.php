        @if($hasAnyImage)
            <template x-teleport="body">
                <div 
                    x-show="lightboxOpen" 
                    x-cloak 
                    :class="{ 'is-active': lightboxOpen }"
                    :style="lightboxOpen ? 'display: flex !important;' : 'display: none !important;'"
                    @keydown.escape.window="closeLightbox()"
                    class="bbpom-att-modal-overlay"
                >
                    <div 
                        @click.away="closeLightbox()" 
                        class="bbpom-att-modal-dialog"
                    >
                        <div class="bbpom-att-modal-header">
                            <div class="bbpom-att-modal-title">
                                <i class="ri-image-2-line"></i>
                                <span x-text="lightboxTitle"></span>
                            </div>
                            <button 
                                type="button" 
                                @click="closeLightbox()" 
                                class="bbpom-att-modal-close"
                                title="Tutup Pratinjau"
                            >
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                        <div class="bbpom-att-modal-body">
                            <img 
                                :src="lightboxSrc" 
                                :alt="lightboxTitle" 
                                class="bbpom-att-modal-img"
                            >
                        </div>
                    </div>
                </div>
            </template>
        @endif

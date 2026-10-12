<script>
    function guideBookViewer() {
        return {
            currentPage: 0,
            totalPages: 13,
            isFullscreen: false,
            showToc: false,
            activeFaq: 1,
            isFlipping: false,

            init: function () {
                var self = this;
                window.addEventListener('keydown', function (e) {
                    if (e.key === 'ArrowRight' || e.key === 'PageDown') {
                        self.nextPage();
                    } else if (e.key === 'ArrowLeft' || e.key === 'PageUp') {
                        self.prevPage();
                    } else if (e.key === 'Escape') {
                        self.showToc = false;
                    }
                });
            },

            nextPage: function () {
                if (this.currentPage < this.totalPages && !this.isFlipping) {
                    var self = this;
                    this.isFlipping = true;
                    this.currentPage = this.currentPage + 1;
                    setTimeout(function () { self.isFlipping = false; }, 700);
                }
            },

            prevPage: function () {
                if (this.currentPage > 0 && !this.isFlipping) {
                    var self = this;
                    this.isFlipping = true;
                    this.currentPage = this.currentPage - 1;
                    setTimeout(function () { self.isFlipping = false; }, 700);
                }
            },

            goToPage: function (index) {
                if (index >= 0 && index <= this.totalPages && !this.isFlipping) {
                    var self = this;
                    this.isFlipping = true;
                    this.currentPage = index;
                    this.showToc = false;
                    setTimeout(function () { self.isFlipping = false; }, 700);
                }
            },

            toggleFaq: function (faqId) {
                this.activeFaq = this.activeFaq === faqId ? null : faqId;
            },

            isFaqOpen: function (faqId) {
                return this.activeFaq === faqId;
            },

            toggleFullscreen: function () {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen().catch(function () {});
                    this.isFullscreen = true;
                } else if (document.exitFullscreen) {
                    document.exitFullscreen();
                    this.isFullscreen = false;
                }
            },

            printGuide: function () {
                window.print();
            },

            getChapterBadgeText: function () {
                var titles = {
                    0: 'Sampul Depan (Cover)',
                    1: 'Pengantar: Kata Sambutan',
                    2: 'Bab I: Urgensi Digitalisasi TI',
                    3: 'Bab I: Maksud, Tujuan, & Manfaat',
                    4: 'Bab II: 12 Katalog Layanan Terpadu',
                    5: 'Bab II: Detail Alur Kerja Aplikasi',
                    6: 'Bab III: Panduan Pengguna (Alur Tiket)',
                    7: 'Bab III: Panduan Pengguna (Pelacakan)',
                    8: 'Bab IV: Panduan Admin (Triage Tiket)',
                    9: 'Bab IV: Panduan Admin (Ekspor Laporan)',
                    10: 'Bab V: Tanya Jawab Umum (FAQ)',
                    11: 'Bab V: Meja Bantuan & Saluran TIK',
                    12: 'Penutup: Ikhtisar Budaya Layanan',
                    13: 'Sampul Belakang (Back Cover)'
                };
                return titles[this.currentPage] || '';
            }
        };
    }
</script>

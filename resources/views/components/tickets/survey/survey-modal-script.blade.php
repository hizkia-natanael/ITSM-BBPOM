<script>
    (function() {
        function createSurveyData() {
            return {
                open: false,
                isSubmitting: false,
                ratings: {
                    speed_rating: 5,
                    quality_rating: 5,
                    communication_rating: 5,
                    system_rating: 5
                },
                hoverRatings: {
                    speed_rating: 0,
                    quality_rating: 0,
                    communication_rating: 0,
                    system_rating: 0
                },
                labels: {
                    1: 'Kurang Sekali',
                    2: 'Kurang',
                    3: 'Cukup',
                    4: 'Baik',
                    5: 'Sangat Baik'
                },
                colors: {
                    1: 'text-rose-500',
                    2: 'text-amber-600',
                    3: 'text-amber-500',
                    4: 'text-emerald-600',
                    5: 'text-emerald-700'
                },
                setStar(name, val) {
                    this.ratings[name] = val;
                },
                hoverStar(name, val) {
                    this.hoverRatings[name] = val;
                },
                leaveStar(name) {
                    this.hoverRatings[name] = 0;
                },
                isStarActive(name, star) {
                    var current = this.hoverRatings[name] || this.ratings[name];
                    return star <= current;
                },
                getRatingLabel(name) {
                    var current = this.hoverRatings[name] || this.ratings[name];
                    return this.labels[current] || '';
                },
                getRatingColor(name) {
                    var current = this.hoverRatings[name] || this.ratings[name];
                    return this.colors[current] || 'text-slate-600';
                },
                getAverage() {
                    var sum = Number(this.ratings.speed_rating) +
                              Number(this.ratings.quality_rating) +
                              Number(this.ratings.communication_rating) +
                              Number(this.ratings.system_rating);
                    return (sum / 4).toFixed(1);
                },
                openModal() {
                    this.open = true;
                    document.body.classList.add('overflow-hidden');
                },
                closeModal() {
                    this.open = false;
                    document.body.classList.remove('overflow-hidden');
                }
            };
        }

        window.ticketSurveyData = createSurveyData;

        if (window.Alpine) {
            window.Alpine.data('ticketSurveyData', createSurveyData);
        } else {
            document.addEventListener('alpine:init', function() {
                window.Alpine.data('ticketSurveyData', createSurveyData);
            });
        }
    })();
</script>

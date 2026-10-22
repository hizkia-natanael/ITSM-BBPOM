<script>
document.title = "Admin Layanan TIK BBPOM Di Semarang"; document.addEventListener("livewire:navigated", function () { document.title = "Admin Layanan TIK BBPOM Di Semarang"; });
window.addEventListener("DOMContentLoaded", function () { document.title = "Admin Layanan TIK BBPOM Di Semarang"; });
</script>
<script>
window.filamentChartJsGlobalPlugins = window.filamentChartJsGlobalPlugins || []; window.filamentChartJsGlobalPlugins.push({ id: "bbpomSmoothAnimationPlugin", beforeInit: function (chart) {
if (!chart.options) chart.options = {}; chart.options.animation = Object.assign({ duration: 500, easing: "easeOutQuart" }, chart.options.animation);
chart.options.transitions = Object.assign({ resize: { animation: { duration: 450, easing: "easeOutQuart" } } }, chart.options.transitions); } }); window.filamentChartJsGlobalPlugins.push({
id: "bbpomChartBridgePlugin", beforeInit: function (chart) { if (chart && chart.canvas) { chart.canvas.__bbpomChart = chart; } window.__bbpomChart = chart; }, afterUpdate: function (chart) {
if (chart && chart.canvas) { chart.canvas.__bbpomChart = chart; } window.__bbpomChart = chart; } }); window.filamentChartJsGlobalPlugins.push({ id: "bbpomSoloLegendPlugin",
beforeInit: function (chart) { if (!chart.options) chart.options = {}; if (!chart.options.plugins) chart.options.plugins = {};
if (!chart.options.plugins.legend) chart.options.plugins.legend = {}; chart.options.plugins.legend.onClick = function (e, legendItem, legend) { const c = legend.chart;
const clickedIdx = legendItem.datasetIndex; let visibleCount = 0; let singleVisibleIdx = -1; for (let i = 0; i < c.data.datasets.length; i++) { if (c.isDatasetVisible(i)) { visibleCount++;
singleVisibleIdx = i; } } if (visibleCount === 1 && singleVisibleIdx === clickedIdx) { for (let i = 0; i < c.data.datasets.length; i++) { c.setDatasetVisibility(i, true); } } else {
for (let i = 0; i < c.data.datasets.length; i++) { c.setDatasetVisibility(i, i === clickedIdx); } } c.update(); }; } }); window.bbpomTrendStatusController = function(initialFilter) { return {
activeFilter: initialFilter || "all", getChart() { if (window.__bbpomChart) return window.__bbpomChart; const canvas = document.querySelector(".fi-wi-chart canvas");
if (canvas && canvas.__bbpomChart) return canvas.__bbpomChart; if (typeof Chart !== "undefined" && Chart.getChart && canvas) { return Chart.getChart(canvas); } return null; },
onHover(status) { if (this.activeFilter === status) return; this.activeFilter = status; this.applyToChart(status); }, onClick(status) { if (this.activeFilter === status && status !== "all") {
this.activeFilter = "all"; this.applyToChart("all"); } else { this.activeFilter = status; this.applyToChart(status); } }, applyToChart(status) { const chart = this.getChart();
if (!chart) return; const map = { "pending": 0, "diproses": 1, "selesai": 2, "ditolak": 3 }; if (status === "all") { for (let i = 0; i < 4; i++) { chart.setDatasetVisibility(i, true); }
} else if (map[status] !== undefined) { for (let i = 0; i < 4; i++) { chart.setDatasetVisibility(i, i === map[status]); } } chart.update(); } }; };
</script>

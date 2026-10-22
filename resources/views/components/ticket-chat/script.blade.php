<script>
window.bbpomTicketConversation = function bbpomTicketConversation(config) { const cfg = config || {}; return { ticketId: cfg.ticketId || null, replyUrl: cfg.replyUrl || '',
syncUrl: cfg.syncUrl || '', csrfToken: cfg.csrfToken || '', isSubmitting: false, hasFile: false, fileName: '', fileSizeFormatted: '', fileExtension: '', selectedFiles: [], fileCount: 0,
totalBytes: 0, compressionMode: 'compressed', errorMessage: '', replyingTo: null, lastMessageId: Number(cfg.lastMessageId) || 0, dynamicMessages: [], existingIds: null, pollTimer: null,
isPolling: false, init() { this.existingIds = new Set(cfg.initialIds || []); this.$nextTick(() => { this.scrollToBottom(); this.adjustTextareaHeight(); }); this.startPolling();
document.addEventListener('visibilitychange', () => { if (document.hidden) { this.stopPolling(); } else { this.startPolling(); this.pollSync(); } }); }, startPolling() {
if (this.pollTimer) clearInterval(this.pollTimer); this.pollTimer = setInterval(() => { this.pollSync(); }, 2500); }, stopPolling() { if (this.pollTimer) { clearInterval(this.pollTimer);
this.pollTimer = null; } }, async pollSync() { if (this.isPolling || this.isSubmitting || !this.syncUrl) return; this.isPolling = true; try {
const res = await fetch(`${this.syncUrl}?after_id=${this.lastMessageId}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', } }); if (!res.ok) {
this.isPolling = false; return; } const data = await res.json(); if (data && data.ticket_status) { window.dispatchEvent(new CustomEvent('ticket-status-updated', { detail: {
ticketId: this.ticketId, status: data.ticket_status, handledBy: data.handled_by || null, hasAdminResponse: Boolean(data.has_admin_response),
updatedAtFormatted: data.updated_at_formatted || null, } })); } if (data && data.responses && data.responses.length > 0) { let hasNew = false; for (const msg of data.responses) {
const id = Number(msg.id); if (id > this.lastMessageId) { this.lastMessageId = id; } if (!this.existingIds.has(id)) { this.existingIds.add(id); this.dynamicMessages.push(msg); hasNew = true;
} } if (hasNew) { this.$nextTick(() => { this.scrollToBottom(); }); } } } catch (err) { } finally { this.isPolling = false; } }, hasReplyTarget() {
return Boolean(this.replyingTo && this.replyingTo.id); }, setReply(msg) { this.replyingTo = msg; this.$nextTick(() => {
const el = this.$refs.replyTextarea || document.getElementById('ticket_conversation_response_text_' + this.ticketId); if (el) { el.focus(); } }); }, cancelReply() { this.replyingTo = null; },
replyAuthorText() { return 'Membalas ' + (this.replyingTo ? this.replyingTo.userName : ''); }, replySnippetText() { return this.replyingTo ? this.replyingTo.text : ''; },
scrollToMessage(id) { const el = document.getElementById('bbpom_msg_' + id); if (!el) return;
const container = this.$refs && this.$refs.streamContainer ? this.$refs.streamContainer : document.querySelector('.bbpom-chat-stream'); if (container) {
const containerRect = container.getBoundingClientRect(); const elRect = el.getBoundingClientRect();
const targetScrollTop = container.scrollTop + (elRect.top - containerRect.top) - (container.clientHeight / 2) + (elRect.clientHeight / 2);
container.scrollTo({ top: Math.max(0, targetScrollTop), behavior: 'smooth' }); } el.classList.remove('bbpom-highlight-pulse'); void el.offsetWidth; el.classList.add('bbpom-highlight-pulse');
setTimeout(() => { el.classList.remove('bbpom-highlight-pulse'); }, 1050); }, triggerFilePicker() { const input = this.getFileInput(); if (input) { input.click(); } }, hasFileSelected() {
return Boolean(this.hasFile && this.fileCount > 0); }, isMultipleFiles() { return this.fileCount > 1; }, isAboveOneMb() { return this.totalBytes > 1048576; }, isCompressedMode() {
return this.compressionMode === 'compressed'; }, isOriginalMode() { return this.compressionMode === 'original'; }, hasErrorMessage() { return this.errorMessage.length > 0; }, hasText(msg) {
return Boolean(msg && msg.response_text && msg.response_text.trim().length > 0); }, hasAttachments(msg) { return Boolean(msg && msg.attachments && msg.attachments.length > 0); },
hasReplyTo(msg) { return Boolean(msg && msg.reply_to && msg.reply_to.id); }, setMode(mode) { this.compressionMode = mode; }, handleFileChange(e) { this.errorMessage = '';
const files = e.target.files; if (!files || files.length === 0) { this.clearFile(); return; } let total = 0; const fileList = []; for (let i = 0; i < files.length; i++) { const f = files[i];
if (f.size > 52428800) { this.errorMessage = 'Ukuran berkas "' + f.name + '" melebihi batas maksimal 50 MB.'; this.clearFile(); return; } total += f.size; fileList.push({ name: f.name,
sizeFormatted: this.formatBytes(f.size), size: f.size, }); } this.selectedFiles = fileList; this.fileCount = files.length; this.totalBytes = total; this.hasFile = true;
if (this.fileCount === 1) { const single = files[0]; this.fileName = single.name; const parts = single.name.split('.'); this.fileExtension = parts.length > 1 ? parts.pop().toLowerCase() : '';
this.fileSizeFormatted = this.formatBytes(single.size); } else { this.fileName = 'Arsip ' + this.fileCount + ' Berkas Lampiran'; this.fileExtension = 'zip';
this.fileSizeFormatted = this.formatBytes(total); } }, clearFile() { this.hasFile = false; this.fileName = ''; this.fileExtension = ''; this.fileSizeFormatted = ''; this.selectedFiles = [];
this.fileCount = 0; this.totalBytes = 0; this.compressionMode = 'compressed'; const input = this.getFileInput(); if (input) { input.value = ''; } }, getFileInput() {
if (this.$refs && this.$refs.fileInput) { return this.$refs.fileInput; } return document.getElementById('ticket_conversation_file_input_' + this.ticketId); }, fileIconClass() {
if (this.isMultipleFiles()) return 'ri-folder-zip-line'; const ext = this.fileExtension; if (['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'].indexOf(ext) !== -1) return 'ri-image-2-line';
if (ext === 'pdf') return 'ri-file-pdf-2-line'; if (['doc', 'docx'].indexOf(ext) !== -1) return 'ri-file-word-2-line';
if (['xls', 'xlsx', 'csv'].indexOf(ext) !== -1) return 'ri-file-excel-2-line'; if (['zip', 'rar', '7z', 'tar', 'gz'].indexOf(ext) !== -1) return 'ri-file-zip-line';
if (['ppt', 'pptx'].indexOf(ext) !== -1) return 'ri-file-ppt-2-line'; if (['psd', 'ai', 'fig', 'sketch'].indexOf(ext) !== -1) return 'ri-palette-line'; return 'ri-file-text-line'; },
fileColorHex() { if (this.isMultipleFiles()) return '#d97706'; const ext = this.fileExtension; if (['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'].indexOf(ext) !== -1) return '#059669';
if (ext === 'pdf') return '#e11d48'; if (['doc', 'docx'].indexOf(ext) !== -1) return '#2563eb'; if (['xls', 'xlsx', 'csv'].indexOf(ext) !== -1) return '#0d9488';
if (['zip', 'rar', '7z', 'tar', 'gz'].indexOf(ext) !== -1) return '#d97706'; if (['ppt', 'pptx'].indexOf(ext) !== -1) return '#ea580c';
if (['psd', 'ai', 'fig', 'sketch'].indexOf(ext) !== -1) return '#9333ea'; return '#475569'; }, fileColorBgHex() { if (this.isMultipleFiles()) return '#fffbeb'; const ext = this.fileExtension;
if (['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'].indexOf(ext) !== -1) return '#f0fdf4'; if (ext === 'pdf') return '#fff1f2'; if (['doc', 'docx'].indexOf(ext) !== -1) return '#eff6ff';
if (['xls', 'xlsx', 'csv'].indexOf(ext) !== -1) return '#f0fdfa'; if (['zip', 'rar', '7z', 'tar', 'gz'].indexOf(ext) !== -1) return '#fffbeb';
if (['ppt', 'pptx'].indexOf(ext) !== -1) return '#fff7ed'; if (['psd', 'ai', 'fig', 'sketch'].indexOf(ext) !== -1) return '#faf5ff'; return '#f8fafc'; }, fileColorBorderHex() {
if (this.isMultipleFiles()) return '#fde68a'; const ext = this.fileExtension; if (['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'].indexOf(ext) !== -1) return '#bbf7d0';
if (ext === 'pdf') return '#fecdd3'; if (['doc', 'docx'].indexOf(ext) !== -1) return '#bfdbfe'; if (['xls', 'xlsx', 'csv'].indexOf(ext) !== -1) return '#99f6e4';
if (['zip', 'rar', '7z', 'tar', 'gz'].indexOf(ext) !== -1) return '#fde68a'; if (['ppt', 'pptx'].indexOf(ext) !== -1) return '#fed7aa';
if (['psd', 'ai', 'fig', 'sketch'].indexOf(ext) !== -1) return '#e9d5ff'; return '#e2e8f0'; }, fileExtensionUpper() { if (this.isMultipleFiles()) return this.fileCount + ' BERKAS';
return (this.fileExtension || 'FILE').toUpperCase(); }, formatBytes(bytes) { const b = Number(bytes) || 0; if (b >= 1048576) { return (b / 1048576).toFixed(1) + ' MB'; } if (b >= 1024) {
return Math.round(b / 1024) + ' KB'; } return b + ' B'; }, scrollToBottom() { if (this.$refs.streamContainer) { this.$refs.streamContainer.scrollTop = this.$refs.streamContainer.scrollHeight;
} }, adjustTextareaHeight() { const el = this.$refs.replyTextarea || document.getElementById('ticket_conversation_response_text_' + this.ticketId); if (!el) return; el.style.height = 'auto';
if (el.scrollHeight > 128) { el.style.height = '128px'; el.style.overflowY = 'auto'; } else { el.style.height = el.scrollHeight + 'px'; el.style.overflowY = 'hidden'; } },
handleEnterSend(e) { if (e && e.shiftKey) { return; } if (e) { e.preventDefault(); e.stopPropagation(); } this.submitReply(e); }, async submitReply(e) { if (e) { e.preventDefault();
e.stopPropagation(); } if (this.isSubmitting) return; const el = this.$refs.replyTextarea || document.getElementById('ticket_conversation_response_text_' + this.ticketId);
const text = el ? el.value.trim() : ''; const fileInput = this.getFileInput(); const hasSelectedFile = Boolean(fileInput && fileInput.files && fileInput.files.length > 0);
if (text.length === 0 && !hasSelectedFile) { this.errorMessage = 'Ketik pesan tanggapan atau lampirkan berkas terlebih dahulu.'; return; } this.errorMessage = ''; this.isSubmitting = true;
let token = this.csrfToken; if (!token) { const meta = document.querySelector('meta[name="csrf-token"]'); if (meta) token = meta.getAttribute('content'); } if (!token) {
const input = document.querySelector('input[name="_token"]'); if (input) token = input.value; } const formData = new FormData(); if (token) { formData.append('_token', token); }
formData.append('response_text', text); formData.append('compression_mode', this.compressionMode); if (this.hasReplyTarget()) { formData.append('reply_to_id', this.replyingTo.id); }
if (hasSelectedFile && fileInput.files && fileInput.files.length > 0) { if (fileInput.files.length === 1) { formData.append('attachment', fileInput.files[0]); } else {
for (let i = 0; i < fileInput.files.length; i++) { formData.append('attachments[]', fileInput.files[i]); } } } try { const headers = { 'X-Requested-With': 'XMLHttpRequest',
'Accept': 'application/json', }; if (token) { headers['X-CSRF-TOKEN'] = token; } const response = await fetch(this.replyUrl, { method: 'POST', headers: headers, body: formData, });
if (response.ok) { const data = await response.json().catch(() => null); if (data && data.response) { const msg = data.response; const id = Number(msg.id); if (id > this.lastMessageId) {
this.lastMessageId = id; } if (!this.existingIds.has(id)) { this.existingIds.add(id); this.dynamicMessages.push(msg); } } if (el) el.value = ''; this.clearFile(); this.cancelReply();
this.adjustTextareaHeight(); this.$nextTick(() => { this.scrollToBottom(); }); this.isSubmitting = false; return; } const data = await response.json().catch(() => null);
if (data && data.errors) { const firstKey = Object.keys(data.errors)[0]; this.errorMessage = data.errors[firstKey][0] || 'Gagal mengirim berkas.'; } else if (data && data.message) {
this.errorMessage = data.message; } else { this.errorMessage = 'Terjadi kendala saat mengirim. Silakan periksa kembali berkas Anda.'; } this.isSubmitting = false; } catch (err) {
this.errorMessage = 'Koneksi terputus saat mengunggah berkas. Silakan coba kembali.'; this.isSubmitting = false; } } }; };
</script>

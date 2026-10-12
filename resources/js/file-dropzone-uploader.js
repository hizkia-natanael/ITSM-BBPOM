import { formatBytes, getFileMeta } from './utils/file-utils.js';

export function fileDropzoneUploader(config) {
    return {
        inputId: config.inputId,
        fieldName: config.fieldName,
        maxFiles: config.maxFiles || 5,
        maxSizeBytes: (config.maxSizeMb || 5) * 1024 * 1024,
        isDragging: false,
        fileList: [],
        errorMessage: '',

        get hasFiles() {
            return this.fileList.length !== 0;
        },

        get fileCountText() {
            if (this.fileList.length === 0) {
                return 'Belum ada berkas';
            }
            return this.fileList.length + ' dari ' + this.maxFiles + ' berkas terpilih';
        },

        triggerBrowse() {
            if (this.$refs && this.$refs.fileInput) {
                this.$refs.fileInput.click();
            }
        },

        onDragOver() {
            this.isDragging = true;
        },

        onDragLeave() {
            this.isDragging = false;
        },

        onDrop(e) {
            this.isDragging = false;
            if (e.dataTransfer && e.dataTransfer.files) {
                this.addFiles(Array.from(e.dataTransfer.files));
            }
        },

        handleFileSelect(e) {
            if (e.target && e.target.files) {
                this.addFiles(Array.from(e.target.files));
            }
        },

        addFiles(newFiles) {
            this.errorMessage = '';
            var totalFilesCount = this.fileList.length + newFiles.length;

            if (totalFilesCount > this.maxFiles) {
                this.errorMessage = 'Maksimal jumlah berkas yang dapat diunggah adalah ' + this.maxFiles + ' file.';
                return;
            }

            for (var i = 0; i < newFiles.length; i++) {
                var file = newFiles[i];

                if (file.size > this.maxSizeBytes) {
                    this.errorMessage = 'Ukuran berkas "' + file.name + '" melebihi batas maksimal (' + (this.maxSizeBytes / (1024 * 1024)) + 'MB).';
                    return;
                }

                var isDuplicate = this.fileList.some(item => item.name === file.name && item.raw.size === file.size);
                if (!isDuplicate) {
                    var meta = getFileMeta(file);
                    this.fileList.push({
                        id: Date.now() + '_' + Math.random().toString(36).substr(2, 9),
                        name: file.name,
                        size: formatBytes(file.size),
                        iconClass: meta.iconClass,
                        iconBg: meta.iconBg,
                        raw: file
                    });
                }
            }

            this.syncToNativeInput();
        },

        removeFile(index) {
            this.errorMessage = '';
            this.fileList.splice(index, 1);
            this.syncToNativeInput();
        },

        clearAllFiles() {
            this.fileList = [];
            this.errorMessage = '';
            this.syncToNativeInput();
        },

        syncToNativeInput() {
            var dataTransfer = new DataTransfer();
            for (var i = 0; i < this.fileList.length; i++) {
                dataTransfer.items.add(this.fileList[i].raw);
            }
            if (this.$refs && this.$refs.fileInput) {
                this.$refs.fileInput.files = dataTransfer.files;
            }
        }
    };
}

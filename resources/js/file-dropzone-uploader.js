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

                // Cek duplikasi nama dan ukuran
                var isDuplicate = false;
                for (var j = 0; j < this.fileList.length; j++) {
                    if (this.fileList[j].name === file.name && this.fileList[j].raw.size === file.size) {
                        isDuplicate = true;
                        break;
                    }
                }

                if (!isDuplicate) {
                    var meta = this.getFileMeta(file);
                    this.fileList.push({
                        id: Date.now() + '_' + Math.random().toString(36).substr(2, 9),
                        name: file.name,
                        size: this.formatBytes(file.size),
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
        },

        formatBytes(bytes) {
            if (bytes === 0) return '0 B';
            var k = 1024;
            var sizes = ['B', 'KB', 'MB', 'GB'];
            var i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
        },

        getFileMeta(file) {
            var ext = file.name.split('.').pop().toLowerCase();
            if (['jpg', 'jpeg', 'png', 'webp', 'gif'].includes(ext)) {
                return {
                    iconClass: 'ri-image-2-line text-emerald-700',
                    iconBg: 'bg-emerald-50'
                };
            }
            if (ext === 'pdf') {
                return {
                    iconClass: 'ri-file-pdf-2-line text-rose-700',
                    iconBg: 'bg-rose-50'
                };
            }
            if (['doc', 'docx'].includes(ext)) {
                return {
                    iconClass: 'ri-file-word-2-line text-blue-700',
                    iconBg: 'bg-blue-50'
                };
            }
            if (['zip', 'rar', '7z'].includes(ext)) {
                return {
                    iconClass: 'ri-file-zip-line text-amber-700',
                    iconBg: 'bg-amber-50'
                };
            }
            return {
                iconClass: 'ri-file-text-line text-slate-700',
                iconBg: 'bg-slate-100'
            };
        }
    };
}

if (typeof window !== 'undefined') {
    window.fileDropzoneUploader = fileDropzoneUploader;
}

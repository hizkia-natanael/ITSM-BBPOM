@props([
    'name' => 'attachments[]',
    'id' => 'dropzone_' . uniqid(),
    'label' => 'Unggah Dokumen / Berkas Pendukung',
    'optional' => true,
    'maxFiles' => 5,
    'maxFileSizeMb' => 5,
    'acceptedTypes' => '.pdf,.doc,.docx,.jpg,.jpeg,.png,.zip,.txt',
    'helperText' => 'Dapat memilih lebih dari 1 dokumen sekaligus (Maksimal 5 berkas, 5MB per berkas).',
])

<div 
    x-data="fileDropzoneUploader({
        inputId: '{{ $id }}',
        fieldName: '{{ $name }}',
        maxFiles: {{ $maxFiles }},
        maxSizeMb: {{ $maxFileSizeMb }}
    })"
    class="space-y-3"
>
    <!-- Header & Label -->
    <div class="flex items-center justify-between">
        <label for="{{ $id }}" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
            {{ $label }}
            @if($optional)
                <span class="text-slate-400 font-normal lowercase tracking-normal text-[11px]">(opsional, multiple file)</span>
            @else
                <span class="text-rose-500 font-bold">*</span>
            @endif
        </label>
        <span class="text-[11px] font-medium text-slate-400" x-text="fileCountText"></span>
    </div>

    <!-- Dropzone Interactive Box -->
    <label 
        for="{{ $id }}"
        @dragover.prevent="onDragOver()"
        @dragleave.prevent="onDragLeave()"
        @drop.prevent="onDrop($event)"
        :class="{
            'border-emerald-600 bg-emerald-50/40 ring-4 ring-emerald-500/10': isDragging,
            'border-slate-300 hover:border-emerald-500/70 bg-slate-50/60 hover:bg-slate-50': !isDragging
        }"
        class="relative block rounded-2xl border-2 border-dashed p-6 transition-all duration-200 cursor-pointer text-center group"
    >
        <!-- Hidden Native File Input -->
        <input 
            type="file" 
            id="{{ $id }}"
            name="{{ $name }}" 
            multiple
            accept="{{ $acceptedTypes }}"
            class="sr-only"
            x-ref="fileInput"
            @click.stop
            @change="handleFileSelect($event)"
        >

        <div class="flex flex-col items-center justify-center pointer-events-none">
            <div class="w-12 h-12 rounded-2xl bg-emerald-700/10 text-emerald-800 flex items-center justify-center text-2xl mb-2.5 transition-transform group-hover:scale-105 duration-200 shadow-xs">
                <i class="ri-upload-cloud-2-line"></i>
            </div>
            <p class="text-xs sm:text-sm font-bold text-slate-800">
                Tarik &amp; lepas dokumen di sini, atau <span class="text-emerald-700 underline font-semibold">pilih berkas</span>
            </p>
            <p class="text-[11px] text-slate-400 mt-1">
                {{ $helperText }}
            </p>
        </div>
    </label>

    <!-- Error Alert Box -->
    <div 
        x-show="errorMessage !== ''" 
        x-cloak 
        class="p-3 rounded-xl bg-rose-50 border border-rose-200/80 text-xs text-rose-700 flex items-center justify-between"
    >
        <div class="flex items-center gap-2">
            <i class="ri-error-warning-line text-rose-600 text-sm shrink-0"></i>
            <span x-text="errorMessage"></span>
        </div>
        <button 
            type="button" 
            @click="errorMessage = ''" 
            class="text-rose-500 hover:text-rose-700 transition"
        >
            <i class="ri-close-line text-base"></i>
        </button>
    </div>

    <!-- Live File Queue / Chips -->
    <div x-show="hasFiles" x-cloak class="space-y-2 pt-1">
        <div class="flex items-center justify-between text-[11px] font-bold uppercase tracking-wider text-slate-500 px-1">
            <span>Daftar Berkas Terpilih</span>
            <button 
                type="button" 
                @click="clearAllFiles()" 
                class="text-rose-600 hover:text-rose-800 hover:underline font-semibold lowercase tracking-normal"
            >
                Hapus semua
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
            <template x-for="(file, index) in fileList" :key="file.id">
                <div class="flex items-center justify-between gap-3 p-3 rounded-xl border border-slate-200/90 bg-white shadow-xs hover:border-slate-300 transition-colors">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" :class="file.iconBg">
                            <i :class="file.iconClass" class="text-base"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-slate-800 truncate" :title="file.name" x-text="file.name"></p>
                            <span class="text-[10px] font-medium text-slate-400" x-text="file.size"></span>
                        </div>
                    </div>

                    <button 
                        type="button" 
                        @click.stop="removeFile(index)" 
                        class="w-7 h-7 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition-colors shrink-0"
                        title="Hapus berkas ini"
                    >
                        <i class="ri-close-line text-base"></i>
                    </button>
                </div>
            </template>
        </div>
    </div>
</div>

@pushOnce('scripts')
<script>
if (typeof window.fileDropzoneUploader !== 'function') {
    window.fileDropzoneUploader = function(config) {
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
    };
}
</script>
@endPushOnce

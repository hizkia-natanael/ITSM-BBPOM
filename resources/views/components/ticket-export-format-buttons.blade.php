@php
    $statePath = $getStatePath();
    $currentVal = $getState() ?: 'pdf';
@endphp

<div 
    x-data="{ 
        format: '{{ $currentVal }}',
        selectFormat(val) {
            this.format = val;
            if (typeof $wire !== 'undefined') {
                $wire.set('{{ $statePath }}', val);
            }
        }
    }"
    class="w-full bbpom-export-format-wrapper"
>
    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
        Pilihan Format Berkas <span class="text-rose-500">*</span>
    </label>

    <!-- Container Sebelahan Kanan Kiri (Grid 2 Kolom) -->
    <div class="bbpom-export-formats-container">
        <!-- 1. Tombol PDF (Nuansa Merah Tegas & Ikon PDF) -->
        <button
            type="button"
            @click="selectFormat('pdf')"
            :class="{ 'is-active': format === 'pdf' }"
            :style="format === 'pdf' 
                ? 'border: 2px solid #e11d48 !important; background-color: #fff1f2 !important; box-shadow: 0 0 0 3px rgba(225, 29, 72, 0.25) !important;' 
                : 'border: 2px solid #fecdd3 !important; background-color: #ffffff !important;'"
            class="bbpom-format-btn btn-pdf"
        >
            <div 
                class="icon-box"
                :style="format === 'pdf' 
                    ? 'background-color: #e11d48 !important; color: #ffffff !important;' 
                    : 'background-color: #ffe4e6 !important; color: #e11d48 !important;'"
            >
                <i class="ri-file-pdf-2-fill"></i>
            </div>
            <div class="text-content">
                <div class="bbpom-format-title" style="color: #9f1239 !important;">
                    Dokumen PDF
                </div>
                <div class="bbpom-format-desc">
                    Format cetak resmi Kop BBPOM
                </div>
            </div>
            <div 
                class="bbpom-radio-indicator"
                :style="format === 'pdf' 
                    ? 'border-color: #e11d48 !important; background-color: #e11d48 !important;' 
                    : 'border-color: #cbd5e1 !important; background-color: #ffffff !important;'"
            >
                <span x-show="format === 'pdf'" class="bbpom-radio-dot"></span>
            </div>
        </button>

        <!-- 2. Tombol Excel (Nuansa Hijau Tegas & Ikon Excel) -->
        <button
            type="button"
            @click="selectFormat('excel')"
            :class="{ 'is-active': format === 'excel' }"
            :style="format === 'excel' 
                ? 'border: 2px solid #059669 !important; background-color: #ecfdf5 !important; box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.25) !important;' 
                : 'border: 2px solid #a7f3d0 !important; background-color: #ffffff !important;'"
            class="bbpom-format-btn btn-excel"
        >
            <div 
                class="icon-box"
                :style="format === 'excel' 
                    ? 'background-color: #059669 !important; color: #ffffff !important;' 
                    : 'background-color: #d1fae5 !important; color: #059669 !important;'"
            >
                <i class="ri-file-excel-2-fill"></i>
            </div>
            <div class="text-content">
                <div class="bbpom-format-title" style="color: #064e3b !important;">
                    Spreadsheet Excel
                </div>
                <div class="bbpom-format-desc">
                    Format .xlsx Multi-Sheet &amp; Filter
                </div>
            </div>
            <div 
                class="bbpom-radio-indicator"
                :style="format === 'excel' 
                    ? 'border-color: #059669 !important; background-color: #059669 !important;' 
                    : 'border-color: #cbd5e1 !important; background-color: #ffffff !important;'"
            >
                <span x-show="format === 'excel'" class="bbpom-radio-dot"></span>
            </div>
        </button>
    </div>

    <!-- Hidden input to guarantee form submission -->
    <input type="hidden" name="{{ $statePath }}" :value="format">

    <style>
        /* Container Grid 2 Kolom Sebelahan Kanan Kiri */
        .bbpom-export-formats-container {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 0.75rem !important;
            width: 100% !important;
        }

        @media (max-width: 640px) {
            .bbpom-export-formats-container {
                grid-template-columns: 1fr !important;
            }
        }

        /* Base Button */
        .bbpom-format-btn {
            position: relative !important;
            display: flex !important;
            align-items: center !important;
            gap: 0.75rem !important;
            padding: 0.75rem 0.95rem !important;
            border-radius: 0.75rem !important;
            text-align: left !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
            cursor: pointer !important;
            user-select: none !important;
            outline: none !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }

        .bbpom-format-btn .text-content {
            min-width: 0 !important;
            flex: 1 1 0% !important;
        }

        .bbpom-format-title {
            font-size: 0.875rem !important;
            font-weight: 700 !important;
            line-height: 1.25 !important;
        }

        .bbpom-format-desc {
            font-size: 0.72rem !important;
            color: #64748b !important;
            margin-top: 0.15rem !important;
            line-height: 1.25 !important;
        }

        .bbpom-format-btn .icon-box {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 2.35rem !important;
            height: 2.35rem !important;
            border-radius: 0.5rem !important;
            font-size: 1.35rem !important;
            flex-shrink: 0 !important;
            transition: all 0.2s ease !important;
        }

        /* PDF Hover & Active Effects */
        .bbpom-format-btn.btn-pdf:hover {
            border-color: #f43f5e !important;
            background-color: #fff1f2 !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 14px -2px rgba(225, 29, 72, 0.22) !important;
        }
        .bbpom-format-btn.btn-pdf:hover .icon-box {
            background-color: #f43f5e !important;
            color: #ffffff !important;
        }

        /* Excel Hover & Active Effects */
        .bbpom-format-btn.btn-excel:hover {
            border-color: #10b981 !important;
            background-color: #ecfdf5 !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 14px -2px rgba(5, 150, 105, 0.22) !important;
        }
        .bbpom-format-btn.btn-excel:hover .icon-box {
            background-color: #10b981 !important;
            color: #ffffff !important;
        }

        /* Radio Indicator Pill */
        .bbpom-radio-indicator {
            width: 1.25rem !important;
            height: 1.25rem !important;
            border-radius: 9999px !important;
            border-width: 2px !important;
            border-style: solid !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin-left: auto !important;
            flex-shrink: 0 !important;
            transition: all 0.2s ease !important;
        }
        .bbpom-radio-dot {
            width: 0.42rem !important;
            height: 0.42rem !important;
            border-radius: 9999px !important;
            background-color: #ffffff !important;
            display: inline-block !important;
        }
    </style>
</div>

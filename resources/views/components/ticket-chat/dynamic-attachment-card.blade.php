<div class="bbpom-doc-card rounded-xl p-2.5 sm:p-3 shadow-2xs flex items-center justify-between gap-3 max-w-sm transition-all hover:shadow-xs group/att"
     :style="'display: flex !important; align-items: center !important; justify-content: space-between !important; gap: 0.75rem !important; width: 100% !important; max-width: 24rem !important; background-color: ' + att.document_card_bg_hex + ' !important; border: 1px solid ' + att.document_card_border_hex + ' !important; border-radius: 0.75rem !important; padding: 0.625rem 0.75rem !important; box-sizing: border-box !important;'">
    <a :href="att.download_url" class="flex items-center gap-2.5 min-w-0 flex-1 text-left no-underline group-hover/att:opacity-95" 
       style="display: flex !important; align-items: center !important; gap: 0.625rem !important; min-width: 0 !important; flex: 1 1 0% !important; text-decoration: none !important; color: inherit !important;" 
       :title="'Unduh ' + att.file_name">
        <div class="w-10 h-10 rounded-xl flex flex-col items-center justify-center shrink-0 shadow-2xs leading-none p-1 text-center"
             :style="'width: 2.5rem !important; height: 2.5rem !important; border-radius: 0.75rem !important; background-color: ' + att.document_hex_color + ' !important; color: #ffffff !important; display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important; flex-shrink: 0 !important; box-shadow: 0 1px 3px rgba(0,0,0,0.12) !important;'">
            <i :class="att.pure_icon_class" style="font-size: 1rem !important; color: #ffffff !important; line-height: 1 !important;"></i>
            <span style="font-size: 8.5px !important; font-weight: 800 !important; color: #ffffff !important; text-transform: uppercase !important; margin-top: 2px !important; line-height: 1 !important;" x-text="att.extension_upper"></span>
        </div>
        <div class="min-w-0 flex-1" style="min-width: 0 !important; flex: 1 1 0% !important; overflow: hidden !important;">
            <p class="bbpom-doc-name text-xs sm:text-[13px] font-bold text-slate-800 group-hover/att:text-emerald-700 truncate block leading-tight transition-colors" 
               style="font-size: 0.8125rem !important; font-weight: 700 !important; color: #1e293b !important; margin: 0 !important; line-height: 1.25 !important; white-space: nowrap !important; overflow: hidden !important; text-overflow: ellipsis !important;" 
               :title="att.file_name" x-text="att.file_name"></p>
            <div class="flex items-center gap-1.5 mt-1 flex-wrap text-[10.5px] text-slate-500 font-medium leading-none" 
                 style="display: flex !important; align-items: center !important; gap: 0.375rem !important; font-size: 10.5px !important; color: #64748b !important; margin-top: 0.25rem !important;">
                <span x-text="att.formatted_size"></span>
                <span style="color: #cbd5e1;">•</span>
                <span style="text-transform: uppercase; font-weight: 600; color: #475569;" x-text="att.extension_upper"></span>
                <template x-if="att.is_compressed">
                    <span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded-full border border-emerald-200/60" 
                          style="display: inline-flex !important; align-items: center !important; gap: 2px !important; font-size: 10px !important; font-weight: 700 !important; color: #047857 !important; background-color: #ecfdf5 !important; border: 1px solid #a7f3d0 !important; padding: 1px 6px !important; border-radius: 9999px !important;"
                          :title="'Ukuran asli: ' + att.formatted_original_size">
                        <i class="ri-flashlight-line"></i>
                        <span x-text="'-' + att.saving_percentage + '%'"></span>
                    </span>
                </template>
                <template x-if="!att.is_compressed">
                    <span class="inline-flex items-center gap-0.5 text-[10px] font-semibold text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded-full border border-slate-200" 
                          style="display: inline-flex !important; align-items: center !important; gap: 2px !important; font-size: 10px !important; font-weight: 600 !important; color: #475569 !important; background-color: #f1f5f9 !important; border: 1px solid #e2e8f0 !important; padding: 1px 6px !important; border-radius: 9999px !important;">
                        <i class="ri-hd-line" style="color: #94a3b8;"></i>
                        <span>Asli</span>
                    </span>
                </template>
            </div>
        </div>
    </a>
    <a :href="att.download_url"
       class="bbpom-chat-download-btn shrink-0 w-9 h-9 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white flex items-center justify-center transition-all shadow-xs hover:scale-105 active:scale-95"
       style="width: 2.25rem !important; height: 2.25rem !important; border-radius: 9999px !important; background-color: #059669 !important; color: #ffffff !important; display: flex !important; align-items: center !important; justify-content: center !important; flex-shrink: 0 !important; margin-left: 0.5rem !important; text-decoration: none !important; box-shadow: 0 2px 5px -1px rgba(5, 150, 105, 0.35) !important;"
       :title="'Unduh ' + att.file_name">
        <i class="ri-download-2-line text-base" style="font-size: 1rem !important; color: #ffffff !important; line-height: 1 !important;"></i>
    </a>
</div>

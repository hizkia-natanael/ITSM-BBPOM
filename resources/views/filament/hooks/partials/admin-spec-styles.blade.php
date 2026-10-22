<style>
[class^="ri-"],[class*=" ri-"]{font-family: 'remixicon' !important;font-style: normal;-webkit-font-smoothing: antialiased;-moz-osx-font-smoothing: grayscale;display: inline-block;line-height: 1;}
.bbpom-spec-card-container{background: #ffffff !important;border: 1px solid #e2e8f0 !important;border-radius: 1rem !important;padding: 1.5rem !important;box-shadow: 0 1px 3px 0 rgba(0,0,0,0.04) !important;display: flex !important;flex-direction: column !important;gap: 1.25rem !important;margin-top: 0.25rem !important;margin-bottom: 0.25rem !important;box-sizing: border-box !important;}
.bbpom-spec-header-compact{display: flex !important;align-items: center !important;justify-content: space-between !important;gap: 0.75rem !important;flex-wrap: wrap !important;padding-bottom: 0.85rem !important;border-bottom: 1px solid #f1f5f9 !important;}
.bbpom-spec-header-row{display: flex !important;align-items: center !important;justify-content: space-between !important;gap: 1rem !important;flex-wrap: wrap !important;padding-bottom: 1.15rem !important;border-bottom: 1px solid #f1f5f9 !important;}
.bbpom-spec-header-left{display: flex !important;align-items: center !important;gap: 0.875rem !important;}
.bbpom-spec-icon-box{width: 2.75rem !important;height: 2.75rem !important;border-radius: 0.75rem !important;background: rgba(5,150,105,0.1) !important;color: #047857 !important;display: flex !important;align-items: center !important;justify-content: center !important;font-size: 1.35rem !important;flex-shrink: 0 !important;}
.bbpom-spec-title-group{display: flex !important;flex-direction: column !important;gap: 0.15rem !important;}
.bbpom-spec-badge-label{font-size: 11px !important;font-weight: 700 !important;color: #047857 !important;text-transform: uppercase !important;letter-spacing: 0.05em !important;line-height: 1 !important;}
.bbpom-spec-service-title{font-size: 1.2rem !important;font-weight: 800 !important;color: #0f172a !important;margin: 0 !important;line-height: 1.25 !important;}
.bbpom-spec-subcat-badge{display: inline-flex !important;align-items: center !important;padding: 0.35rem 0.85rem !important;border-radius: 9999px !important;font-size: 12px !important;font-weight: 700 !important;background: #ecfdf5 !important;color: #065f46 !important;border: 1px solid rgba(16,185,129,0.3) !important;}
.bbpom-spec-meta-grid{display: grid !important;grid-template-columns: repeat(auto-fit,minmax(220px,1fr)) !important;gap: 1rem !important;}
.bbpom-spec-meta-card{background: #f8fafc !important;border: 1px solid #f1f5f9 !important;border-radius: 0.75rem !important;padding: 0.85rem 1rem !important;display: flex !important;flex-direction: column !important;gap: 0.25rem !important;}
.bbpom-spec-meta-label{font-size: 11px !important;font-weight: 600 !important;color: #64748b !important;}.bbpom-spec-meta-value{font-size: 13.5px !important;font-weight: 700 !important;color: #0f172a !important;}
.bbpom-spec-fields-section{display: flex !important;flex-direction: column !important;gap: 0.75rem !important;}
.bbpom-spec-section-title{font-size: 11.5px !important;font-weight: 700 !important;color: #475569 !important;text-transform: uppercase !important;letter-spacing: 0.05em !important;display: flex !important;align-items: center !important;gap: 0.45rem !important;}
.bbpom-spec-section-title i{color: #047857 !important;font-size: 14px !important;}
.bbpom-spec-fields-grid{display: grid !important;grid-template-columns: repeat(auto-fit,minmax(260px,1fr)) !important;gap: 0.85rem !important;}
.bbpom-spec-field-card{background: #ffffff !important;border: 1px solid #e2e8f0 !important;border-radius: 0.875rem !important;padding: 0.95rem 1rem !important;box-shadow: 0 1px 2px 0 rgba(0,0,0,0.02) !important;transition: border-color 0.15s ease !important;display: flex !important;flex-direction: column !important;gap: 0.5rem !important;}
.bbpom-spec-field-card:hover{border-color: #6ee7b7 !important;}.bbpom-spec-field-header{display: flex !important;align-items: center !important;gap: 0.45rem !important;}
.bbpom-spec-field-header i{color: #94a3b8 !important;font-size: 14px !important;}
.bbpom-spec-field-header span{font-size: 11px !important;font-weight: 700 !important;color: #64748b !important;text-transform: uppercase !important;letter-spacing: 0.05em !important;overflow: hidden !important;text-overflow: ellipsis !important;white-space: nowrap !important;}
.bbpom-spec-field-value{background: #f8fafc !important;border: 1px solid #f1f5f9 !important;border-radius: 0.5rem !important;padding: 0.5rem 0.75rem !important;font-size: 12.5px !important;font-weight: 600 !important;font-family: ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,monospace !important;color: #0f172a !important;word-break: break-all !important;}
.bbpom-spec-long-section{display: flex !important;flex-direction: column !important;gap: 0.75rem !important;padding-top: 0.5rem !important;border-top: 1px solid #f1f5f9 !important;}
.bbpom-spec-long-card{background: #f8fafc !important;border: 1px solid #e2e8f0 !important;border-radius: 0.75rem !important;padding: 1rem !important;display: flex !important;flex-direction: column !important;gap: 0.5rem !important;}
.bbpom-spec-long-label{font-size: 12px !important;font-weight: 700 !important;color: #334155 !important;display: flex !important;align-items: center !important;gap: 0.4rem !important;}
.bbpom-spec-long-label i{color: #047857 !important;font-size: 14px !important;}
.bbpom-spec-long-content{background: #ffffff !important;border: 1px solid #e2e8f0 !important;border-radius: 0.5rem !important;padding: 0.85rem !important;font-size: 13px !important;color: #1e293b !important;line-height: 1.6 !important;white-space: pre-wrap !important;}
.bbpom-sr-only{position: absolute !important;width: 1px !important;height: 1px !important;padding: 0 !important;margin: -1px !important;overflow: hidden !important;clip: rect(0,0,0,0) !important;white-space: nowrap !important;border: 0 !important;}
@keyframes bbpomHighlightPulse{0%{background-color: rgba(5,150,105,0.24);}30%{background-color: rgba(5,150,105,0.28);}100%{background-color: transparent;}}@keyframes bbpomSpin{from{transform: rotate(0deg);}
to{transform: rotate(360deg);}}.bbpom-spinner-rotate{animation: bbpomSpin 0.85s linear infinite !important;}
.bbpom-highlight-pulse{animation: bbpomHighlightPulse 1.0s ease-out forwards !important;border-radius: 1rem !important;}.bbpom-msg-row{position: relative;transition: background-color 0.15s ease;}
.bbpom-reply-trigger{opacity: 0 !important;visibility: hidden !important;pointer-events: none !important;transform: scale(0.85) !important;transition: opacity 0.2s cubic-bezier(0.4,0,0.2,1),transform 0.2s cubic-bezier(0.4,0,0.2,1),background-color 0.15s ease,color 0.15s ease !important;border-radius: 9999px !important;width: 2rem !important;height: 2rem !important;display: inline-flex !important;align-items: center !important;justify-content: center !important;border: none !important;cursor: pointer !important;background: transparent !important;color: #94a3b8 !important;flex-shrink: 0 !important;align-self: center !important;}
.bbpom-msg-row:hover .bbpom-reply-trigger,.bbpom-msg-row:focus-within .bbpom-reply-trigger{opacity: 1 !important;visibility: visible !important;pointer-events: auto !important;transform: scale(1) !important;}
.bbpom-reply-trigger:hover{background-color: rgba(226,232,240,0.85) !important;color: #047857 !important;transform: scale(1.12) !important;}.bbpom-reply-trigger:active{transform: scale(0.95) !important;}
.bbpom-quote-card{transition: filter 0.15s ease,background-color 0.15s ease !important;cursor: pointer !important;user-select: none !important;border-left: 3px solid #059669 !important;}
.bbpom-quote-card:hover{filter: brightness(0.94) !important;}
.bbpom-doc-card{transition: transform 0.18s cubic-bezier(0.4,0,0.2,1),box-shadow 0.18s cubic-bezier(0.4,0,0.2,1),border-color 0.18s ease !important;cursor: default !important;}
.bbpom-doc-card:hover{transform: translateY(-1.5px) !important;box-shadow: 0 4px 14px -2px rgba(15,23,42,0.08) !important;}.bbpom-doc-card:hover .bbpom-doc-name{color: #047857 !important;}
.bbpom-chat-download-btn{transition: transform 0.15s cubic-bezier(0.4,0,0.2,1),box-shadow 0.15s cubic-bezier(0.4,0,0.2,1),background-color 0.15s ease !important;}
.bbpom-chat-download-btn:hover{transform: scale(1.08) !important;background-color: #047857 !important;box-shadow: 0 4px 12px -1px rgba(5,150,105,0.45) !important;}
.bbpom-chat-download-btn:active{transform: scale(0.95) !important;}.bbpom-clip-btn{transition: transform 0.15s cubic-bezier(0.4,0,0.2,1),background-color 0.15s ease,border-color 0.15s ease,color 0.15s ease !important;}
.bbpom-clip-btn:hover{background-color: #f1f5f9 !important;border-color: #cbd5e1 !important;color: #047857 !important;transform: scale(1.05) !important;}.bbpom-clip-btn:active{transform: scale(0.95) !important;}
.bbpom-send-btn{transition: transform 0.15s cubic-bezier(0.4,0,0.2,1),background-color 0.15s ease,box-shadow 0.15s ease !important;}
.bbpom-send-btn:hover:not(:disabled){background-color: #047857 !important;transform: scale(1.06) !important;box-shadow: 0 4px 12px -1px rgba(5,150,105,0.4) !important;}
.bbpom-send-btn:active:not(:disabled){transform: scale(0.95) !important;}[x-cloak]{display: none !important;}
.bbpom-file-preview-card{display: flex;flex-direction: column;gap: 0.75rem;box-sizing: border-box;width: 100%;border-radius: 1rem;padding: 0.75rem 1rem;margin-bottom: 0.75rem;box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);transition: all 0.2s ease;}
.bbpom-file-preview-top-row{display: flex;align-items: center;justify-content: space-between;gap: 0.75rem;width: 100%;}.bbpom-file-preview-info{display: flex;align-items: center;gap: 0.75rem;min-width: 0;flex: 1 1 auto;}
.bbpom-file-preview-actions{display: flex;align-items: center;gap: 0.5rem;flex-shrink: 0;margin-left: auto;}
.bbpom-file-chips-container{display: flex;align-items: center;gap: 0.375rem;flex-wrap: wrap;width: 100%;padding-top: 0.35rem;border-top: 1px dashed rgba(203,213,225,0.7);}
.bbpom-file-chip{display: inline-flex;align-items: center;gap: 0.35rem;padding: 0.2rem 0.5rem;border-radius: 0.375rem;background-color: #ffffff;border: 1px solid #e2e8f0;font-size: 11px;font-weight: 500;color: #334155;max-width: 220px;min-width: 0;line-height: 1.25;white-space: nowrap;box-shadow: 0 1px 2px rgba(0,0,0,0.03);}
.bbpom-file-chip-name{overflow: hidden;text-overflow: ellipsis;white-space: nowrap;min-width: 0;flex: 1 1 auto;}
.bbpom-file-chip-size{color: #64748b;font-size: 9.5px;font-family: ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,monospace;white-space: nowrap !important;flex-shrink: 0 !important;display: inline-block;line-height: 1;margin-left: 0.25rem;background-color: #f1f5f9;padding: 0.125rem 0.35rem;border-radius: 0.25rem;border: 1px solid #e2e8f0;}
.bbpom-segmented-switcher{display: inline-flex;align-items: center;padding: 3px;border-radius: 9999px;background-color: #ffffff;border: 1px solid #e2e8f0;font-size: 11px;font-weight: 600;gap: 2px;box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);}
.bbpom-mode-pill{display: inline-flex;align-items: center;justify-content: center;gap: 0.375rem;padding: 0.35rem 0.75rem;border-radius: 9999px;font-size: 11px;font-weight: 600;border: none;cursor: pointer;line-height: 1.25;white-space: nowrap;transition: all 0.15s ease;font-family: inherit;}
.bbpom-mode-pill:hover{filter: brightness(0.96);}
.bbpom-clear-file-btn{width: 1.875rem;height: 1.875rem;border-radius: 9999px;display: inline-flex;align-items: center;justify-content: center;cursor: pointer;background-color: #ffffff;border: 1px solid #e2e8f0;color: #64748b;padding: 0;flex-shrink: 0;box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);transition: all 0.15s ease;}
.bbpom-clear-file-btn:hover{background-color: #fee2e2;border-color: #fca5a5;color: #b91c1c;transform: scale(1.08);}
.bbpom-action-circle-btn{transition: background-color 0.15s ease,color 0.15s ease,transform 0.15s ease !important;}.bbpom-action-circle-btn:hover{transform: scale(1.1) !important;}
.bbpom-chat-textarea{transition: border-color 0.15s ease,box-shadow 0.15s ease !important;}
.bbpom-chat-textarea:focus{border-color: #059669 !important;box-shadow: 0 0 0 3px rgba(5,150,105,0.15) !important;outline: none !important;}
</style>

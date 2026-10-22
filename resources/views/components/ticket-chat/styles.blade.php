<style>
[x-cloak]{display: none !important;}@keyframes bbpomSpin{from{transform: rotate(0deg);}to{transform: rotate(360deg);}}.bbpom-spinner-rotate{animation: bbpomSpin 0.85s linear infinite !important;}
@keyframes bbpomHighlightPulse{0%{background-color: rgba(5,150,105,0.24);}30%{background-color: rgba(5,150,105,0.28);}100%{background-color: transparent;}}
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
.bbpom-send-btn:active:not(:disabled){transform: scale(0.95) !important;}
.bbpom-file-preview-card{display: flex;flex-direction: column;gap: 0.75rem;box-sizing: border-box;width: 100%;border-radius: 1rem;padding: 0.75rem 1rem;margin-bottom: 0.75rem;box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);transition: all 0.2s ease;}
@media (min-width: 640px){.bbpom-file-preview-card{flex-direction: column;}}.bbpom-file-preview-top-row{display: flex;align-items: center;justify-content: space-between;gap: 0.75rem;width: 100%;}
.bbpom-file-preview-info{display: flex;align-items: center;gap: 0.75rem;min-width: 0;flex: 1 1 auto;}.bbpom-file-preview-actions{display: flex;align-items: center;gap: 0.5rem;flex-shrink: 0;margin-left: auto;}
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

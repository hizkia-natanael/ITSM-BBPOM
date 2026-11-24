@if (session('success'))
    <div style="padding: 0.875rem 1rem; border-radius: 0.75rem; background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; display: flex; align-items: center; gap: 0.75rem; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.03);">
        <i class="ri-checkbox-circle-fill" style="font-size: 1.25rem; color: #059669; flex-shrink: 0;"></i>
        <div style="font-size: 0.8125rem; font-weight: 600; line-height: 1.25;">
            {{ session('success') }}
        </div>
    </div>
@endif

@if (session('error'))
    <div style="padding: 0.875rem 1rem; border-radius: 0.75rem; background: #fff1f2; border: 1px solid #fecdd3; color: #9f1239; display: flex; align-items: center; gap: 0.75rem; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.03);">
        <i class="ri-error-warning-fill" style="font-size: 1.25rem; color: #e11d48; flex-shrink: 0;"></i>
        <div style="font-size: 0.8125rem; font-weight: 600; line-height: 1.25;">
            {{ session('error') }}
        </div>
    </div>
@endif

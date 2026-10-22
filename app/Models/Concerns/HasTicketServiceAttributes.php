<?php

namespace App\Models\Concerns;

trait HasTicketServiceAttributes
{
    public function getSubjectAttribute($value): string
    {
        return preg_replace('/^\[[^\]]+\]\s*/', '', (string) $value);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'diproses' => 'Diproses',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
            default => ucfirst((string) $this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'diproses' => 'info',
            'selesai' => 'success',
            'ditolak' => 'danger',
            default => 'gray',
        };
    }

    public function getServiceKeyResolvedAttribute(): ?string
    {
        if (!empty($this->service_key)) {
            return $this->service_key;
        }

        $relations = [
            'bsreDetail' => 'sertifikat_bse',
            'zoomDetail' => 'zoom',
            'kegiatanDetail' => 'formulir_kegiatan',
            'windowsDetail' => 'install_windows',
            'officeDetail' => 'lisensi_office',
            'lainnyaDetail' => 'lainnya',
        ];

        foreach ($relations as $rel => $key) {
            if ($this->relationLoaded($rel) ? $this->{$rel} : $this->{$rel}()->exists()) {
                return $key;
            }
        }

        if ($this->category) {
            $slug = str_replace('-', '_', $this->category->slug);
            return $slug === 'sertifikat_bsre' ? 'sertifikat_bse' : $slug;
        }

        return null;
    }

    public function getServiceNameAttribute(): string
    {
        $resolvedKey = $this->service_key_resolved;
        if ($resolvedKey) {
            $services = config('services_gateway.services', []);
            if (isset($services[$resolvedKey]['name'])) {
                return $services[$resolvedKey]['name'];
            }
        }

        return $this->category?->name ?? 'Layanan TIK';
    }

    public function hasSubDetail(): bool
    {
        return (bool) ($this->bsreDetail || $this->zoomDetail || $this->kegiatanDetail || $this->windowsDetail || $this->officeDetail || $this->lainnyaDetail);
    }

    public function getSubcategoryLabelAttribute(): ?string
    {
        if ($this->bsreDetail) return $this->bsreDetail->sub_category_label;
        if ($this->zoomDetail) return $this->zoomDetail->sub_category_label;
        if ($this->kegiatanDetail) return $this->kegiatanDetail->sub_category_label;
        if ($this->windowsDetail) return $this->windowsDetail->sub_category_label;
        if ($this->officeDetail) return $this->officeDetail->sub_category_label;
        if ($this->lainnyaDetail) return 'Layanan Umum';

        if ($this->subcategory) {
            $services = config('services_gateway.services', []);
            $resolvedKey = $this->service_key_resolved;
            if ($resolvedKey && isset($services[$resolvedKey]['subcategories'][$this->subcategory])) {
                return $services[$resolvedKey]['subcategories'][$this->subcategory];
            }
            return ucwords(str_replace('_', ' ', $this->subcategory));
        }

        return null;
    }

    public function getServiceIconAttribute(): string
    {
        $key = $this->service_key_resolved;
        $services = config('services_gateway.services', []);
        return ($key && isset($services[$key]['icon'])) ? $services[$key]['icon'] : 'ri-file-list-3-line';
    }

    public function getServiceBadgeAttribute(): ?string
    {
        $key = $this->service_key_resolved;
        $services = config('services_gateway.services', []);
        return ($key && isset($services[$key]['badge'])) ? $services[$key]['badge'] : 'Katalog Layanan TIK';
    }
}

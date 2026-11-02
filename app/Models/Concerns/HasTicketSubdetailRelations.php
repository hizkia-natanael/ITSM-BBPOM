<?php

namespace App\Models\Concerns;

use App\Models\TicketBsreDetail;
use App\Models\TicketKegiatanDetail;
use App\Models\TicketLainnyaDetail;
use App\Models\TicketOfficeDetail;
use App\Models\TicketWindowsDetail;
use App\Models\TicketZoomDetail;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasTicketSubdetailRelations
{
    public function bsreDetail(): HasOne { return $this->hasOne(TicketBsreDetail::class, 'ticket_id'); }
    public function zoomDetail(): HasOne { return $this->hasOne(TicketZoomDetail::class, 'ticket_id'); }
    public function kegiatanDetail(): HasOne { return $this->hasOne(TicketKegiatanDetail::class, 'ticket_id'); }
    public function windowsDetail(): HasOne { return $this->hasOne(TicketWindowsDetail::class, 'ticket_id'); }
    public function officeDetail(): HasOne { return $this->hasOne(TicketOfficeDetail::class, 'ticket_id'); }
    public function lainnyaDetail(): HasOne { return $this->hasOne(TicketLainnyaDetail::class, 'ticket_id'); }
}

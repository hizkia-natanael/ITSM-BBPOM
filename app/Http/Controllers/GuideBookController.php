<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class GuideBookController extends Controller
{
    /**
     * Display the creative interactive guide book for BBPOM Semarang ITSM.
     */
    public function index(): View
    {
        return view('guide-book.index', [
            'pageTitle' => 'Buku Panduan Resmi ITSM BBPOM di Semarang',
            'edition' => 'Edisi Standar Operasional Prosedur 2026',
        ]);
    }
}

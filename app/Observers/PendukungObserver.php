<?php

namespace App\Observers;

use App\Models\Pendukung;
use App\Models\PendukungLog;
use Illuminate\Support\Facades\Auth;

class PendukungObserver
{
    /**
     * 🔹 SAAT DATA DITAMBAH
     */
    public function created(Pendukung $pendukung): void
    {
        PendukungLog::create([
            'admin_id'  => Auth::id(),
            'aksi'      => 'tambah',
            'perubahan' => 1,
            'tanggal'   => now()->toDateString(),
        ]);
    }

    /**
     * 🔹 SAAT DATA DIHAPUS
     */
    public function deleted(Pendukung $pendukung): void
    {
        PendukungLog::create([
            'admin_id'  => Auth::id(),
            'aksi'      => 'hapus',
            'perubahan' => -1,
            'tanggal'   => now()->toDateString(),
        ]);
    }
}

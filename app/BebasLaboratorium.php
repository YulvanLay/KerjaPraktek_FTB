<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BebasLaboratorium extends Model
{
    protected $table = 'bebas_laboratoriums';

    protected $fillable = [
        'kode_pelanggan',
        'laboratorium_id',
        'form_url',
        'ck_bebas_pinjaman',
        'ck_buka_bakteri',
        'ck_bayar_bahan',
        'ck_alat_bersih',
        'ck_alat_ganti',
        'acc_laboran',
        'acc_kalab',
        'tanggal_acc_kalab',
    ];

    /**
     * Relasi ke Pelanggan
     */
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'kode_pelanggan', 'kode_pelanggan');
    }

    public function laboratorium()
    {
        return $this->belongsTo(Laboratorium::class, 'laboratorium_id', 'id');
    }


    /**
     * Get laboran dari laboratorium
     */
    public function laboran()
    {
        return $this->belongsTo(Laboran::class, 'laborans_kode_laboran', 'kode_laboran');
    }

    public function kalab()
    {
        return $this->belongsTo(Pejabat::class);
    }


    public function periode()
    {
        return $this->belongsTo(
            Periode::class,
            'periode_id_periode',
            'id_periode'
        );
    }
    /**
     * Check if all laboran checklist is complete
     */
    public function isLaboranChecklistComplete()
    {
        return $this->checklists()->where('laboran_checked', true)->count() === 5;
    }

    /**
     * Check if all kalab checklist is complete
     */
    public function isKalabChecklistComplete()
    {
        return $this->checklists()->where('kalab_checked', true)->count() === 5;
    }
}

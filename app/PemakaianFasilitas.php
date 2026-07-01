<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PemakaianFasilitas extends Model
{
    protected $table = 'pemakaian_fasilitas';
    protected $primaryKey = 'no_transaksi';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'no_transaksi',
        'tanggal',
        'kode_laboran',
        'kode_keperluan',
        'kode_pelanggan',
        'periode_id',
        'acc_laboran',
        'acc_kalab',
        'acc_koor',
        'status_verifikasi',
        'status_kembali',
    ];

    public function laboran()
    {
        return $this->belongsTo('App\Laboran', 'kode_laboran');
    }

    public function keperluan()
    {
        return $this->belongsTo('App\Keperluan', 'kode_keperluan');
    }

    public function pelanggan()
    {
        return $this->belongsTo('App\Pelanggan', 'kode_pelanggan');
    }

    public function periode()
    {
        return $this->belongsTo('App\Periode', 'periode_id');
    }

    public function details()
    {
        return $this->hasMany('App\DetailPemakaianFasilitas', 'no_transaksi');
    }
}
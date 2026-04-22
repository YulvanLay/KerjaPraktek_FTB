<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PeminjamanAlat extends Model
{
    protected $table = 'peminjaman_alats';
    protected $primaryKey = 'no_transaksi';
    public $incrementing = false;
    protected $fillable = ['no_transaksi', 'tanggal_pinjam', 'kode_laboran', 'kode_keperluan', 'kode_pelanggan', 'periode_id'];
    public $timestamps = false;

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
        return $this->hasMany('App\DetailPeminjamanAlat', 'no_transaksi');
    }
}

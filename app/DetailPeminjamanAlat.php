<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetailPeminjamanAlat extends Model
{
    protected $table = 'detail_peminjaman_alats';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'no_transaksi', 'kode_alat', 'jumlah_usulan','jumlah'];
    public $timestamps = false;

    public function alat()
    {
        return $this->belongsTo('App\AlatLab', 'kode_alat');
    }

    public function peminjamanAlat()
    {
        return $this->belongsTo('App\PeminjamanAlat', 'no_transaksi');
    }

    public function detailKembali()
    {
        return $this->hasMany('App\DetailPengembalianAlat', 'id_detail_pinjam');
    }
    public function laboran()
    {
        return $this->belongsTo('App\Laboran', 'kode_laboran');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetailPeminjamanInventaris extends Model
{
    protected $table = 'detail_peminjaman_inventaris';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'no_transaksi',
        'kode_inventaris',
        'jumlah_usulan',
        'jumlah',
        'kembali',
        'kode_laboran',
    ];

    public function inventaris()
    {
        return $this->belongsTo('App\InventarisLab', 'kode_inventaris');
    }

    public function peminjamanInventaris()
    {
        return $this->belongsTo('App\PeminjamanInventaris', 'no_transaksi');
    }

    public function laboran()
    {
        return $this->belongsTo('App\Laboran', 'kode_laboran');
    }

    public function detailKembali()
    {
        return $this->hasMany('App\DetailPengembalianInventaris', 'id_detail_pinjam');
    }
}
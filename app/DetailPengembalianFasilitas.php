<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetailPengembalianFasilitas extends Model
{
    protected $table = 'detail_pengembalian_fasilitas';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'id_detail_pemakaian', 'tanggal_kembali', 'jumlah', 'kondisi'];
    public $timestamps = false;

    public function detailPemakaian()
    {
        return $this->belongsTo('App\DetailPemakaianFasilitas', 'id_detail_pemakaian');
    }
}
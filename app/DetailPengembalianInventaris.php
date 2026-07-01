<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetailPengembalianInventaris extends Model
{
    protected $table = 'detail_pengembalian_inventaris';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'id_detail_pinjam', 'tanggal_kembali', 'jumlah', 'kondisi'];
    public $timestamps = false;

    public function detailPinjam()
    {
        return $this->belongsTo('App\DetailPeminjamanInventaris', 'id_detail_pinjam');
    }
}
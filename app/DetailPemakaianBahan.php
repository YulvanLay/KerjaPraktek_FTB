<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetailPemakaianBahan extends Model
{
    protected $table = 'detail_pemakaian_bahans';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'no_transaksi', 'kode_bahan', 'jumlah_usulan','jumlah'];
    public $timestamps = false;

    public function bahan()
    {
        return $this->belongsTo('App\BahanLab', 'kode_bahan');
    }

    public function pemakaianBahan()
    {
        return $this->belongsTo('App\PemakaianBahan', 'no_transaksi');
    }
}

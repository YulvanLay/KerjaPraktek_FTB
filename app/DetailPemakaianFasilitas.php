<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetailPemakaianFasilitas extends Model
{
    protected $table = 'detail_pemakaian_fasilitas';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'no_transaksi',
        'kode_fasilitas',
        'jumlah_usulan',
        'jumlah',
        'kembali',
        'kode_laboran',
    ];

    public function fasilitas()
    {
        return $this->belongsTo('App\FasilitasLab', 'kode_fasilitas');
    }

    public function pemakaianFasilitas()
    {
        return $this->belongsTo('App\PemakaianFasilitas', 'no_transaksi');
    }

    public function laboran()
    {
        return $this->belongsTo('App\Laboran', 'kode_laboran');
    }

    public function detailKembali()
    {
        return $this->hasMany('App\DetailPengembalianFasilitas', 'id_detail_pemakaian');
    }
}
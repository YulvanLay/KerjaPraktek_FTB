<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BahanLab extends Model
{
	protected $table = 'bahan_labs';
    protected $primaryKey = 'kode_bahan';
    public $incrementing = false;
    protected $fillable = ['kode_bahan', 'nama_bahan', 'kode_sinta', 'kode_jenis', 'harga_bahan', 'stok', 'satuan', 'minimum_stok', 'kode_laboran', 'notif'];
    public $timestamps = false;

    public function jenis()
    {
        return $this->belongsTo('App\JenisBahan', 'kode_jenis');
    }

    public function detailPemakaianBahans()
    {
        return $this->hasMany('App\DetailPemakaianBahan', 'kode_bahan');
    }
    public function merekBahans()
    {
        return $this->belongsTo('App\merekBahan', 'kode_merek');
    }

    public function detailPenerimaanBahans()
    {
        return $this->hasMany('App\DetailPenerimaanBahan', 'kode_bahan');
    }

    public function laboran()
    {
        return $this->belongsTo('App\Laboran', 'kode_laboran');
    }
}

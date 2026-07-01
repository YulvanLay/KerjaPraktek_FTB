<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FasilitasLab extends Model
{
    protected $table = 'fasilitas_labs';
    protected $primaryKey = 'kode_fasilitas';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'kode_fasilitas',
        'nama_fasilitas',
        'lokasi',
        'stok',
        'kode_laboratorium',
    ];

    public function laboratorium()
    {
        return $this->belongsTo('App\Laboratorium', 'kode_laboratorium');
    }

    public function detailPemakaian()
    {
        return $this->hasMany('App\DetailPemakaianFasilitas', 'kode_fasilitas');
    }
}
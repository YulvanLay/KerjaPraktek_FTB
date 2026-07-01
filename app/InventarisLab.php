<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InventarisLab extends Model
{
    protected $table = 'inventaris_labs';
    protected $primaryKey = 'kode_inventaris';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kode_inventaris',
        'nama_inventaris',
        'kode_merek',
        'tipe',
        'jumlah',
        'satuan',
        'harga_satuan',
        'mata_uang',
        'tahun_pembelian',
        'kode_supplier',
        'kode_sumber_dana',
        'no_inventaris',
        'ruangan',
        'keterangan',
        'kode_laboratorium',
    ];

    public function merek()
    {
        return $this->belongsTo('App\MerekInventaris', 'kode_merek');
    }

    public function supplier()
    {
        return $this->belongsTo('App\SupplierInventaris', 'kode_supplier');
    }

    public function sumberDana()
    {
        return $this->belongsTo('App\SumberDana', 'kode_sumber_dana');
    }

    public function laboratorium()
    {
        return $this->belongsTo('App\Laboratorium', 'kode_laboratorium');
    }

    public function detailPinjam()
    {
        return $this->hasMany('App\DetailPeminjamanInventaris', 'kode_inventaris');
    }
}
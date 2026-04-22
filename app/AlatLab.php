<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AlatLab extends Model
{
	protected $table = 'alat_labs';
    protected $primaryKey = 'kode_alat';
    public $incrementing = false;
    protected $fillable = ['kode_alat','nama_alat', 'kode_sinta','harga', 'stok', 'kode_jenis_alat', 'kode_merek', 'kode_supplier'];
    public $timestamps = false;

    public function jenis()
    {
        return $this->belongsTo('App\JenisAlat', 'kode_jenis_alat');
    }

    public function merek()
    {
        return $this->belongsTo('App\Merek', 'kode_merek');
    }

    public function supplier()
    {
        return $this->belongsTo('App\Supplier', 'kode_supplier');
    }

    public function detailPinjam()
    {
        return $this->hasMany('App\DetailPeminjamanAlat', 'kode_alat');
    }

    public function detailKembali()
    {
        return $this->hasMany('App\DetailPengembalianAlat', 'kode_alat');
    }

    public function detailPembelianAlats()
    {
        return $this->hasMany('App\DetailPembelianAlat', 'kode_alat');
    }
}

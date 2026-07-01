<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupplierInventaris extends Model
{
    protected $table = 'supplier_inventaris';
    protected $primaryKey = 'kode_supplier';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = ['kode_supplier', 'nama_supplier', 'kontak_supplier'];

    public function inventaris()
    {
        return $this->hasMany('App\InventarisLab', 'kode_supplier');
    }
}
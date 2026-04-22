<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'supplier_alats';
    protected $primaryKey = 'kode_supplier';
    public $incrementing = false;
    protected $fillable = ['kode_supplier', 'nama_supplier', 'kontak_supplier'];
    public $timestamps = false;

    public function alats()
    {
        return $this->hasMany('App\AlatLab', 'kode_supplier');
    }
}

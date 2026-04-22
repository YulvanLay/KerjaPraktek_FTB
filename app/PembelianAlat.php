<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PembelianAlat extends Model
{
    protected $table = 'pembelian_alats';
    protected $primaryKey = 'no_PO';
    public $incrementing = false;
    protected $fillable = ['no_PO', 'no_TTB', 'tgl_TTB', 'kode_laboran'];
    public $timestamps = false;

    public function laboran()
    {
        return $this->belongsTo('App\Laboran', 'kode_laboran');
    }

    public function details()
    {
        return $this->hasMany('App\DetailPembelianAlat', 'no_PO');
    }
}

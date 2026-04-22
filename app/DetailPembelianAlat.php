<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetailPembelianAlat extends Model
{
    protected $table = 'detail_pembelian_alats';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'no_PO', 'kode_alat', 'jumlah'];
    public $timestamps = false;

    public function alat()
    {
        return $this->belongsTo('App\AlatLab', 'kode_alat');
    }

    public function penerimaanBahan()
    {
        return $this->belongsTo('App\PembelianAlat', 'no_PO');
    }
}

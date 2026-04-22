<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetailPenerimaanBahan extends Model
{
    protected $table = 'detail_penerimaan_bahans';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'no_PO', 'kode_bahan', 'jumlah'];
    public $timestamps = false;

    public function bahan()
    {
        return $this->belongsTo('App\BahanLab', 'kode_bahan');
    }

    public function penerimaanBahan()
    {
        return $this->belongsTo('App\PenerimaanBahan', 'no_PO');
    }
}

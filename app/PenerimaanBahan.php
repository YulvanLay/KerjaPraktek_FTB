<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PenerimaanBahan extends Model
{
    protected $table = 'penerimaan_bahans';
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
        return $this->hasMany('App\DetailPenerimaanBahan', 'no_PO');
    }
}

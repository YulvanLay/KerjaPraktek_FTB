<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Laboratorium extends Model
{
	protected $table = 'laboratoriums';
    protected $fillable = ['id', 'nama_laboratorium', 'kode_pejabat'];
    public $timestamps = false;

    public function laboran()
    {
        return $this->hasOne('App\Laboran', 'laboratorium');
    }

    public function pejabat()
    {
        return $this->belongsTo('App\Pejabat', 'kode_pejabat');
    }
}

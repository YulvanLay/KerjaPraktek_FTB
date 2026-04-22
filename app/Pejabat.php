<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pejabat extends Model
{
	protected $table = 'pejabat_strukturals';
    protected $primaryKey = 'kode_pejabat';
    public $incrementing = false;
    protected $fillable = ['kode_pejabat', 'nama_pejabat', 'jabatan'];
    public $timestamps = false;

    public function laborans()
    {
        return $this->hasMany('App\Laboran', 'kode_pejabat');
    }

    public function laboratoriums()
    {
        return $this->hasMany('App\Laboratorium', 'kode_pejabat');
    }

    public function koordinator()
    {
        return $this->hasOne('App\Koordinator', 'kode_pejabat');
    }
}

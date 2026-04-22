<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JenisAlat extends Model
{
    protected $table = 'jenis_alats';
    protected $primaryKey = 'kode_jenis_alat';
    public $incrementing = false;
    protected $fillable = ['kode_jenis_alat', 'jenis_alat'];
    public $timestamps = false;

    public function alats()
    {
        return $this->hasMany('App\AlatLab', 'kode_jenis_alat');
    }
}

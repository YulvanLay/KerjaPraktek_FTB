<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Keperluan extends Model
{
    // protected $table = 'keperluans';
    protected $primaryKey = 'kode_keperluan';
    public $incrementing = false;
    protected $fillable = ['kode_keperluan', 'nama_keperluan'];
    public $timestamps = false;

    public function peminjamanAlats()
    {
        return $this->hasMany('App\PeminjamanAlat', 'kode_keperluan');
    }

    public function pemakaianBahans()
    {
        return $this->hasMany('App\PemakaianBahan', 'kode_keperluan');
    }
}

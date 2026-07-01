<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Periode extends Model
{
    // protected $table = 'periodes';
    protected $primaryKey = 'id_periode';
    protected $fillable = ['id_periode', 'nama_periode'];
    public $timestamps = false;

    public function peminjamanAlats()
    {
        return $this->hasMany('App\PeminjamanAlat', 'periode_id');
    }

    public function pemakaianBahans()
    {
        return $this->hasMany('App\PemakaianBahan', 'periode_id');
    }

    public function peminjamanInventaris()
    {
        return $this->hasMany('App\PeminjamanInventaris', 'periode_id');
    }

    public function pemakaianFasilitas()
    {
        return $this->hasMany('App\PemakaianFasilitas', 'periode_id');
    }
}

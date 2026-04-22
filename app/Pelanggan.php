<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    protected $primaryKey = 'kode_pelanggan';
    public $incrementing = false;
    protected $fillable = ['kode_pelanggan', 'nama_pelanggan', 'users_id','email'];
    public $timestamps = false;

    public function peminjamanAlats()
    {
        return $this->hasMany('App\PeminjamanAlat', 'kode_pelanggan');
    }

    public function pemakaianBahans()
    {
        return $this->hasMany('App\PemakaianBahan', 'kode_pelanggan');
    }
}

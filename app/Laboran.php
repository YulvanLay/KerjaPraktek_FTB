<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Laboran extends Model
{
    protected $primaryKey = 'kode_laboran';
    public $incrementing = false;
    protected $fillable = ['kode_laboran', 'nama_laboran', 'email', 'user_id', 'laboratorium', 'kalab'];
    public $timestamps = false;

    public function bahans()
    {
        return $this->hasMany('App\Bahan', 'kode_laboran');
    }

    public function peminjamanAlats()
    {
        return $this->hasMany('App\PeminjamanAlat', 'kode_laboran');
    }

    public function pemakaianBahans()
    {
        return $this->hasMany('App\PemakaianBahan', 'kode_laboran');
    }

    public function pembelianAlats()
    {
        return $this->hasMany('App\PembelianAlat', 'kode_laboran');
    }

    public function penerimaanBahans()
    {
        return $this->hasMany('App\PenerimaanBahan', 'kode_laboran');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function lab()
    {
        return $this->belongsTo('App\Laboratorium', 'laboratorium');
    }

    public function pejabat()
    {
        return $this->belongsTo('App\Pejabat', 'kalab');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JenisBahan extends Model
{
    protected $table = 'jenis_bahans';
    protected $primaryKey = 'kode_jenis_bahan';
    public $incrementing = false;
    protected $fillable = ['kode_jenis_bahan', 'jenis_bahan'];
    public $timestamps = false;

    public function bahans()
    {
        return $this->hasMany('App\BahanLab', 'kode_jenis');
    }
}

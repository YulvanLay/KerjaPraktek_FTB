<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class merekBahan extends Model
{
    protected $table = 'merek_bahans';
    protected $primaryKey = 'kode_merek';
    public $incrementing = false;
    protected $fillable = ['kode_merek', 'nama_merek'];
    public $timestamps = false;

    public function bahans()
    {
        return $this->hasMany('App\BahanLab', 'kode_merek');
    }
}

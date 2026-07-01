<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SumberDana extends Model
{
    protected $table = 'sumber_dana';
    protected $primaryKey = 'kode_sumber_dana';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = ['kode_sumber_dana', 'nama_sumber_dana'];

    public function inventaris()
    {
        return $this->hasMany('App\InventarisLab', 'kode_sumber_dana');
    }
}
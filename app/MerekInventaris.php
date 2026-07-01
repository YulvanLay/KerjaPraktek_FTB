<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MerekInventaris extends Model
{
    protected $table = 'merek_inventaris';
    protected $primaryKey = 'kode_merek';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = ['kode_merek', 'nama_merek'];

    public function inventaris()
    {
        return $this->hasMany('App\InventarisLab', 'kode_merek');
    }
}
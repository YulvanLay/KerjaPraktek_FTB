<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Merek extends Model
{
    protected $table = 'merek_alats';
    protected $primaryKey = 'kode_merek';
    public $incrementing = false;
    protected $fillable = ['kode_merek', 'nama_merek'];
    public $timestamps = false;

    public function alats()
    {
        return $this->hasMany('App\AlatLab', 'kode_merek');
    }
}

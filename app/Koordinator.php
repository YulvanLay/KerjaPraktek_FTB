<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Koordinator extends Model
{
    protected $table = 'koordinators';
    protected $fillable = ['id', 'kode_pejabat'];
    public $timestamps = false;

    public function pejabat()
    {
        return $this->belongsTo('App\Pejabat', 'kode_pejabat');
    }
}

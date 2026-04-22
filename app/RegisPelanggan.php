<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RegisPelanggan extends Model
{
    protected $table='pelanggans';
    protected $primaryKey = 'kode_pelanggan';
    public $incrementing = false;
    protected $fillable = ['kode_pelanggan', 'nama_pelanggan', 'email'];
    public $timestamps = false;
}
